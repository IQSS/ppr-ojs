<?php

class PPRPluginSettings {

    const CONFIG_VARS = array(
        // PROPERTY NAME => [TYPE, DEFAULT VALUE]
        //DEFAULT TO TRUE AS NAME HAS CHANGED. TODO: RESET IN NEXT RELEASE
        'displayWorkflowMessageEnabled' => ['bool', true],
        'displayContributorsEnabled' => ['bool', null],
        'displaySuggestedReviewersEnabled' => ['bool', null],
        'hideReviewMethodEnabled' => ['bool', null],
        'hideReviewFormDefaultEnabled' => ['bool', null],
        'hideReviewRecommendationEnabled' => ['bool', null],
        'hidePreferredPublicNameEnabled' => ['bool', null],
        'userOnLeaveEnabled' => ['bool', null],
        'userCustomFieldsEnabled' => ['bool', null],
        'categoryOptions' => ['string', 'Faculty, Fellow (Post-Doc), Grad Student, Staff, Student'],
        'institutionOptions' => ['string', 'Harvard University, Washington University in St. Louis'],
        'submissionCustomFieldsEnabled' => ['bool', null],
        'submissionCloseEnabled' => ['bool', null],
        'submissionConfirmationChecklistEnabled' => ['bool', null],
        'submissionUploadFileValidationEnabled' => ['bool', null],
        'submissionRequestRevisionsFileValidationEnabled' => ['bool', null],
        //DEFAULT TO TRUE AS NAME HAS CHANGED. TODO: RESET IN NEXT RELEASE
        'reviewReminderEditorTaskEnabled' => ['bool', true],
        'reviewReminderEditorDaysFromDueDate' => ['string', null],
        //DEFAULT TO TRUE AS NAME HAS CHANGED. TODO: RESET IN NEXT RELEASE
        'reviewReminderReviewerTaskEnabled' => ['bool', true],
        'reviewReminderReviewerDaysFromDueDate' => ['int', null],
        'reviewReminderEmailOverrideEnabled' => ['bool', null],
        'reviewUploadFileValidationEnabled' => ['bool', null],
        'reviewerRegistrationEmailDisabled' => ['bool', null],
    );

    private $contextId;
    private $pprPlugin;

    public function __construct($contextId, $pprPlugin) {
        $this->contextId = $contextId;
        $this->pprPlugin = $pprPlugin;
    }

    public function getContextId() {
        return $this->contextId;
    }

    public function displayWorkflowMessageEnabled() {
        return $this->getValue('displayWorkflowMessageEnabled');
    }

    public function displayContributorsEnabled() {
        return $this->getValue('displayContributorsEnabled');
    }

    public function displaySuggestedReviewersEnabled() {
        return $this->getValue('displaySuggestedReviewersEnabled');
    }

    public function hideReviewMethodEnabled() {
        return $this->getValue('hideReviewMethodEnabled');
    }

    public function hideReviewFormDefaultEnabled() {
        return $this->getValue('hideReviewFormDefaultEnabled');
    }

    public function hideReviewRecommendationEnabled() {
        return $this->getValue('hideReviewRecommendationEnabled');
    }

    public function hidePreferredPublicNameEnabled() {
        return $this->getValue('hidePreferredPublicNameEnabled');
    }

    public function userOnLeaveEnabled() {
        return $this->getValue('userOnLeaveEnabled');
    }

    public function userCustomFieldsEnabled() {
        return $this->getValue('userCustomFieldsEnabled');
    }

    public function getCategoryOptions() {
        $categoriesString =  $this->getValue('categoryOptions');
        $categoryOptions = array_filter(array_map('trim', explode(',', $categoriesString)));
        return array_combine($categoryOptions, $categoryOptions);
    }

    public function getInstitutionOptions() {
        $institutionString =  $this->getValue('institutionOptions');
        $institutionOptions = array_filter(array_map('trim', explode(',', $institutionString)));
        return array_combine($institutionOptions, $institutionOptions);
    }

    public function submissionCustomFieldsEnabled() {
        return $this->getValue('submissionCustomFieldsEnabled');
    }

    public function submissionCloseEnabled() {
        return $this->getValue('submissionCloseEnabled');
    }

    public function submissionConfirmationChecklistEnabled() {
        return $this->getValue('submissionConfirmationChecklistEnabled');
    }

    public function submissionUploadFileValidationEnabled() {
        return $this->getValue('submissionUploadFileValidationEnabled');
    }

    public function submissionRequestRevisionsFileValidationEnabled() {
        return $this->getValue('submissionRequestRevisionsFileValidationEnabled');
    }

    public function reviewReminderEditorTaskEnabled() {
        return $this->getValue('reviewReminderEditorTaskEnabled');
    }

    public function getReviewReminderEditorDaysFromDueDate() {
        $reminderDaysString =  $this->getValue('reviewReminderEditorDaysFromDueDate');
        $reminderDays = $reminderDaysString ? array_map('intval', array_filter(array_map('trim', explode(',', $reminderDaysString)))) : [];
        // RETURN DAYS IN DESCENDING ORDER;
        rsort($reminderDays);
        return $reminderDays;
    }

    public function reviewReminderReviewerTaskEnabled() {
        return $this->getValue('reviewReminderReviewerTaskEnabled');
    }

    public function reviewReminderReviewerDaysFromDueDate() {
        return $this->getValue('reviewReminderReviewerDaysFromDueDate');
    }

    public function reviewReminderEmailOverrideEnabled() {
        return $this->getValue('reviewReminderEmailOverrideEnabled');
    }

    public function reviewUploadFileValidationEnabled() {
        return $this->getValue('reviewUploadFileValidationEnabled');
    }

    public function reviewerRegistrationEmailDisabled() {
        return $this->getValue('reviewerRegistrationEmailDisabled');
    }

    private function getValue($propertyName) {
        return $this->pprPlugin->getSetting($this->contextId, $propertyName) ?? self::CONFIG_VARS[$propertyName][1];
    }
}