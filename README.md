ego_scheduler
======================

manage all cronjobs of your OXID-shop from the backend

view log from the backend

email reminder, if scheduler is locked and something went wrong

use cron-expressions to set up your jobs in the backend (https://github.com/mtdowling/cron-expression)

Install
------------------

1. copy module into your OXID module folder
2. set $triggerMinuteInterval in the scheduler.php to your cronjob-frequency
3. change email-recipient for notifications in modules/EGOIST/ego_scheduler/tasks/sendServiceMail.php
4. activate the module
5. add a log-file with the OXID standard filepermissions and the name ego_scheduler.log
6. add a cron job like php /PATH/TO/WEBROOT/modules/EGOIST/ego_scheduler/scheduler.php >> /PATH/TO/WEBROOT/log/ego_scheduler.log
