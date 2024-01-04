<?php

/**
 * Class to mock a OJS User class and return values useful for emails or front-end templates when a user is not found.
 */
class PPRMissingUser {
    private $defaultValue;

    public function __construct($defaultValue = '') {
        $this->defaultValue = $defaultValue;
    }

    public function getLocalizedGivenName() {
        return $this->defaultValue;
    }

    public function getFullName($preferred = true, $familyFirst = false, $defaultLocale = null) {
        return $this->defaultValue;
    }
}