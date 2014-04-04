<?php

if (php_sapi_name() == 'cli') {
    ini_set('error_reporting', E_ERROR);
    ini_set('log_errors', false);
    ini_set('display_errors', true);

    /**
     * Bootstrapping
     * Start the framework
     */
    try {
        require_once __DIR__ . '/../../../bootstrap.php';
        require_once __DIR__ . '/lib/Cron/CronExpression.php';
    } catch (Exception $e) {
        echo 'Caught exception: ' . $e->getMessage() . PHP_EOL;
    }

    /**
     * Start the scheduler
     */
    $scheduler = Scheduler::getInstance();
    $scheduler->run();
}

/**
 * Start the scheduler
 */
$scheduler = Scheduler::getInstance();
$scheduler->run();

/**
 * Description of scheduler
 */
final class Scheduler
{

    const CONFIG_ENTRY_NAME = 'ego_scheduler_config';

    private static $_instance = null;

    /**
     * trigger interval des cronjobs
     *
     * in minutes
     *
     * @var integer
     */
    private $triggerMinuteInterval = 5;

    /**
     * the serial scheduler number
     *
     * @var integer
     */
    private $continuous = 0;

    protected $_blLocked = 0;
    protected $_oDb = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function run()
    {
        $ret = null;
        //check if scheduler is still running
        $config = oxRegistry::getConfig()->getShopConfVar(self::CONFIG_ENTRY_NAME);
        if ($config['locked']) {
            echo PHP_EOL . "###START###" . date('Y-m-d H:i:s') . " start: " . PHP_EOL;
            echo 'scheduler locked';
            $config['count']++;

            if ($config['count'] <= 5) {
                $this->_sendMail();
                oxRegistry::getConfig()->saveShopConfVar('aarr', self::CONFIG_ENTRY_NAME, $config);
            }
            return false;
        } else {
            $config['count'] = 0;
        }

        //lock scheduler
        $config['locked'] = 1;
        oxRegistry::getConfig()->saveShopConfVar('aarr', self::CONFIG_ENTRY_NAME, $config);

        $tasks = $this->_getTasks();
        $deactivatedCountBefore = $this->_getActiveTasks();
        foreach ($tasks as $task) {
            /**
             * Starting output for logfiles
             */
            echo PHP_EOL . "###START###" . date('Y-m-d H:i:s') . " start: " . PHP_EOL;

            echo PHP_EOL . "##" . $task['description'] . "##";
            try {
                if ($task['path']) {
                    if (file_exists(getShopBasePath() . $task['path'])) {
                        $this->_logTask($task, null, true);
                        $start = time();
                        include getShopBasePath() . $task['path'];
                        $ret['runtime'] = time() - $start;
                        $this->_logTask($task, $ret);
                    }
                }
                if (!empty($task['class'])) {
                    $class = oxNew($task['class']);
                    if (method_exists($class, 'run')) {
                        $this->_logTask($task, null, true);
                        $start = time();
                        $ret = $class->run();
                        $ret['runtime'] = time() - $start;
                        $this->_logTask($task, $ret);
                    } else {
                        throw new Exception('function run does not exist');
                    }
                }
            } catch (Exception $e) {
                $message = $e->getMessage();
                echo $message;
                $this->_deactivateTask($task['id']);
            }

            /**
             * Starting output for logfiles
             */
            echo PHP_EOL . "###END###: " . date('Y-m-d H:i:s') . PHP_EOL;
        }
        $deactivatedCountAfter = $this->_getActiveTasks();

        if (count($deactivatedCountBefore) != count($deactivatedCountAfter)) {
            $this->_sendMail(array_diff_assoc($deactivatedCountBefore, $deactivatedCountAfter));
        }

        //unlock scheduler there was a success
        if ($ret != null && array_key_exists('success', $ret) && $ret['success']) {
            $config['locked'] = 0;
            oxRegistry::getConfig()->saveShopConfVar('aarr', self::CONFIG_ENTRY_NAME, $config);
        }

    }

