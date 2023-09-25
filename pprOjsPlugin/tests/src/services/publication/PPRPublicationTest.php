<?php

import('tests.src.PPRTestCase');
import('services.publication.PPRPublication');

class PPRPublicationTest extends PPRTestCase {

    public function test_getShortAuthorString_should_return_empty_when_no_authors() {
        $target = $this->createTarget(null, []);

        $this->assertEquals('', $target->getShortAuthorString());
    }

    public function test_getShortAuthorString_should_use_primary_author() {
        $primaryAuthor = $this->getTestUtil()->createAuthor(33, 'Primary');
        $firstContributor = $this->getTestUtil()->createAuthor(10, 'Contributor');
        $target = $this->createTarget(33, [$firstContributor, $primaryAuthor]);

        $this->assertEquals('Primary', $target->getShortAuthorString());
    }

    public function test_getShortAuthorString_should_use_first_contributor_when_no_primary_author() {
        $primaryAuthor = $this->getTestUtil()->createAuthor(33, 'Primary');
        $firstContributor = $this->getTestUtil()->createAuthor(10, 'Contributor');
        $target = $this->createTarget(null, [$firstContributor, $primaryAuthor]);

        $this->assertEquals('Contributor', $target->getShortAuthorString());
    }

    public function test_getShortAuthorString_should_use_familyName() {
        $primaryAuthor = $this->getTestUtil()->createAuthor(33, 'FamilyName', 'GivenName');
        $target = $this->createTarget(33, [$primaryAuthor]);

        $this->assertEquals('FamilyName', $target->getShortAuthorString());
    }

    public function test_getShortAuthorString_should_use_given_name_when_familyName_not_set() {
        $primaryAuthor = $this->getTestUtil()->createAuthor(33, null, 'GivenName');
        $target = $this->createTarget(33, [$primaryAuthor]);

        $this->assertEquals('GivenName', $target->getShortAuthorString());
    }

    private function createTarget($primaryContactId, $authors) {
        $target = new PPRPublication();
        $target->setAllData(['primaryContactId' => $primaryContactId, 'authors' => $authors]);
        return $target;
    }
}