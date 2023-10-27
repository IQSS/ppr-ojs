<?php

class PPRPluginSettings {

    private $contextId;

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
        'hideUserBioEnabled' => ['bool', null],
        'userOnLeaveEnabled' => ['bool', null],
        'userCustomFieldsEnabled' => ['bool', null],
        'categoryOptions' => ['string', 'Faculty, Fellow (Post-Doc), Grad Student, Staff, Student'],
        'institutionOptions' => ['string', 'Harvard University, Washington University in St. Louis'],
        //DEFAULT TO TRUE AS NAME HAS CHANGED. TODO: RESET IN NEXT RELEASE
        'submissionCommentsForReviewerEnabled' => ['bool', true],
        'submissionResearchTypeEnabled' => ['bool', null],
        'researchTypeOptions' => ['string', 'Manuscript Draft, Meta-Analysis, Paper, Pre-Analysis Plan, Grant Proposal, Book Proposal, Other'],
        'submissionHidePrefixEnabled' => ['bool', null],
        'submissionCloseEnabled' => ['bool', null],
        'submissionApprovedEmailEnabled' => ['bool', null],
        'submissionConfirmationChecklistEnabled' => ['bool', null],
        'submissionUploadFileValidationEnabled' => ['bool', null],
        'submissionRequestRevisionsFileValidationEnabled' => ['bool', null],
        'publicationOverrideEnabled' => ['bool', null],
        'hideReviewRoundSelectionEnabled' => ['bool', null],
        'hideSendToReviewersEnabled' => ['bool', null],
        //DEFAULT TO TRUE AS NAME HAS CHANGED. TODO: RESET IN NEXT RELEASE
        'reviewReminderEditorTaskEnabled' => ['bool', true],
        'reviewReminderEditorDaysFromDueDate' => ['string', null],
        //DEFAULT TO TRUE AS NAME HAS CHANGED. TODO: RESET IN NEXT RELEASE
        'reviewReminderReviewerTaskEnabled' => ['bool', true],
        'reviewReminderReviewerDaysFromDueDate' => ['int', null],
        'reviewReminderEmailOverrideEnabled' => ['bool', null],
        'reviewAddEditorToBccEnabled' => ['bool', null],
        'reviewUploadFileValidationEnabled' => ['bool', null],

        'authorSubmissionSurveyHtml' => ['string', null],
        'authorDashboardSurveyHtml' => ['string', null],
        'reviewerSurveyHtml' => ['string', null],

        'reviewerRegistrationEmailDisabled' => ['bool', null],
        'reviewAcceptedEmailEnabled' => ['bool', null],
        'reviewSubmittedEmailEnabled' => ['bool', null],
        'submissionConfirmationContributorsEmailDisabled' => ['bool', null],
        'editorialDecisionsEmailRemoveContributorsEnabled' => ['bool', null],
        'addReviewerEmailServiceEnabled' => ['bool', null],
        'unassignReviewerEmailOverrideEnabled' => ['bool', null],

        'accessKeyLifeTime' => ['int', 30],
        'fileUploadTextOverrideEnabled' => ['bool', null],
    );
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

    public function hideReviewRoundSelectionEnabled() {
        return $this->getValue('hideReviewRoundSelectionEnabled');
    }

    public function hideSendToReviewersEnabled() {
        return $this->getValue('hideSendToReviewersEnabled');
    }

    public function hidePreferredPublicNameEnabled() {
        return $this->getValue('hidePreferredPublicNameEnabled');
    }

    public function hideUserBioEnabled() {
        return $this->getValue('hideUserBioEnabled');
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

    public function submissionCommentsForReviewerEnabled() {
        return $this->getValue('submissionCommentsForReviewerEnabled');
    }

    public function submissionResearchTypeEnabled() {
        return $this->getValue('submissionResearchTypeEnabled');
    }

    public function getResearchTypeOptions() {
        $researchTypeString =  $this->getValue('researchTypeOptions');
        $researchTypeOptions = array_filter(array_map('trim', explode(',', $researchTypeString)));
        return array_combine($researchTypeOptions, $researchTypeOptions);
    }

    public function submissionHidePrefixEnabled() {
        return $this->getValue('submissionHidePrefixEnabled');
    }

    public function submissionCloseEnabled() {
        return $this->getValue('submissionCloseEnabled');
    }

    public function submissionApprovedEmailEnabled() {
        return $this->getValue('submissionApprovedEmailEnabled');
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

    public function publicationOverrideEnabled() {
        return $this->getValue('publicationOverrideEnabled');
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

    public function reviewAddEditorToBccEnabled() {
        return $this->getValue('reviewAddEditorToBccEnabled');
    }

    public function reviewUploadFileValidationEnabled() {
        return $this->getValue('reviewUploadFileValidationEnabled');
    }

    public function fileUploadTextOverrideEnabled() {
        return $this->getValue('fileUploadTextOverrideEnabled');
    }

    public function reviewerRegistrationEmailDisabled() {
        return $this->getValue('reviewerRegistrationEmailDisabled');
    }

    public function reviewAcceptedEmailEnabled() {
        return $this->getValue('reviewAcceptedEmailEnabled');
    }

    public function reviewSubmittedEmailEnabled() {
        return $this->getValue('reviewSubmittedEmailEnabled');
    }

    public function submissionConfirmationContributorsEmailDisabled() {
        return $this->getValue('submissionConfirmationContributorsEmailDisabled');
    }

    public function editorialDecisionsEmailRemoveContributorsEnabled() {
        return $this->getValue('editorialDecisionsEmailRemoveContributorsEnabled');
    }

    public function addReviewerEmailServiceEnabled() {
        return $this->getValue('addReviewerEmailServiceEnabled');
    }

    public function unassignReviewerEmailOverrideEnabled() {
        return $this->getValue('unassignReviewerEmailOverrideEnabled');
    }

    public function authorSubmissionSurveyHtml() {
        return $this->getValue('authorSubmissionSurveyHtml');
    }

    public function authorDashboardSurveyHtml() {
        return $this->getValue('authorDashboardSurveyHtml');
    }

    public function reviewerSurveyHtml() {
        return $this->getValue('reviewerSurveyHtml');
    }


    public function accessKeyLifeTime() {
        return $this->getValue('accessKeyLifeTime');
    }

    private function getValue($propertyName) {
        return $this->pprPlugin->getSetting($this->contextId, $propertyName) ?? self::CONFIG_VARS[$propertyName][1];
    }
}