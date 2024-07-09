<?php

/**
 * Service to add author and editor first name to all SubmissionMailTemplates and forms
 *
 * Issue 065, Issue 127
 */
class PPRFirstNameEmailService {
    const SUPPORTED_EMAILS =
        [
            //BATCH 1
            'REVIEW_REMIND',
            'REVIEW_REMIND_ONECLICK',
            'PPR_REVIEW_REQUEST_DUE_DATE_REVIEWER',
            'PPR_REVIEW_DUE_DATE_EDITOR',
            'PPR_REVIEW_DUE_DATE_WITH_FILES_REVIEWER'.
            'PPR_REVIEW_DUE_DATE_REVIEWER',
            'PPR_REVIEW_PENDING_WITH_FILES_REVIEWER',
            //BATCH 2
            'PPR_REVIEW_ACCEPTED',
            'PPR_REVIEW_SUBMITTED',
            'PPR_SUBMISSION_APPROVED',
            'PPR_REVIEW_SENT_AUTHOR',
            'PPR_SUBMISSION_CLOSED_AUTHOR',
            //BATCH 3
            'REVIEW_REQUEST' ,
            'REVIEW_REQUEST_SUBSEQUENT',
            'REVIEW_REQUEST_ONECLICK',
            'REVIEW_REQUEST_ONECLICK_SUBSEQUENT',
            'REVIEW_CONFIRM',
            'REVIEW_CANCEL',
            'REVIEW_DECLINE',
            'REVIEW_ACK',
            //BATCH 4
            'SUBMISSION_ACK',
            'SUBMISSION_ACK_NOT_USER',
            'EDITOR_DECISION_ACCEPT',
            'EDITOR_DECISION_REVISIONS',
            'EDITOR_DECISION_INITIAL_DECLINE',
            'EDITOR_DECISION_DECLINE',
            'EDITOR_ASSIGN',
            'EDITOR_DECISION_SEND_TO_EXTERNAL',
            'EDITOR_DECISION_RESUBMIT',
        ];

    const SUPPORTED_TEMPLATES =
        [
            'reviewer/review/modal/regretMessage.tpl' => 'declineMessageBody',
        ];

    private $pprPlugin;
    private $pprObjectFactory;

