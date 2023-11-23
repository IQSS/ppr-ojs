<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.reviewer.PPRReviewAttachmentsService');

class PPRReviewAttachmentsServiceTest extends PPRTestCase {

    const CONTEXT_ID = 9922234;
    private $defaultPPRPlugin;

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, []);
    }

    public function test_register_should_not_register_any_hooks_when_reviewAttachmentsOverrideEnabled_is_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['reviewAttachmentsOverrideEnabled' => false], true);
        $target = new PPRReviewAttachmentsService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_service_hooks_when_reviewAttachmentsOverrideEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['reviewAttachmentsOverrideEnabled' => true], false);
        $target = new PPRReviewAttachmentsService($pprPluginMock);
        $target->register();

        $this->assertEquals(1, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('LoadComponentHandler')));
    }

    public function test_addPPRReviewAttachmentsGridHandler_should_replace_the_component_name_when_it_matches_expected_value() {
        $target = new PPRReviewAttachmentsService($this->defaultPPRPlugin);
        $componentName = 'grid.files.attachment.EditorSelectableReviewAttachmentsGridHandler';
        $arguments = [& $componentName];
        $result = $target->addPPRReviewAttachmentsGridHandler('LoadComponentHandler', $arguments);

        $this->assertEquals(true, $result);
        $this->assertEquals('plugins.generic.pprOjsPlugin.services.reviewer.PPRReviewAttachmentsGridHandler', $componentName);
    }

    public function test_addPPRReviewAttachmentsGridHandler_should_not_replace_the_component_name_when_it_does_not_match_expected_value() {
        $target = new PPRReviewAttachmentsService($this->defaultPPRPlugin);
        $componentName = 'grid.component.name.not.expected';
        $arguments = [& $componentName];
        $result = $target->addPPRReviewAttachmentsGridHandler('LoadComponentHandler', $arguments);

        $this->assertEquals(false, $result);
        $this->assertEquals('grid.component.name.not.expected', $componentName);
    }
}