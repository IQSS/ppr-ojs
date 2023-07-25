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
            'displayWorkflowMessageEnabled' => true,
            'displayContributorsEnabled' => null,
            'displaySuggestedReviewersEnabled' => null,
            'hideReviewMethodEnabled' => null,
            'hideReviewFormDefaultEnabled' => null,
            'hideReviewRecommendationEnabled' => null,
            'hidePreferredPublicNameEnabled' => null,
            'userOnLeaveEnabled' => null,
            'userCustomFieldsEnabled' => null,
            'getCategoryOptions' => ['Faculty' => 'Faculty', 'Fellow (Post-Doc)' => 'Fellow (Post-Doc)', 'Grad Student' => 'Grad Student', 'Staff' => 'Staff', 'Student' => 'Student'],
            'getInstitutionOptions' => ['Harvard University' => 'Harvard University', 'Washington University in St. Louis' => 'Washington University in St. Louis'],
            'submissionCustomFieldsEnabled' => null,
            'submissionCloseEnabled' => null,
            'submissionConfirmationChecklistEnabled' => null,
            'submissionUploadFileValidationEnabled' => null,
            'submissionRequestRevisionsFileValidationEnabled' => null,
            'reviewReminderEditorTaskEnabled' => true,
            'getReviewReminderEditorDaysFromDueDate' => [],
            'reviewReminderReviewerTaskEnabled' => true,
            'reviewReminderReviewerDaysFromDueDate' => null,
            'reviewReminderEmailOverrideEnabled' => null,
            'reviewUploadFileValidationEnabled' => null,
            'reviewerRegistrationEmailDisabled' => null,
        );
        $pprPluginMock = new PPRPluginMock([]);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);
        foreach ($expectedDefaultValues as $methodName => $expectedDefaultValue) {
            $result = call_user_func([$target, $methodName]);
            $this->assertEquals($expectedDefaultValue, $result, "Error for method: $methodName");
        }
    }

    public function test_getCategoryOptions_splits_comma_separated_string_values_into_map() {
        $pprPluginMock = new PPRPluginMock(['categoryOptions' => 'first, second, third']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals(['first' => 'first', 'second' => 'second', 'third' => 'third'], $target->getCategoryOptions());
    }

    public function test_getCategoryOptions_handles_empty_string() {
        $pprPluginMock = new PPRPluginMock(['categoryOptions' => '']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals([], $target->getCategoryOptions());
    }

    public function test_getCategoryOptions_should_filter_empty_strings() {
        $pprPluginMock = new PPRPluginMock(['categoryOptions' => 'first, ,second']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals(['first' => 'first', 'second' => 'second'], $target->getCategoryOptions());
    }

    public function test_getInstitutionOptions_splits_comma_separated_string_values_into_map() {
        $pprPluginMock = new PPRPluginMock(['institutionOptions' => 'first, second, third']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals(['first' => 'first', 'second' => 'second', 'third' => 'third'], $target->getInstitutionOptions());
    }

    public function test_getInstitutionOptions_handles_empty_string() {
        $pprPluginMock = new PPRPluginMock(['institutionOptions' => '']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals([], $target->getInstitutionOptions());
    }

    public function test_getInstitutionOptions_should_filter_empty_strings() {
        $pprPluginMock = new PPRPluginMock(['institutionOptions' => 'first, ,second']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals(['first' => 'first', 'second' => 'second'], $target->getInstitutionOptions());
    }

    public function test_getReviewReminderEditorDaysFromDueDate_should_handle_empty_string() {
        $pprPluginMock = new PPRPluginMock(['reviewReminderEditorDaysFromDueDate' => '']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals([], $target->getReviewReminderEditorDaysFromDueDate());
    }

    public function test_getReviewReminderEditorDaysFromDueDate_should_convert_to_int_and_order() {
        $pprPluginMock = new PPRPluginMock(['reviewReminderEditorDaysFromDueDate' => '1 , 2 , 3 ']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals([3, 2, 1], $target->getReviewReminderEditorDaysFromDueDate());
    }

    public function test_getReviewReminderEditorDaysFromDueDate_should_filter_empty_strings() {
        $pprPluginMock = new PPRPluginMock(['reviewReminderEditorDaysFromDueDate' => '1, ,3, ,2']);
        $target = new PPRPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals([3, 2, 1], $target->getReviewReminderEditorDaysFromDueDate());
    }
}