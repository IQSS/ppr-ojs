<?php

/**
 * Service manage review reminder email notifications
 */
class PPRReviewReminderEmailService {

    const REVIEW_REMINDER_EMAIL_TEMPLATES = ['REVIEW_REQUEST_REMIND_AUTO', 'REVIEW_REQUEST_REMIND_AUTO_ONECLICK'];

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->reviewRequestReminderEmailDisabled()) {
            HookRegistry::register('Mail::send', array($this, 'stopReviewRequestReminderEmail'));
        }
    }

    function stopReviewRequestReminderEmail($hookName, $hookArgs) {
        $emailTemplate =& $hookArgs[0];
        if (isset($emailTemplate->emailKey) && in_array($emailTemplate->emailKey, self::REVIEW_REMINDER_EMAIL_TEMPLATES)) {
            error_log('PPR[PPRReviewReminderEmailService] action=stopReviewRequestReminderEmail result=success');
            return true;
        }

        return false;
    }
}