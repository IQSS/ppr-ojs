<?php

/**
 * Service to add author and editor first name to all SubmissionMailTemplates
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
            //BATCH 3
            'REVIEW_REQUEST' ,
            'REVIEW_REQUEST_SUBSEQUENT',
            'REVIEW_REQUEST_ONECLICK',
            'REVIEW_REQUEST_ONECLICK_SUBSEQUENT',
            'REVIEW_CONFIRM',
            'REVIEW_CANCEL',
            'REVIEW_DECLINE',
            'REVIEW_ACK',
        ];

    const SUPPORTED_TEMPLATES =
        [
            'reviewer/review/modal/regretMessage.tpl' => 'declineMessageBody',
        ];

    private $pprPlugin;
    private $pprObjectFactory;

    function __construct($plugin, $pprObjectFactory = null) {
        $this->pprPlugin = $plugin;
        $this->pprObjectFactory = $pprObjectFactory ?: new PPRObjectFactory();
        $this->pprPlugin->import('util.PPRMissingUser');
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->firstNameEmailEnabled()) {
            HookRegistry::register('Mail::send', array($this, 'addFirstNamesToEmailTemplate'));
            HookRegistry::register('reviewreminderform::display', array($this, 'addFirstNamesToReviewReminderForm'));
            HookRegistry::register('thankreviewerform::display', array($this, 'addFirstNamesToThankReviewerForm'));
            HookRegistry::register('TemplateManager::fetch', array($this, 'replaceFirstNameInTemplateText'));

            HookRegistry::register('advancedsearchreviewerform::display', array($this, 'addFirstNameLabelsToAdvancedSearchReviewerForm'));
        }
    }

    function addFirstNamesToEmailTemplate($hookName, $arguments) {
        $emailTemplate = $arguments[0];
        if ($this->isEmailSupported($emailTemplate)) {
            $this->pprObjectFactory->firstNamesManagementService()->addFirstNamesToEmailTemplate($emailTemplate);
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

    public function isEmailSupported($email) {
        return $email instanceof SubmissionMailTemplate && in_array($email->emailKey,self::SUPPORTED_EMAILS);
    }

    public function isTemplateSupported($template) {
        return in_array($template, array_keys(self::SUPPORTED_TEMPLATES));
    }
}