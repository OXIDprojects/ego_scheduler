<?php

class ego_scheduler_init extends oxConfig
{

    public static function setup()
    {
        $oDb = oxDb::getDb();

        $sInsertTableScheduler
            = "
            CREATE TABLE IF NOT EXISTS `ego_scheduler_tasks` (
                 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                 `active` TINYINT(1) NOT NULL,
                 `path` VARCHAR(255) NOT NULL,
                 `class` VARCHAR(255) NOT NULL,
                 `description` VARCHAR(255) NOT NULL,
                 `timeinterval` VARCHAR(255) NOT NULL DEFAULT '',
                 `lastrun` INT NOT NULL
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table with all scheduler tasks.';
        ";
        $oDb->execute($sInsertTableScheduler);

        $sInsertTableSchedulerLog
            = "
            CREATE TABLE IF NOT EXISTS `ego_scheduler_log` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `taskid` int(11) NOT NULL,
                `class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `status` tinyint(1) NOT NULL,
                `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `time` int(11) NOT NULL,
                `runtime` int(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2;
        ";
        $oDb->execute($sInsertTableSchedulerLog);
    }
}
