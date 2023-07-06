<?php

class PPRPluginSettings {

    const CONFIG_VARS = array(
        // PROPERTY NAME => [TYPE, DEFAULT VALUE]
        'displayWorkflowMessageEnabled' => ['bool', true],
        'displayContributorsEnabled' => ['bool', null],
        'displaySuggestedReviewersEnabled' => ['bool', null],
        'hideReviewMethodEnabled' => ['bool', null],
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
        'reviewReminderEditorEnabled' => ['bool', null],
        'reviewReminderEditorDaysFromDueDate' => ['string', null],
        'reviewerRegistrationEmailDisabled' => ['bool', null],
    );

    /** @var $contextId int */
    private $contextId;

    /** @var $pprPlugin object */
    private $pprPlugin;

    public function __construct($contextId, $pprPlugin) {
        $this->contextId = $contextId;
        $this->pprPlugin = $pprPlugin;
    }

    public function getContextId() {
        return $this->contextId;
    }

    public function displayWorkflowMessageEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'displayWorkflowMessageEnabled') ?? self::CONFIG_VARS['displayWorkflowMessageEnabled'][1];
    }

    public function displayContributorsEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'displayContributorsEnabled');
    }

    public function displaySuggestedReviewersEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'displaySuggestedReviewersEnabled');
    }

    public function hideReviewMethodEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'hideReviewMethodEnabled');
    }

    public function hideReviewRecommendationEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'hideReviewRecommendationEnabled');
    }

    public function hidePreferredPublicNameEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'hidePreferredPublicNameEnabled');
    }

    public function userOnLeaveEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'userOnLeaveEnabled');
    }

    public function userCustomFieldsEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'userCustomFieldsEnabled');
    }

    public function getCategoryOptions() {
        $categoriesString =  $this->pprPlugin->getSetting($this->contextId, 'categoryOptions') ?? self::CONFIG_VARS['categoryOptions'][1];
        $categoryOptions = array_map('trim', explode(',', $categoriesString));
        return array_combine($categoryOptions, $categoryOptions);
    }

    public function getInstitutionOptions() {
        $institutionString =  $this->pprPlugin->getSetting($this->contextId, 'institutionOptions') ?? self::CONFIG_VARS['institutionOptions'][1];
        $institutionOptions = array_map('trim', explode(',', $institutionString));
        return array_combine($institutionOptions, $institutionOptions);
    }

    public function submissionCustomFieldsEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'submissionCustomFieldsEnabled');
    }

    public function submissionCloseEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'submissionCloseEnabled');
    }

    public function submissionConfirmationChecklistEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'submissionConfirmationChecklistEnabled');
    }

    public function submissionUploadFileValidationEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'submissionUploadFileValidationEnabled');
    }

    public function submissionRequestRevisionsFileValidationEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'submissionRequestRevisionsFileValidationEnabled');
    }

    public function reviewReminderEditorEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'reviewReminderEditorEnabled');
    }

    public function reviewReminderEditorDaysFromDueDate() {
        $reminderDaysString =  $this->pprPlugin->getSetting($this->contextId, 'reviewReminderEditorDaysFromDueDate');
        $reminderDays = $reminderDaysString ? array_map('intval', explode(',', $reminderDaysString)) : [];
        // RETURN DAYS IN DESCENDING ORDER;
        rsort($reminderDays);
        return $reminderDays;
    }

    public function reviewerRegistrationEmailDisabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'reviewerRegistrationEmailDisabled');
    }

}