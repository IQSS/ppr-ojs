<?php

import ('lib.pkp.classes.form.validation.FormValidator');

class PPRReviewReminderDaysValidator extends FormValidator {

    function __construct(&$form) {
        parent::__construct($form, 'reviewReminderEditorDaysFromDueDate', FORM_VALIDATOR_OPTIONAL_VALUE, 'plugins.generic.pprPlugin.settings.reviewReminderEditorDaysFromDueDate.invalid');
    }

    function isValid() {
        if ($this->isEmptyAndOptional()) {
            return true;
        }

        // SIMPLE VALIDATION TO LIMIT THE VALUES TO <= 20
        $value = $this->getFieldValue();
        $reminderDays = array_map('intval', explode(',', $value));
        foreach ($reminderDays as $reminderDay) {
            if (abs($reminderDay) >= 20) {
                return false;
            }
        }

        return true;
    }

}