<?php

class ego_schedulerlog_main extends oxAdminView
{

    /**
     *
     * @var string
     */
    protected $_sThisTemplate = 'ego_schedulerlog_main.tpl';

    /**
     *
     * @var string
     */
    protected $_fileName = 'ego_scheduler.log';

    public function getExceptionsFromLogfile()
    {

        $sContent = $this->_readExceptionFile();

        $aExceptions = explode("\n###START###", $sContent);

        $aExceptionList = array();

        foreach ($aExceptions as $sException) {
            if (strlen($sException) > 0) {
                $aExceptionList[] = $this->_prepareException($sException);
            }
        }

        return $aExceptionList;
    }

    /**
     * Reads the content from the Logfile
     *
     * @return string
     */
    protected function _readExceptionFile()
    {
        $sLogFilePath = oxRegistry::getConfig()->getLogsDir() . $this->_fileName;
        if (file_exists($sLogFilePath)) {
            $sContent = file_get_contents($sLogFilePath);
        }
        return $sContent;
    }

    protected function _prepareException($sException)
    {
        preg_match("/##.*##/", $sException, $title);

        preg_match("!(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})!", $sException, $datetime);

        if (is_array($title) && count($title) > 0) {
            $title = str_replace('##', '', $title[0]);
        } else {
            $title = 'scheduler locked';
        }

        return array('type' => $title, 'msg' => $sException, 'datetime' => $datetime[0]);
    }

    /**
     * archive logfile
     *
     * @return array
     */
    public function archiveLogFile()
    {
        $sLogFilePath = oxRegistry::getConfig()->getLogsDir() . $this->_fileName;
        if (copy($sLogFilePath, oxRegistry::getConfig()->getLogsDir() . date('Ymd') . $this->_fileName)) {
            $offen = @fopen($sLogFilePath, "w") or die("Wrong file permissions");
            fclose($offen);
            oxRegistry::get("oxUtilsView")->addErrorToDisplay('ego_schedulerlog_ARCHIVE_OK');
        } else {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay('ego_schedulerlog_ARCHIVE_ERROR');
        }
    }
}
