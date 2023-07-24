<?php

/**
 * Service to manage the review reminder action from the reviewers component.
 */
class PPRReviewReminderService {

    const OJS_REVIEW_REMINDER_EMAIL_TEMPLATES = ['REVIEW_REMIND', 'REVIEW_REMIND_ONECLICK'];
    const TEMPLATE = 'PPR_REVIEW_REQUEST_DUE_DATE_REVIEWER';

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->reviewReminderEmailOverrideEnabled()) {
            HookRegistry::register('reviewreminderform::display', array($this, 'reviewReminderDisplay'));
            HookRegistry::register('Mail::send', array($this, 'reviewReminderEmailSend'));
        }
    }

    function reviewReminderDisplay($hookName, $arguments) {
        $reviewReminderForm = $arguments[0];
        $reviewAssignment = $reviewReminderForm->getReviewAssignment();
        if (!$reviewAssignment->getDateConfirmed()) {
            import('lib.pkp.classes.mail.MailTemplate');
            $email = new MailTemplate(self::TEMPLATE);
            // OVERRIDE EMAIL BODY TO DISPLAY IN THE FORM
            $reviewReminderForm->setData('message', $email->getBody());
        }

        return false;
    }

    function reviewReminderEmailSend($hookName, $arguments) {
        $emailTemplate = $arguments[0];
        if (isset($emailTemplate->emailKey) && in_array($emailTemplate->emailKey,self::OJS_REVIEW_REMINDER_EMAIL_TEMPLATES)) {
            //IT IS REVIEW REMINDER EMAIL => VERIFY IS FROM THE SEND REMINDER ACTION
            $request = Application::get()->getRequest();
            $router = $request->getRouter();
            $componentId = $router->getRequestedComponent($request);
            $operation = $router->getRequestedOp($request);
            if ($componentId === 'grid.users.reviewer.ReviewerGridHandler' && $operation === 'sendReminder') {
                //IS FROM THE SEND REMINDER ACTION => UPDATE
                $reviewAssignmentId = $request->getUserVar('reviewAssignmentId');
                $reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO');
                $reviewAssignment = $reviewAssignmentId ? $reviewAssignmentDao->getById($reviewAssignmentId) : null;
                if ($reviewAssignment && !$reviewAssignment->getDateConfirmed()) {
                    // SET REVIEW REQUEST REMINDER DATA
                    import('lib.pkp.classes.mail.MailTemplate');
                    $email = new MailTemplate(self::TEMPLATE);
                    $emailTemplate->setSubject($email->getSubject());
                }
            }

        }

        return false;
    }
}