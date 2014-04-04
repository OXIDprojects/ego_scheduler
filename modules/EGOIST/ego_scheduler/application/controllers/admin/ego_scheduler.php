<?php

/**
 * Automically generated file.
 */
class ego_scheduler extends oxAdminView
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'ego_scheduler.tpl';

    const CONFIG_ENTRY_NAME = 'ego_scheduler_config';

    public function getTasks()
    {
        $sQuery = 'SELECT * FROM ego_scheduler_tasks';

        $oDb = oxDb::getDb();
        $oRes = $oDb->Execute($sQuery);
        $tasks = array();

        if ($oRes != false && $oRes->recordCount() > 0) {
            while (!$oRes->EOF) {
                $task = array();
                $task['id'] = $oRes->fields[0];
                $task['active'] = $oRes->fields[1];
                $task['path'] = $oRes->fields[2];
                $task['class'] = $oRes->fields[3];
                $task['description'] = $oRes->fields[4];
                $task['timeinterval'] = $oRes->fields[5];
                $task['lastrun'] = $oRes->fields[6];
                $task['log'] = $this->_getLastLog($task['id']);
                $tasks[] = $task;
                $oRes->moveNext();
            }
        }
        return $tasks;
    }

    protected function getTask($id)
    {
        $sQuery = "
            SELECT
                *
            FROM
                ego_scheduler_tasks
            WHERE
                id='" . $id . "'
        ";

        $oDb = oxDb::getDb(false);
        $oRes = $oDb->execute($sQuery);
        $task = array();

        if ($oRes != false && $oRes->recordCount() > 0) {
            $task['id'] = $oRes->fields[0];
            $task['active'] = $oRes->fields[1];
            $task['path'] = $oRes->fields[2];
            $task['class'] = $oRes->fields[3];
            $task['description'] = $oRes->fields[4];
            $task['timeinterval'] = $oRes->fields[5];
            $task['lastrun'] = $oRes->fields[6];
            $task['log'] = $this->_getLastLog($task['id']);
        }
        return $task;
    }

    /**
     * Saves the Tasks
     *
     * @return void
     */
    public function save()
    {
        $oDb = oxDb::getDb();
        $aParams = oxConfig::getRequestParameter("editval");
        foreach ($aParams as $key => $task) {
            if (!is_int($key)) {
                break;
            }
            $sQuery = "UPDATE ego_scheduler_tasks SET class ='" . $task['class']
                . "',path='" . $task['path']
                . "',description='" . $task['description']
                . "',active='" . $task['active']
                . "',timeinterval='" . $task['timeinterval']
                . "' WHERE id=" . $key;
            $oDb->Execute($sQuery);
        }
        if (!empty($aParams['new']['path'])) {
            $task = $aParams['new'];

            $sQuery =
                "INSERT INTO ego_scheduler_tasks (class, path, description, active, timeinterval) VALUES ('"
                . $task['class']
                . "','" . $task['path']
                . "','" . $task['description']
                . "','" . $task['active']
                . "','" . $task['timeinterval']
                . "')";
            $oDb->Execute($sQuery);
        }

    }

    /**
     * Saves the Tasks
     *
     * @return void
     */
    public function trigger()
    {
        $taskId = (int)oxConfig::getRequestParameter("trigger");
        $ret = array();

        if ($taskId && is_integer($taskId)) {
            $task = $this->getTask($taskId);
            if (is_array($task) && count($task) > 0) {
                try {
                    if ($task['path']) {
                        if (file_exists(getShopBasePath() . $task['path'])) {
                            $start = time();
                            include getShopBasePath() . $task['path'];
                            $ret['runtime'] = time() - $start;
                        }
                    }
                    if (!empty($task['class'])) {
                        $class = oxNew($task['class']);
                        if (method_exists($class, 'run')) {
                            $start = time();
                            $ret = $class->run();
                            $ret['runtime'] = time() - $start;
                        } else {
                            throw new Exception('function run does not exist');
                        }
                    }
                } catch (Exception $e) {
                    $message = $e->getMessage();
                    echo $message;
                }
            }
        }

        if (count($ret) == 0) {
            $ret['success'] =  false;
            $ret['message'] =  'Task konnte nicht ausgeführt werden';
        }
        $this->_aViewData["output"] = $ret;

    }

    public function isSchedulerRunning()
    {
        $config = oxRegistry::getConfig()->getShopConfVar(self::CONFIG_ENTRY_NAME);
        return $config['locked'];
    }

    public function unlockScheduler()
    {
        $config = oxRegistry::getConfig()->getShopConfVar(self::CONFIG_ENTRY_NAME);
        if ($config['locked'] > 0) {
            $config['locked'] = 0;
            oxRegistry::getConfig()->saveShopConfVar('aarr', self::CONFIG_ENTRY_NAME, $config);
        }
    }

    private function _getLastLog($id)
    {
        $oDb = oxDb::getDb(false);
        $sQuery = 'SELECT * FROM ego_scheduler_log WHERE taskid =' . $id . ' ORDER BY id DESC LIMIT 1';
        $oRes = $oDb->Execute($sQuery);
        if ($oRes != false && $oRes->recordCount() > 0) {
            while (!$oRes->EOF) {
                $log = array();
                $log['status'] = $oRes->fields['status'];
                $log['message'] = $oRes->fields['message'];
                $log['time'] = date('Y-m-d H:i:s', $oRes->fields['time']);
                $log['runtime'] = $oRes->fields['runtime'];
                break;
            }
            return $log;
        }
        return null;
    }
}
