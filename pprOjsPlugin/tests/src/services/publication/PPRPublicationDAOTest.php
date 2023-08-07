<?php

import('tests.src.PPRTestCase');
import('services.publication.PPRPublicationDAO');

class PPRPublicationDAOTest extends PPRTestCase {

    public function test_newDataObject_should_return_PPRPublication() {
        $target = new PPRPublicationDAO();
        $this->assertEquals(true, $target->newDataObject() instanceof PPRPublication);
    }
}