    public function __construct($plugin, $pprObjectFactory = null) {
        $this->pprPlugin = $plugin;
        $this->pprObjectFactory = $pprObjectFactory ?: new PPRObjectFactory();
        $this->pprPlugin->import('util.PPRMissingUser');
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->firstNameEmailEnabled()) {
            HookRegistry::register('Mail::send', array($this, 'addFirstNamesToEmailTemplate'));
            HookRegistry::register('reviewreminderform::display', array($this, 'addFirstNamesToReviewReminderForm'));
            HookRegistry::register('thankreviewerform::display', array($this, 'addFirstNamesToThankReviewerForm'));
            HookRegistry::register('sendreviewsform::display', array($this, 'addFirstNamesToSendReviewsForm'));
            HookRegistry::register('TemplateManager::fetch', array($this, 'replaceFirstNameInTemplateText'));

            HookRegistry::register('advancedsearchreviewerform::display', array($this, 'addFirstNameLabelsToAdvancedSearchReviewerForm'));

            HookRegistry::register('LoadComponentHandler', array($this, 'addPPRStageParticipantGridHandler'));
        }
    }

    /**
     * This new handler will replace the author, editor first names in the email body of the form
     * when using the assign participant feature in a submission.
     */
    function addPPRStageParticipantGridHandler($hookName, $hookArgs) {
        $component =& $hookArgs[0];
        $method = $hookArgs[1];
        if ($component === 'grid.users.stageParticipant.StageParticipantGridHandler' && $method === 'fetchTemplateBody') {
            $emailTemplate = Application::get()->getRequest()->getUserVar('template');
            if ($this->isEmailSupported($emailTemplate)) {
                // LOAD THE PPR STAGE PARTICIPANTS HANDLER FROM THE PLUGIN REPO
                $component =str_replace('/', '.', $this->pprPlugin->getPluginPath()) . '.services.email.PPRStageParticipantGridHandler';
                return true;
            }
        }

        return false;
    }

    function addFirstNamesToEmailTemplate($hookName, $arguments) {
        $emailTemplate = $arguments[0];
        if ($emailTemplate instanceof SubmissionMailTemplate && $this->isEmailSupported($emailTemplate->emailKey)) {
            error_log("PPR[PPRFirstNameEmailService] processing emailTemplate={$emailTemplate->emailKey}");
            $this->pprObjectFactory->firstNamesManagementService()->addFirstNamesToEmailTemplate($emailTemplate);
        } else {
            $emailKey = $emailTemplate->emailKey ?? 'N/A';
            error_log("PPR[PPRFirstNameEmailService] notSupported emailTemplate={$emailKey}");
        }

        return false;
    }

    function addFirstNameLabelsToAdvancedSearchReviewerForm($hookName, $arguments) {
        $this->pprObjectFactory->firstNamesManagementService()->addFirstNameLabelsToTemplate('emailVariables');

        return false;
    }

    function addFirstNamesToThankReviewerForm($hookName, $arguments) {
        $thankReviewerForm = $arguments[0];
        $review = $thankReviewerForm->getReviewAssignment();
        $reviewerId = $thankReviewerForm->getReviewAssignment()->getReviewerId();
        $submission = $this->pprObjectFactory->submissionUtil()->getSubmission($review->getSubmissionId());
        $emailBodyText = $thankReviewerForm->getData('message');
        $emailBodyTextUpdated = $this->pprObjectFactory->firstNamesManagementService()->replaceFirstNames($emailBodyText, $submission, $reviewerId);
        $thankReviewerForm->setData('message',  $emailBodyTextUpdated);

        return false;
    }

    function addFirstNamesToReviewReminderForm($hookName, $arguments) {
        $reviewReminderForm = $arguments[0];
        $review = $reviewReminderForm->getReviewAssignment();
        $reviewerId = $reviewReminderForm->getReviewAssignment()->getReviewerId();
        $submission = $this->pprObjectFactory->submissionUtil()->getSubmission($review->getSubmissionId());
        $emailBodyText = $reviewReminderForm->getData('message');
        $emailBodyTextUpdated = $this->pprObjectFactory->firstNamesManagementService()->replaceFirstNames($emailBodyText, $submission, $reviewerId);
        $reviewReminderForm->setData('message',  $emailBodyTextUpdated);
        
        return false;
    }

    /**
     * Add first names to the email text in the form for request revisions and decline submission actions
     */
    function addFirstNamesToSendReviewsForm($hookName, $arguments) {
        $sendReviewForm = $arguments[0];
        $submission = $sendReviewForm->getSubmission();
        $emailBodyText = $sendReviewForm->getData('personalMessage');
        $emailBodyTextUpdated = $this->pprObjectFactory->firstNamesManagementService()->replaceFirstNames($emailBodyText, $submission);
        $sendReviewForm->setData('personalMessage',  $emailBodyTextUpdated);

        return false;
    }

    function replaceFirstNameInTemplateText($hookName, $arguments) {
        $templateName = $arguments[1];
        if ($this->isTemplateSupported($templateName)) {
            $templateMgr = $arguments[0];
            $emailBodyVariableName = self::SUPPORTED_TEMPLATES[$templateName];
            $emailBodyText = $templateMgr->getTemplateVars($emailBodyVariableName);
            $submission = Application::get()->getRequest()->getRouter()->getHandler()->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION);
            $emailBodyTextUpdated = $this->pprObjectFactory->firstNamesManagementService()->replaceFirstNames($emailBodyText, $submission);
            $templateMgr->assign($emailBodyVariableName, $emailBodyTextUpdated);
        }

        return false;
    }

    public function isEmailSupported($emailTemplateName) {
        return in_array($emailTemplateName,self::SUPPORTED_EMAILS);
    }

    public function isTemplateSupported($template) {
        return in_array($template, array_keys(self::SUPPORTED_TEMPLATES));
    }
}