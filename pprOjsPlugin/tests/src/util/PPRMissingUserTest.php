<?php

import('tests.src.PPRTestCase');

class PPRMissingUserTest extends PPRTestCase {

    public function test_User_default_value() {
        $target = new PPRMissingUser();

        $this->assertEquals('', $target->getFullName());
        $this->assertEquals('', $target->getLocalizedGivenName());
    }

    public function test_User_overridden_value() {
        $target = new PPRMissingUser('valueToReturn');

        $this->assertEquals('valueToReturn', $target->getFullName());
        $this->assertEquals('valueToReturn', $target->getLocalizedGivenName());
    }
}