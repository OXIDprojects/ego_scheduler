<?php
/**
 * Metadata version
 */
$sMetadataVersion = '1.0';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'ego_scheduler',
    'title'       => 'EGO_IST Scheduler',
    'description' => array(
        'de' => 'Scheduler für die Verwaltung regelmäßiger Aufgaben im Shop.',
        'en' => 'Scheduler for management of periodical tasks in the shop.',
    ),
    'thumbnail'   => 'egoist-logo.png',
    'version'     => '0.1',
    'author'      => 'EGO_IST GmbH | Steve Schütze',
    'email'       => 'technik@egoist.de',
    'url'         => 'http://www.egoist.de',
    'extend'      => array(),
    'files'       => array(
        'ego_scheduler_init'    => 'EGOIST/ego_scheduler/core/ego_scheduler_init.php',
        'ego_schedulerlog_main' => 'EGOIST/ego_scheduler/application/controllers/admin/ego_schedulerlog_main.php',
        'ego_scheduler'         => 'EGOIST/ego_scheduler/application/controllers/admin/ego_scheduler.php',
        'sendServiceMail'       => 'EGOIST/ego_scheduler/tasks/sendServiceMail.php',
    ),
    'templates'   => array(
        'ego_schedulerlog_main.tpl' => 'EGOIST/ego_scheduler/application/views/admin/tpl/ego_schedulerlog_main.tpl',
        'ego_scheduler.tpl'         => 'EGOIST/ego_scheduler/application/views/admin/tpl/ego_scheduler.tpl'
    ),
    'events'      => array(
        'onActivate' => 'ego_scheduler_init::setup',
    ),
);
