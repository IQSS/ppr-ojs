<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.submission.PPRSubmissionActionsService');
import('util.PPRObjectFactory');

import('lib.pkp.classes.linkAction.LinkAction');
import('controllers.modals.editorDecision.form.InitiateExternalReviewForm');

class PPRSubmissionActionsServiceTest extends PPRTestCase {

    const CONTEXT_ID = 130;
    const SUBJECT_WITH_TITLE_VAR = 'Subject: {$submissionTitle} Variable';
    private $defaultPPRPlugin;

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, []);
    }

    public function test_register_should_not_register_any_hooks_when_service_toggles_are_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['submissionCloseEnabled' => false, 'submissionApprovedEmailEnabled' => false], true);
        $target = new PPRSubmissionActionsService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_service_hooks_when_submissionCloseEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['submissionCloseEnabled' => true], false);
        $target = new PPRSubmissionActionsService($pprPluginMock);
        $target->register();

        $this->assertEquals(3, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('Schema::get::submission')));
        $this->assertEquals(1, count($this->getHooks('TemplateManager::fetch')));
        $this->assertEquals(1, count($this->getHooks('LoadComponentHandler')));
    }

    public function test_register_should_register_service_hooks_when_submissionApprovedEmailEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['submissionApprovedEmailEnabled' => true], false);
        $target = new PPRSubmissionActionsService($pprPluginMock);
        $target->register();

        $this->assertEquals(1, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('initiateexternalreviewform::execute')));
    }

    public function test_addFieldsToSubmissionDatabaseSchema_should_update_schema() {
        $target = new PPRSubmissionActionsService($this->defaultPPRPlugin);
        $schema = new stdClass();;
        $schema->properties = new stdClass();

        $target->addFieldsToSubmissionDatabaseSchema('Schema::get::submission', [$schema]);
        $this->assertEquals(true, isset($schema->properties->closedDate));
        $this->assertEquals(true, isset($schema->properties->closedDate->validation));
        $this->assertEquals('string', $schema->properties->closedDate->type);
    }

    public function test_addPPRSubmissionActionsHandler_should_update_component_when_it_is_SubmissionActionsHandler() {
        $target = new PPRSubmissionActionsService($this->defaultPPRPlugin);
        $component = 'pprPlugin.services.SubmissionActionsHandler';

        $result = $target->addPPRSubmissionActionsHandler('LoadComponentHandler', [&$component]);
        $this->assertEquals(true, $result);
        $this->assertEquals('plugins.generic.pprOjsPlugin.services.submission.SubmissionActionsHandler', $component);
    }

    public function test_addPPRSubmissionActionsHandler_should_not_update_component_when_it_is_not_SubmissionActionsHandler() {
        $target = new PPRSubmissionActionsService($this->defaultPPRPlugin);
        $component = 'pprPlugin.services.OtherComponent';

        $result = $target->addPPRSubmissionActionsHandler('LoadComponentHandler', [&$component]);
        $this->assertEquals(false, $result);
        $this->assertEquals('pprPlugin.services.OtherComponent', $component);
    }

    public function test_addActionSubmissionButton_should_update_template_manager_with_close_action_when_it_is_editorialLinkActions_template_and_submission_status_is_queued() {
        $this->check_addActionSubmissionButton(STATUS_QUEUED, 'close');
    }

    public function test_addActionSubmissionButton_should_update_template_manager_with_open_action_when_it_is_editorialLinkActions_template_and_submission_status_not_queued() {
        $this->check_addActionSubmissionButton(STATUS_PUBLISHED, 'open');
    }

    public function test_addActionSubmissionButton_should_not_update_template_manager_when_it_is_not_editorialLinkActions_template() {
        $target = new PPRSubmissionActionsService($this->defaultPPRPlugin);
        $template = 'otherTemplate.tpl';

        $result = $target->addActionSubmissionButton('TemplateManager::fetch', ['template_name', $template]);
        $this->assertEquals(false, $result);
    }

    public function test_sendSubmissionApprovedEmail_should_send_email() {
        $form = $this->createExternalReviewFormWithSubmission();
        $this->getDispatcherMock()->expects($this->once())->method('url')->willReturn('http://submission/url');

        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $authors = [$this->getTestUtil()->createUser($this->getRandomId(), 'Santana', 'Antonio')];
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionAuthors')->with($form->getSubmission()->getId())->willReturn($authors);

        $submissionTemplate = $this->createMock(SubmissionMailTemplate::class);
        $objectFactory->expects($this->once())->method('submissionMailTemplate')->with($form->getSubmission(), 'PPR_SUBMISSION_APPROVED')->willReturn($submissionTemplate);
        $submissionTemplate->expects($this->once())->method('addRecipient')->with('Santana@email.com', 'Santana');
        $submissionTemplate->expects($this->once())->method('assignParams')->with([
            'authorFirstName' => 'Antonio',
            'authorFullName' => 'Santana',
            'submissionUrl' => 'http://submission/url',
            ]);
        $submissionTemplate->expects($this->once())->method('send');

        $target = new PPRSubmissionActionsService($this->defaultPPRPlugin, $objectFactory);
        $target->sendSubmissionApprovedEmail('initiateexternalreviewform::execute', [$form]);
    }

    public function test_sendSubmissionApprovedEmail_should_not_send_email_if_no_authors() {
        $form = $this->createExternalReviewFormWithSubmission();

        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionAuthors')->with($form->getSubmission()->getId())->willReturn(null);
        $objectFactory->expects($this->never())->method('submissionMailTemplate');


        $target = new PPRSubmissionActionsService($this->defaultPPRPlugin, $objectFactory);
        $target->sendSubmissionApprovedEmail('initiateexternalreviewform::execute', [$form]);
    }

    private function check_addActionSubmissionButton($submissionStatus, $expectedActionType) {
        $target = new PPRSubmissionActionsService($this->defaultPPRPlugin);
        $template = 'workflow/editorialLinkActions.tpl';

        $templateManager = TemplateManager::getManager();
        $templateManager->setData(['submissionStatus' => $submissionStatus]);

        $result = $target->addActionSubmissionButton('TemplateManager::fetch', [$templateManager, $template]);
        $this->assertEquals(false, $result);
        $this->assertEquals($expectedActionType, $templateManager->getTemplateVars('pprActionType'));
        $this->assertInstanceOf(LinkAction::class, $templateManager->getTemplateVars('pprAction'));
        $this->assertEquals('ppr_submission_action', $templateManager->getTemplateVars('pprAction')->getId());
        $this->assertEquals(__("submission.$expectedActionType.button.title"), $templateManager->getTemplateVars('pprAction')->getTitle());
    }

    private function createExternalReviewFormWithSubmission() {
        $submission = $this->getTestUtil()->createSubmission($this->getRandomId());

        $form = $this->createMock(InitiateExternalReviewForm::class);
        $form->method('getSubmission')->willReturn($submission);
        return $form;
    }
}