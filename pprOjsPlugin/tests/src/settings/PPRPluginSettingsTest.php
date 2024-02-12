<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('settings.PPRPluginSettings');

class PPRPluginSettingsTest extends PPRTestCase {

    const CONTEXT_ID = 100;

    public function setUp(): void {
        parent::setUp();
    }

    public function test_default_values() {
        $expectedDefaultValues = array(
            // fieldName => [methodName, defaultValue]
            'displayWorkflowMessageEnabled' => [null, true],
            'displayContributorsEnabled' => [null, null],
            'displaySuggestedReviewersEnabled' => [null, null],
            'hideReviewMethodEnabled' => [null, null],
            'hideReviewFormDefaultEnabled' => [null, null],
            'hideReviewRecommendationEnabled' => [null, null],
            'hidePreferredPublicNameEnabled' => [null, null],
            'hideUserBioEnabled' => [null, null],
            'userOnLeaveEnabled' => [null, null],
            'userCustomFieldsEnabled' => [null, null],
            'categoryOptions' => ['getCategoryOptions', ['Faculty' => 'Faculty', 'Fellow (Post-Doc)' => 'Fellow (Post-Doc)', 'Grad Student' => 'Grad Student', 'Staff' => 'Staff', 'Student' => 'Student']],
            'institutionOptions' => ['getInstitutionOptions', ['Harvard University' => 'Harvard University', 'Washington University in St. Louis' => 'Washington University in St. Louis']],
            'submissionCommentsForReviewerEnabled' => [null, true],
            'submissionResearchTypeEnabled' => [null, null],
            'researchTypeOptions' => ['getResearchTypeOptions', ['Paper' => 'Paper Review', 'Pre-Analysis Plan' => 'Pre-Analysis Plan', 'Grant Proposal' => 'Paper Review', 'Book Proposal' => 'Paper Review', 'Other' => 'Paper Review']],
            'submissionHidePrefixEnabled' => [null, null],
            'submissionCloseEnabled' => [null, null],
            'submissionApprovedEmailEnabled' => [null, null],
            'submissionConfirmationChecklistEnabled' => [null, null],
            'submissionUploadFileValidationEnabled' => [null, null],
            'submissionRequestRevisionsFileValidationEnabled' => [null, null],
            'publicationOverrideEnabled' => [null, null],
            'hideReviewRoundSelectionEnabled' => [null, null],
            'hideSendToReviewersEnabled' => [null, null],
            'reviewSentAuthorTaskEnabled' => [null, null],
            'reviewSentAuthorWaitingDays' => [null, 7],
            'reviewSentAuthorEnabledDate' => [null, '2023-11-01'],
            'reviewReminderEditorTaskEnabled' => [null, true],
            'reviewReminderEditorDaysFromDueDate' => ['getReviewReminderEditorDaysFromDueDate', []],
            'reviewReminderReviewerTaskEnabled' => [null, true],
            'reviewReminderReviewerDaysFromDueDate' => [null, null],
            'reviewReminderEmailOverrideEnabled' => [null, null],
            'reviewAddEditorToBccEnabled' => [null, null],
            'reviewUploadFileValidationEnabled' => [null, null],
            'reviewAttachmentsOverrideEnabled' => [null, null],
            'firstNameEmailEnabled' => [null, true],
            'reviewerRegistrationEmailDisabled' => [null, null],
            'reviewAcceptedEmailEnabled' => [null, null],
            'reviewSubmittedEmailEnabled' => [null, null],
            'submissionConfirmationContributorsEmailDisabled' => [null, null],
            'editorialDecisionsEmailRemoveContributorsEnabled' => [null, null],
            'unassignReviewerEmailOverrideEnabled' => [null, null],
            'authorSubmissionSurveyHtml' => [null, null],
            'authorDashboardSurveyHtml' => [null, null],
            'reviewerSurveyHtml' => [null, null],
            'accessKeyLifeTime' => [null, 30],
            'fileUploadTextOverrideEnabled' => [null, null],
        );

        foreach (PPRPluginSettings::CONFIG_VARS as $configVar => $varInfo) {
            $this->assertEquals(true, array_key_exists($configVar, $expectedDefaultValues), "PPR settings default value not tested for: $configVar");
        }

        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, []);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);
        foreach ($expectedDefaultValues as $configVar => $varInfo) {
            $result = call_user_func([$target, $varInfo[0] ?? $configVar]);
            $this->assertEquals($varInfo[1], $result, "Error for config var: $configVar");
        }
    }

    public function test_getResearchTypes_default_value() {
        $expectedValue = ['Paper' => 'Paper', 'Pre-Analysis Plan' => 'Pre-Analysis Plan', 'Grant Proposal' => 'Grant Proposal', 'Book Proposal' => 'Book Proposal', 'Other' => 'Other'];

        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, []);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);
        $this->assertEquals($expectedValue, $target->getResearchTypes(), "Error for config var: getResearchTypes");
    }

    public function test_getCategoryOptions_splits_comma_separated_string_values_into_map() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['categoryOptions' => 'first, second, third']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals(['first' => 'first', 'second' => 'second', 'third' => 'third'], $target->getCategoryOptions());
    }

    public function test_getCategoryOptions_handles_empty_string() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['categoryOptions' => '']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals([], $target->getCategoryOptions());
    }

    public function test_getCategoryOptions_should_filter_empty_strings() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['categoryOptions' => 'first, ,second']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals(['first' => 'first', 'second' => 'second'], $target->getCategoryOptions());
    }

    public function test_getInstitutionOptions_splits_comma_separated_string_values_into_map() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['institutionOptions' => 'first, second, third']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals(['first' => 'first', 'second' => 'second', 'third' => 'third'], $target->getInstitutionOptions());
    }

    public function test_getInstitutionOptions_handles_empty_string() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['institutionOptions' => '']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals([], $target->getInstitutionOptions());
    }

    public function test_getInstitutionOptions_should_filter_empty_strings() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['institutionOptions' => 'first, ,second']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals(['first' => 'first', 'second' => 'second'], $target->getInstitutionOptions());
    }

    public function test_getResearchTypeOptions_getResearchTypes_handles_list_of_research_types_with_reviewer_forms_in_comma_separated_name_equals_value_format() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['researchTypeOptions' => ' first = first form, second = second form, third = third form']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals(['first' => 'first form', 'second' => 'second form', 'third' => 'third form'], $target->getResearchTypeOptions());
        $this->assertEquals(['first' => 'first', 'second' => 'second', 'third' => 'third'], $target->getResearchTypes());
    }

    public function test_getResearchTypeOptions_getResearchTypes_handles_missing_reviewer_forms() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['researchTypeOptions' => 'first, second, third = ']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals(['first' => null, 'second' => null, 'third' => null], $target->getResearchTypeOptions());
        $this->assertEquals(['first' => 'first', 'second' => 'second', 'third' => 'third'], $target->getResearchTypes());
    }

    public function test_getResearchTypeOptions_getResearchTypes_handles_empty_string() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['researchTypeOptions' => '']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals([], $target->getResearchTypeOptions());
        $this->assertEquals([], $target->getResearchTypes());
    }

    public function test_getResearchTypeOptions_getResearchTypes_should_filter_empty_strings() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['researchTypeOptions' => 'first = first form, ,second = second form']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals(['first' => 'first form', 'second' => 'second form'], $target->getResearchTypeOptions());
        $this->assertEquals(['first' => 'first', 'second' => 'second'], $target->getResearchTypes());
    }

    public function test_getReviewReminderEditorDaysFromDueDate_should_handle_empty_string() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['reviewReminderEditorDaysFromDueDate' => '']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals([], $target->getReviewReminderEditorDaysFromDueDate());
    }

    public function test_getReviewReminderEditorDaysFromDueDate_should_convert_to_int_and_order() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['reviewReminderEditorDaysFromDueDate' => '1 , 2 , 3 ']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals([3, 2, 1], $target->getReviewReminderEditorDaysFromDueDate());
    }

    public function test_getReviewReminderEditorDaysFromDueDate_should_filter_empty_strings() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['reviewReminderEditorDaysFromDueDate' => '1, ,3, ,2']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals([3, 2, 1], $target->getReviewReminderEditorDaysFromDueDate());
    }
}