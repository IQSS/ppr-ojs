<?php

import('tests.src.PPRTestCase');
import('settings.PPRPluginSettingsForm');
import('settings.PPRReviewReminderDaysValidator');

class PPRReviewReminderDaysValidatorTest extends PPRTestCase {

    public function test_null_values_are_valid() {
        $formMock = $this->createMock(PPRPluginSettingsForm::class);
        $formMock->expects($this->atLeast(1))->method('getData')->with('reviewReminderEditorDaysFromDueDate')->willReturn(null);
        $target = new PPRReviewReminderDaysValidator($formMock);

        $this->assertEquals(true, $target->isValid());
    }

    public function test_empty_values_are_valid() {
        $formMock = $this->createMock(PPRPluginSettingsForm::class);
        $formMock->expects($this->atLeast(1))->method('getData')->with('reviewReminderEditorDaysFromDueDate')->willReturn('');
        $target = new PPRReviewReminderDaysValidator($formMock);

        $this->assertEquals(true, $target->isValid());
    }

    public function test_value_is_valid_when_all_items_are_valid() {
        $formMock = $this->createMock(PPRPluginSettingsForm::class);
        $formMock->expects($this->atLeast(1))->method('getData')->with('reviewReminderEditorDaysFromDueDate')->willReturn('-20, -10, 0 , 10, 20');
        $target = new PPRReviewReminderDaysValidator($formMock);

        $this->assertEquals(true, $target->isValid());
    }

    public function test_values_bigger_than_20_are_not_valid() {
        $formMock = $this->createMock(PPRPluginSettingsForm::class);
        $formMock->expects($this->atLeast(1))->method('getData')->with('reviewReminderEditorDaysFromDueDate')->willReturn('21');
        $target = new PPRReviewReminderDaysValidator($formMock);

        $this->assertEquals(false, $target->isValid());
    }

    public function test_values_smaller_than_minus_20_are_not_valid() {
        $formMock = $this->createMock(PPRPluginSettingsForm::class);
        $formMock->expects($this->atLeast(1))->method('getData')->with('reviewReminderEditorDaysFromDueDate')->willReturn('-21');
        $target = new PPRReviewReminderDaysValidator($formMock);

        $this->assertEquals(false, $target->isValid());
    }

    public function test_value_is_not_valid_when_one_item_is_not_valid() {
        $formMock = $this->createMock(PPRPluginSettingsForm::class);
        $formMock->expects($this->atLeast(1))->method('getData')->with('reviewReminderEditorDaysFromDueDate')->willReturn('-20, -10, 100 , 10, 20');
        $target = new PPRReviewReminderDaysValidator($formMock);

        $this->assertEquals(false, $target->isValid());
    }
}