<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.submission.PPRAuthorSubmissionSurveyService');
import('util.PPRObjectFactory');

import('classes.notification.Notification');

class PPRAuthorSubmissionSurveyServiceTest extends PPRTestCase {

    const CONTEXT_ID = 55009;

    private $defaultPPRPlugin;

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, []);
        $context = $this->createMock(Context::class);
        $context->method('getId')->willReturn(self::CONTEXT_ID);
        $this->getRequestMock()->method('getContext')->willReturn($context);
    }

    public function test_register_should_not_register_any_hooks_when_service_toggles_are_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['authorSubmissionSurveyHtml' => false], true);
        $target = new PPRAuthorSubmissionSurveyService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_service_hooks_when_submissionCloseEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['authorSubmissionSurveyHtml' => true], false);
        $target = new PPRAuthorSubmissionSurveyService($pprPluginMock);
        $target->register();

        $this->assertEquals(1, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('TemplateManager::fetch')));
    }

    public function test_addAuthorSurvey_should_not_update_template_manager_when_it_is_not_complete_template() {
        $templateName = 'otherTemplate.tpl';
        $templateManager = TemplateManager::getManager();

        $target = new PPRAuthorSubmissionSurveyService($this->defaultPPRPlugin);

        $result = $target->addAuthorSurvey('TemplateManager::fetch', [$templateManager, $templateName]);
        $this->assertEquals(false, $result);
        $this->assertNull($templateManager->getTemplateVars('showPPRAuthorSurvey'));
    }

    public function test_addAuthorSurvey_should_show_survey_when_no_survey_notifications_for_user() {
        $templateName = 'submission/form/complete.tpl';
        $templateManager = TemplateManager::getManager();

        $objectFactory = $this->createMock(PPRObjectFactory::class);
        $notificationRegistry = $this->createMock(PPRTaskNotificationRegistry::class);
        $notificationRegistry->expects($this->once())->method('getSubmissionSurveyForAuthor')->willReturn([]);
        $notificationRegistry->expects($this->once())->method('registerSubmissionSurveyForAuthor');
        $objectFactory->expects($this->once())->method('pprTaskNotificationRegistry')->with(self::CONTEXT_ID)->willReturn($notificationRegistry);

        $target = new PPRAuthorSubmissionSurveyService($this->defaultPPRPlugin, $objectFactory);

        $result = $target->addAuthorSurvey('TemplateManager::fetch', [$templateManager, $templateName]);
        $this->assertEquals(false, $result);
        $this->assertEquals(true, $templateManager->getTemplateVars('showPPRAuthorSurvey'));
    }

    public function test_addAuthorSurvey_should_not_show_survey_when_there_are_survey_notifications_for_user() {
        $templateName = 'submission/form/complete.tpl';
        $templateManager = TemplateManager::getManager();

        $objectFactory = $this->createMock(PPRObjectFactory::class);
        $notificationRegistry = $this->createMock(PPRTaskNotificationRegistry::class);
        $notificationRegistry->expects($this->once())->method('getSubmissionSurveyForAuthor')->willReturn([new Notification()]);
        $notificationRegistry->expects($this->never())->method('registerSubmissionSurveyForAuthor');
        $objectFactory->expects($this->once())->method('pprTaskNotificationRegistry')->with(self::CONTEXT_ID)->willReturn($notificationRegistry);

        $target = new PPRAuthorSubmissionSurveyService($this->defaultPPRPlugin, $objectFactory);

        $result = $target->addAuthorSurvey('TemplateManager::fetch', [$templateManager, $templateName]);
        $this->assertEquals(false, $result);
        $this->assertEquals(false, $templateManager->getTemplateVars('showPPRAuthorSurvey'));
    }
}