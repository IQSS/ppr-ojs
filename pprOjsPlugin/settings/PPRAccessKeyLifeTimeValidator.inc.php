<?php

import ('lib.pkp.classes.form.validation.FormValidator');

class PPRAccessKeyLifeTimeValidator extends FormValidator {

    function __construct(&$form) {
        parent::__construct($form, 'accessKeyLifeTime', FORM_VALIDATOR_OPTIONAL_VALUE, 'plugins.generic.pprPlugin.settings.accessKeyLifeTime.invalid');
    }

    function isValid() {
        if ($this->isEmptyAndOptional()) {
            return true;
        }

        // SIMPLE VALIDATION TO LIMIT THE VALUES TO <= 100
        $value = intval($this->getFieldValue());
        if ($value < 0 || $value > 100) {
            return false;
        }

        return true;
    }

}