    private function _getTasks()
    {
        $sQuery = "
            SELECT
              *
            FROM
              ego_scheduler_tasks
            WHERE
              active = 1
        ";

        $this->_oDb = oxDb::getDb(false);
        $oRes = $this->_oDb->execute($sQuery);
        $tasks = array();
        if ($oRes != false && $oRes->recordCount() > 0) {
            while (!$oRes->EOF) {
                if ($oRes->fields['timeinterval'] == '' || $this->checkStartTime($oRes->fields['timeinterval'])) {
                    $task = array();
                    $task['id'] = $oRes->fields['id'];
                    $task['class'] = $oRes->fields['class'];
                    $task['path'] = $oRes->fields['path'];
                    $task['description'] = $oRes->fields['description'];
                    $tasks[] = $task;
                }
                $oRes->moveNext();
            }
        }
        return $tasks;
    }

    private function _getActiveTasks()
    {
        $sQuery = 'SELECT * FROM ego_scheduler_tasks WHERE active = 1';
        $this->_oDb = oxDb::getDb();
        $oRes = $this->_oDb->Execute($sQuery);
        $tasks = array();
        $i = 0;
        if ($oRes != false && $oRes->recordCount() > 0) {
            while (!$oRes->EOF) {
                $task = array();
                $task['id'] = $oRes->fields[0];
                $task['class'] = $oRes->fields[3];
                $task['path'] = $oRes->fields[2];
                $task['description'] = $oRes->fields[4];
                $tasks[md5(implode(',', $task))] = $task;
                $i++;
                $oRes->moveNext();
            }
        }
        return $tasks;
    }

    private function _logTask($task, $array = null, $blStart = false)
    {
        $now = time();
        if ($blStart) {
            $sQuery = 'INSERT INTO ego_scheduler_log (continuous, taskid,class,status,message,time)'
                . ' VALUES (\'' . $this->continuous
                . '\',\'' . $task['id']
                . '\',\'' . $task['class']
                . '\',\'2'
                . '\',\'starting'
                . '\',\'' . $now
                . '\')';
        } else {
            $sQuery = 'INSERT INTO ego_scheduler_log (continuous, taskid,class,status,message,time,runtime)'
                . ' VALUES (\'' . $this->continuous
                . '\',\'' . $task['id']
                . '\',\'' . $task['class']
                . '\',\'' . $array['success']
                . '\',\'' . $array['message']
                . '\',\'' . $now
                . '\',\'' . $array['runtime']
                . '\')';
        }
        $this->_oDb->Execute($sQuery);
        if (!$blStart) {
            $sQuery = 'UPDATE ego_scheduler_tasks SET lastrun =' . $now . ' WHERE id =' . $task['id'];
            $this->_oDb->Execute($sQuery);
        }
    }

    private function _deactivateTask($id)
    {
        $sQuery = 'UPDATE ego_scheduler_tasks SET active = 0 WHERE id =' . $id;
        $this->_oDb->Execute($sQuery);
    }

    private function _sendMail($tasks = null)
    {
        /** @var sendServiceMail $sendServiceMail */
        $sendServiceMail = oxNew('sendServiceMail');
        $sendMailSuccess = $sendServiceMail->run($tasks);

        if (!$sendMailSuccess['success'])
            throw new Exception('no mail-notification possible');
    }

    /**
     * check for timeinterval of scheduler task
     *
     * @param string $timeinterval
     *
     * @return bool
     */
    private function checkStartTime($timeinterval) {

        // Works with complex expressions
        $cron = Cron\CronExpression::factory($timeinterval);
        $nextRunDate = $cron->getNextRunDate();

        $return = false;

        if ($nextRunDate->format('U') - time() <= (($this->triggerMinuteInterval * 60) - 1)) {
            $return = true;
        }

        if ($this->triggerMinuteInterval == 0) {
            $return = true;
        }

        return $return;
    }
}
