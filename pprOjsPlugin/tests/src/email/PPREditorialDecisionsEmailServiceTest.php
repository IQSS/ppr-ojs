<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.email.PPREditorialDecisionsEmailService');

class PPREditorialDecisionsEmailServiceTest extends PPRTestCase {

    const CONTEXT_ID = 130;

    public function setUp(): void {
        parent::setUp();
    }

    public function test_register_should_not_register_any_hooks_when_editorialDecisionsEmailRemoveContributorsEnabled_is_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['editorialDecisionsEmailRemoveContributorsEnabled' => false]);
        $target = new PPREditorialDecisionsEmailService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_service_hooks_when_editorialDecisionsEmailRemoveContributorsEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['editorialDecisionsEmailRemoveContributorsEnabled' => true]);
        $target = new PPREditorialDecisionsEmailService($pprPluginMock);
        $target->register();

        $this->assertEquals(2, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('sendreviewsform::display')));
        $this->assertEquals(1, count($this->getHooks('Mail::send')));
    }

}