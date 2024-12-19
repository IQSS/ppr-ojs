<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.email.PPRFirstNameEmailService');

import('lib.pkp.controllers.grid.users.reviewer.form.ThankReviewerForm');
import('lib.pkp.controllers.grid.users.reviewer.form.ReviewReminderForm');

class PPRFirstNameEmailServiceTest extends PPRTestCase {

    const CONTEXT_ID = 99887766;

    const SUPPORTED_COMPONENT_NAME = 'grid.users.stageParticipant.StageParticipantGridHandler';
    const SUPPORTED_METHOD_NAME = 'fetchTemplateBody';

    private $defaultPPRPlugin;
    private $defaultEmailKey;

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, []);
        $this->defaultEmailKey = PPRFirstNameEmailService::SUPPORTED_EMAILS[array_rand(PPRFirstNameEmailService::SUPPORTED_EMAILS)];
    }

    public function test_register_should_not_register_any_hooks_when_firstNameEmailEnabled_is_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['firstNameEmailEnabled' => false]);
        $target = new PPRFirstNameEmailService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_service_hooks_when_firstNameEmailEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['firstNameEmailEnabled' => true]);
        $target = new PPRFirstNameEmailService($pprPluginMock);
        $target->register();

        $this->assertEquals(9, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('Mail::send')));
        $this->assertEquals(1, count($this->getHooks('reviewreminderform::display')));
        $this->assertEquals(1, count($this->getHooks('thankreviewerform::display')));
        $this->assertEquals(1, count($this->getHooks('sendreviewsform::display')));
        $this->assertEquals(1, count($this->getHooks('TemplateManager::fetch')));
        $this->assertEquals(1, count($this->getHooks('advancedsearchreviewerform::display')));
        $this->assertEquals(1, count($this->getHooks('createreviewerform::display')));
        $this->assertEquals(1, count($this->getHooks('createreviewerform::execute')));
        $this->assertEquals(1, count($this->getHooks('LoadComponentHandler')));
    }

    public function test_isEmailSupported_returns_true_for_expected_emails() {
        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin);
        foreach (PPRFirstNameEmailService::SUPPORTED_EMAILS as $emailTemplateName) {
            $this->assertTrue($target->isEmailSupported($emailTemplateName));
        }
    }

    public function test_isSupportedTemplate_returns_true_for_expected_templates() {
        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin);
        foreach (PPRFirstNameEmailService::SUPPORTED_TEMPLATES as $supportedTemplateName => $emailBodyVariableName) {
            $this->assertTrue($target->isTemplateSupported($supportedTemplateName));
            $this->assertNotNull($emailBodyVariableName);
        }
    }

    public function test_addPPRStageParticipantGridHandler_should_not_update_component_name_when_component_is_not_supported() {
        // SET VALUES TO TRIGGER FEATURE
        $componentName = 'grid.users.otherComponent';
        $methodName = self::SUPPORTED_METHOD_NAME;
        $this->getRequestMock()->method('getUserVar')->with('template')->willReturn($this->defaultEmailKey);

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $this->getTestUtil()->createObjectFactory());
        $result = $target->addPPRStageParticipantGridHandler('LoadComponentHandler', [&$componentName, &$methodName]);
        $this->assertEquals(false, $result);
        $this->assertEquals('grid.users.otherComponent', $componentName);
    }

    public function test_addPPRStageParticipantGridHandler_should_not_update_component_name_when_method_is_not_supported() {
        // SET VALUES TO TRIGGER FEATURE
        $componentName = self::SUPPORTED_COMPONENT_NAME;
        $methodName = 'otherMethod';
        $this->getRequestMock()->method('getUserVar')->with('template')->willReturn($this->defaultEmailKey);

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $this->getTestUtil()->createObjectFactory());
        $result = $target->addPPRStageParticipantGridHandler('LoadComponentHandler', [&$componentName, &$methodName]);
        $this->assertEquals(false, $result);
        $this->assertEquals(self::SUPPORTED_COMPONENT_NAME, $componentName);
    }

    public function test_addPPRStageParticipantGridHandler_should_not_update_component_name_when_template_is_not_supported() {
        // SET VALUES TO TRIGGER FEATURE
        $componentName = self::SUPPORTED_COMPONENT_NAME;
        $methodName = self::SUPPORTED_METHOD_NAME;
        $this->getRequestMock()->method('getUserVar')->with('template')->willReturn('other_template');

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $this->getTestUtil()->createObjectFactory());
        $result = $target->addPPRStageParticipantGridHandler('LoadComponentHandler', [&$componentName, &$methodName]);
        $this->assertEquals(false, $result);
        $this->assertEquals(self::SUPPORTED_COMPONENT_NAME, $componentName);
    }

    public function test_addPPRStageParticipantGridHandler_should_update_component_name_when_is_expected_component_with_method_and_template_is_supported() {
        // SET VALUES TO TRIGGER FEATURE
        $componentName = self::SUPPORTED_COMPONENT_NAME;
        $methodName = self::SUPPORTED_METHOD_NAME;
        $this->getRequestMock()->method('getUserVar')->with('template')->willReturn($this->defaultEmailKey);

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $this->getTestUtil()->createObjectFactory());
        $result = $target->addPPRStageParticipantGridHandler('LoadComponentHandler', [&$componentName, &$methodName]);
        $this->assertEquals(true, $result);
        $this->assertEquals('plugins.generic.pprOjsPlugin.services.email.PPRStageParticipantGridHandler', $componentName);
    }

    public function test_addFirstNamesToEmailTemplate_should_not_update_mail_template_when_not_supported_template() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $mailTemplate = $this->createSubmissionEmailTemplate('not_supported');

        $mailTemplate->expects($this->never())->method('addPrivateParam');
        $objectFactory->expects($this->never())->method('submissionUtil');

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $result = $target->addFirstNamesToEmailTemplate('Mail::send', [$mailTemplate]);
        $this->assertEquals(false, $result);
    }

    public function test_addFirstNamesToEmailTemplate_should_delegate_to_pprFirstNamesManagementService() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $mailTemplate = $this->createSubmissionEmailTemplate($this->defaultEmailKey);
        $reviewerId = $this->getRandomId();
        $mailTemplate->method('getData')->with('reviewerId')->willReturn($reviewerId);

        $objectFactory->expects($this->atLeastOnce())->method('firstNamesManagementService');
        $objectFactory->firstNamesManagementService()->expects($this->once())->method('addFirstNamesToEmailTemplate')->with($mailTemplate);

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $result = $target->addFirstNamesToEmailTemplate('Mail::send', [$mailTemplate]);
        $this->assertEquals(false, $result);
    }

    public function test_addFirstNameLabelsToAdvancedSearchReviewerForm_should_delegate_to_pprFirstNamesManagementService() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();

        $objectFactory->expects($this->atLeastOnce())->method('firstNamesManagementService');
        $objectFactory->firstNamesManagementService()->expects($this->once())->method('addFirstNameLabelsToTemplate');

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $result = $target->addFirstNameLabelsToAdvancedSearchReviewerForm('advancedsearchreviewerform::display', [null]);
        $this->assertEquals(false, $result);
    }

    public function test_addFirstNamesToThankReviewerForm_should_update_form_message_variable_with_firstNamesManagementService_result() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $form = $this->createMock(ThankReviewerForm::class);
        $this->createReviewFormWithReview($form, $objectFactory);

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $result = $target->addFirstNamesToThankReviewerForm('thankreviewerform::display', [$form]);
        $this->assertEquals(false, $result);
    }

    public function test_addFirstNamesToReviewReminderForm_should_update_form_message_variable_with_firstNamesManagementService_result() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $form = $this->createMock(ReviewReminderForm::class);
        $this->createReviewFormWithReview($form, $objectFactory);

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $result = $target->addFirstNamesToReviewReminderForm('reviewreminderform::display', [$form]);
        $this->assertEquals(false, $result);
    }

    public function test_addFirstNamesToSendReviewsForm_should_update_form_message_variable_with_firstNamesManagementService_result() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $form = $this->createMock(SendReviewsForm::class);
        $submission = $this->getTestUtil()->createSubmission();
        $form->method('getSubmission')->willReturn($submission);
        $form->expects($this->once())->method('getData')->with('personalMessage')->willReturn('message');
        $objectFactory->firstNamesManagementService()->expects($this->once())->method('replaceFirstNames')->with('message', $submission)->willReturn('updatedMessage');
        $form->expects($this->once())->method('setData')->with('personalMessage', 'updatedMessage');

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $result = $target->addFirstNamesToSendReviewsForm('sendreviewsform::display', [$form]);
        $this->assertEquals(false, $result);
    }

    public function test_replaceFirstNameInTemplateText_should_update_template_email_variable_with_firstNamesManagementService_result() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $submission = $this->getTestUtil()->createSubmission($this->getRandomId());
        $this->getRequestMock()->getRouter()->getHandler()->expects($this->once())
            ->method('getAuthorizedContextObject')->with(ASSOC_TYPE_SUBMISSION)->willReturn($submission);
        $templateManager = TemplateManager::getManager();
        foreach (PPRFirstNameEmailService::SUPPORTED_TEMPLATES as $templateName => $variableName) {
            $message = strval($this->getRandomId());
            $templateManager->setData([$variableName => $message]);
            $updatedMessage = 'updated' . $message;
            $objectFactory->firstNamesManagementService()->expects($this->once())
                ->method('replaceFirstNames')->with($message, $submission)->willReturn($updatedMessage);

            $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
            $result = $target->replaceFirstNameInTemplateText('TemplateManager::fetch', [$templateManager, $templateName]);
            $this->assertEquals($updatedMessage, TemplateManager::getManager()->getTemplateVars($variableName));
            $this->assertEquals(false, $result);
        }
    }

    private function createSubmissionEmailTemplate($emailKey, $createSubmission = true) {
        $submissionMailTemplate = $this->createMock(SubmissionMailTemplate::class);
        if ($createSubmission) {
            $submission = $this->getTestUtil()->createSubmissionWithAuthors('AuthorName');
            $submissionMailTemplate->submission = $submission;
        }
        $submissionMailTemplate->emailKey = $emailKey;
        return $submissionMailTemplate;
    }

    private function createReviewFormWithReview($form, $objectFactory) {
        $submission = $this->getTestUtil()->createSubmissionWithAuthors('AuthorName');
        $review = $this->getTestUtil()->createReview();
        $review->method('getSubmissionId')->willReturn($submission->getId());

        $form->method('getReviewAssignment')->willReturn($review);
        $form->expects($this->once())->method('getData')->with('message')->willReturn('message');
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmission')->with($review->getSubmissionId())->willReturn($submission);
        $objectFactory->firstNamesManagementService()->expects($this->once())->method('replaceFirstNames')->with('message', $submission, $review->getReviewerId())->willReturn('updatedMessage');
        $form->expects($this->once())->method('setData')->with('message', 'updatedMessage');
    }
}