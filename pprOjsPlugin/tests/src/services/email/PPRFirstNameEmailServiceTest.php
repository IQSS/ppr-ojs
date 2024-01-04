<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.email.PPRFirstNameEmailService');

class PPRFirstNameEmailServiceTest extends PPRTestCase {

    const CONTEXT_ID = 991234;

    private $defaultPPRPlugin;
    private $dafaultEmailKey;

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, []);
        $this->dafaultEmailKey = PPRFirstNameEmailService::SUPPORTED_TEMPLATES[array_rand(PPRFirstNameEmailService::SUPPORTED_TEMPLATES)];
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

        $this->assertEquals(1, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('Mail::send')));
    }

    public function test_isSupportedTemplate_returns_true_for_expected_templates() {
        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin);
        foreach (PPRFirstNameEmailService::SUPPORTED_TEMPLATES as $supportedTemplate) {
            $mailTemplate = $this->createSubmissionEmailTemplate($supportedTemplate);
            $this->assertTrue($target->isTemplateSupported($mailTemplate));
        }
    }

    public function test_addFirstName_should_not_update_mail_template_when_not_supported_template() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $mailTemplate = $this->createSubmissionEmailTemplate('not_supported');

        $mailTemplate->expects($this->never())->method('addPrivateParam');
        $objectFactory->expects($this->never())->method('submissionUtil');
        $target->addFirstName('Mail::send', [$mailTemplate]);
    }

    public function test_addFirstName_should_not_update_mail_template_when_no_submission_provided() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, false);

        $mailTemplate->expects($this->never())->method('addPrivateParam');
        $objectFactory->expects($this->never())->method('submissionUtil');
        $target->addFirstName('Mail::send', [$mailTemplate]);
    }

    public function test_addFirstName_should_add_author_and_editor_names() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey);
        $this->addUsers($objectFactory);

        $mailTemplate->expects($this->exactly(4))->method('addPrivateParam')
            ->withConsecutive(
                ['{$authorName}', 'authorFullName'], ['{$authorFirstName}', 'authorFirstName'],
                ['{$editorName}', 'editorFullName'], ['{$editorFirstName}', 'editorFirstName']);
        $objectFactory->expects($this->atLeastOnce())->method('submissionUtil');
        $target->addFirstName('Mail::send', [$mailTemplate]);
    }

    public function test_addFirstName_should_handle_missing_author_and_editor() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey);
        $this->addUsers($objectFactory, true);

        $mailTemplate->expects($this->exactly(4))->method('addPrivateParam')
            ->withConsecutive(
                ['{$authorName}', 'N/A'], ['{$authorFirstName}', 'N/A'],
                ['{$editorName}', 'N/A'], ['{$editorFirstName}', 'N/A']);
        $objectFactory->expects($this->atLeastOnce())->method('submissionUtil');
        $target->addFirstName('Mail::send', [$mailTemplate]);
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

    private function addUsers($objectFactory, $emptyUsers = false) {
        $editors = $emptyUsers ? [] : [$this->getTestUtil()->createUser($this->getRandomId(), 'editorFullName', 'editorFirstName')];
        $authors = $emptyUsers ? [] : [$this->getTestUtil()->createUser($this->getRandomId(), 'authorFullName', 'authorFirstName')];

        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionEditors')->willReturn($editors);
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionAuthors')->willReturn($authors);
    }
}