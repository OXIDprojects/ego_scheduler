<?php

/**
 * Class to demonstrate the scheduler and to send notifications if there are some more deactivated tasks
 */
class sendServiceMail extends oxAdminView
{
    /**
     * You'll always need a public function run,
     * as this is the method we will call.
     */
    public function run($tasks = null)
    {
        $success = true;
        /** @var oxEmail $oMail */
        $oMail = oxNew('oxEmail');
        $oShop = oxNew('oxShop');
        $oShopId = oxRegistry::getConfig()->getShopId();
        $oShop->load($oShopId);
        // build mail object
        $oMail->setSmtp($oShop);

        $oMail->setFrom($oShop->oxshops__oxinfoemail->value);
        $oMail->setRecipient('steve_schuetze@egoist.de');

        $oMail->setSubject('Scheduler E-Mail-Notification');

        $taskOutput = '';
        if(is_array($tasks) && count($tasks) > 0) {
            $i = 1;
            foreach ($tasks as $task) {
                $taskOutput .= $task['description'];
                if ($i < count($tasks))
                    $taskOutput .= ', ';
                $i++;
            }
        }

        if (strlen($taskOutput) > 0) {
            $body = "There are some new errors in the scheduler.\nthe following tasks are affected: " . $taskOutput;
        } else {
            $body = "There are some new errors in the scheduler.";
        }

        $oMail->setBody($body);
        $oMail->setAltBody($body);

        if (!$oMail->send()) {
            $success = false;
        }

        $ret['success'] = $success;

        $ret['message'] = 'Everything went fine! Mails were send.';
        if (!$success) {
            $ret['message'] = 'Mail could not be send.';
        }

        return $ret;
    }
}
