<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.publication.PPRPublicationOverrideService');

class PPRPublicationOverrideServiceTest extends PPRTestCase {

    const CONTEXT_ID = 14;

    public function test_register_should_not_register_PPRPublicationDAO_when_publicationOverrideEnabled_is_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['publicationOverrideEnabled' => false]);
        $target = new PPRPublicationOverrideService($pprPluginMock);
        $target->register();

        $publicationDao = DAORegistry::getDAO('PublicationDAO');
        $this->assertEquals(false, $publicationDao instanceof PPRPublicationDAO);
    }

    public function test_register_should_register_PPRPublicationDAO_when_publicationOverrideEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['publicationOverrideEnabled' => true]);
        $target = new PPRPublicationOverrideService($pprPluginMock);
        $target->register();

        $publicationDao = DAORegistry::getDAO('PublicationDAO');
        $this->assertEquals(true, $publicationDao instanceof PPRPublicationDAO);
    }
}