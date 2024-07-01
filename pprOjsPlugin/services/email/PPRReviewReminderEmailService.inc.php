<?php

/**
 * Service to manage the review reminder action from the reviewers component.
 * This is the component in the front end used to send notifications to reviewers.
 */
class PPRReviewReminderEmailService {

    const OJS_TEMPLATES_TO_OVERRIDE = ['REVIEW_REMIND', 'REVIEW_REMIND_ONECLICK'];
    const TEMPLATE = 'PPR_REVIEW_REQUEST_DUE_DATE_REVIEWER';

    private $pprPlugin;
    private $pprObjectFactory;

    public function __construct($plugin, $pprObjectFactory = null) {
        $this->pprPlugin = $plugin;
        $this->pprObjectFactory = $pprObjectFactory ?: new PPRObjectFactory();
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

        // ADD name and firstName AS A LABELS FOR REVIEWER, AUTHOR, AND EDITOR IN THE EMAIL BODY IN THE FORM
        $this->pprObjectFactory->firstNamesManagementService()->addFirstNameLabelsToTemplate('emailVariables');

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
        if (isset($emailTemplate->emailKey) && in_array($emailTemplate->emailKey,self::OJS_TEMPLATES_TO_OVERRIDE)) {
            //IT IS REVIEW REMINDER EMAIL => VERIFY IS FROM THE SEND REMINDER ACTION
            $request = Application::get()->getRequest();
            $router = $request->getRouter();
            $componentId = $router->getRequestedComponent($request);
            $operation = $router->getRequestedOp($request);

            $reviewAssignmentId = $request->getUserVar('reviewAssignmentId');
            $reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO');
            $reviewAssignment = $reviewAssignmentId ? $reviewAssignmentDao->getById($reviewAssignmentId) : null;

            if (!$reviewAssignment) {
                // NO REVIEW ASSIGMENT => NOTHING TO DO
                // THIS IS NOT EXPECTED. LOG INFORMATION
                error_log("PPR[PPRReviewReminderEmailService] componentId={$componentId} operation={$operation} no review assigment");
                return false;
            }

            // ADD REVIEWER FIRST NAME IN ALL CASES
            $userDao = DAORegistry::getDAO('UserDAO');
            $reviewerId = $reviewAssignment->getReviewerId();
            $reviewer = $userDao->getById($reviewerId);
            // SETTING PRIVATE PARAMS IN THE EMAIL TEMPLATE WILL GET REPLACED IN THE BODY AFTER THIS HOOK COMPLETES
            $emailTemplate->privateParams['{$reviewerFirstName}'] = htmlspecialchars($reviewer->getLocalizedGivenName());

            if ($componentId === 'grid.users.reviewer.ReviewerGridHandler' && $operation === 'sendReminder') {
                //IS FROM THE SEND REMINDER ACTION => UPDATE SUBJECT.
                // BODY ALREADY UPDATED IN THE FORM DISPLAY METHOD
                if (!$reviewAssignment->getDateConfirmed()) {
                    // SET REVIEW REQUEST REMINDER DATA
                    import('lib.pkp.classes.mail.MailTemplate');
                    $email = new MailTemplate(self::TEMPLATE);
                    $emailTemplate->setSubject($email->getSubject());
                }

                $newSubject = str_replace('{$submissionTitle}', $emailTemplate->params['submissionTitle'], $emailTemplate->getSubject());
                $emailTemplate->setSubject($newSubject);
            }

        }

        return false;
    }
}