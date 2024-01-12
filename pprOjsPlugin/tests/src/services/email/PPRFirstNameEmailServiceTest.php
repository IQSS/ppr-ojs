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

    public function test_getReviewer_should_use_template_reviewerId_if_set() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey);
        $reviewerId = $this->getRandomId();
        $mailTemplate->method('getData')->with('reviewerId')->willReturn($reviewerId);
        $reviewer = $this->addReviewer($objectFactory, $reviewerId);

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $result = $target->getReviewer($mailTemplate);
        $this->assertEquals($reviewer, $result);
    }

    public function test_getReviewer_should_use_request_reviewAssignmentId_when_template_reviewerId_not_set() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey);
        $mailTemplate->method('getData')->with('reviewerId')->willReturn(null);

        $reviewAssignmentId = $this->getRandomId();
        $this->getRequestMock()->expects($this->once())->method('getUserVar')->with('reviewAssignmentId')->willReturn($reviewAssignmentId);
        $reviewer =$this->getTestUtil()->createUser($this->getRandomId(), 'Reviewer');
        $objectFactory->submissionUtil()->expects($this->once())->method('getReviewer')->with($reviewAssignmentId)->willReturn($reviewer);

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $result = $target->getReviewer($mailTemplate);
        $this->assertEquals($reviewer, $result);
    }

    public function test_addFirstName_should_not_update_mail_template_when_not_supported_template() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $mailTemplate = $this->createSubmissionEmailTemplate('not_supported');

        $mailTemplate->expects($this->never())->method('addPrivateParam');
        $objectFactory->expects($this->never())->method('submissionUtil');

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $target->addFirstName('Mail::send', [$mailTemplate]);
    }

    public function test_addFirstName_should_not_update_mail_template_when_no_submission_provided() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, false);

        $mailTemplate->expects($this->never())->method('addPrivateParam');
        $objectFactory->expects($this->never())->method('submissionUtil');

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $target->addFirstName('Mail::send', [$mailTemplate]);
    }

    public function test_addFirstName_should_add_author_and_editor_names() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey);
        $this->addEditorAndAuthor($objectFactory);
        $reviewerId = $this->getRandomId();
        $mailTemplate->method('getData')->with('reviewerId')->willReturn($reviewerId);
        $this->addReviewer($objectFactory, $reviewerId);

        $mailTemplate->expects($this->exactly(9))->method('addPrivateParam')
            ->withConsecutive(
                ['{$authorName}', 'authorFullName'], ['{$authorFullName}', 'authorFullName'], ['{$authorFirstName}', 'authorFirstName'],
                ['{$editorName}', 'editorFullName'], ['{$editorFullName}', 'editorFullName'], ['{$editorFirstName}', 'editorFirstName'],
                ['{$reviewerName}', 'reviewerFullName'], ['{$reviewerFullName}', 'reviewerFullName'], ['{$reviewerFirstName}', 'reviewerFirstName']);
        $objectFactory->expects($this->atLeastOnce())->method('submissionUtil');

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
        $target->addFirstName('Mail::send', [$mailTemplate]);
    }

    public function test_addFirstName_should_handle_missing_author_editor_and_reviewer() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey);
        $this->addEditorAndAuthor($objectFactory, true);

        $missingName = __('ppr.user.missing.name');
        $mailTemplate->expects($this->exactly(9))->method('addPrivateParam')
            ->withConsecutive(
                ['{$authorName}', $missingName], ['{$authorFullName}', $missingName], ['{$authorFirstName}', $missingName],
                ['{$editorName}', $missingName], ['{$editorFullName}', $missingName], ['{$editorFirstName}', $missingName],
                ['{$reviewerName}', $missingName], ['{$reviewerFullName}', $missingName], ['{$reviewerFirstName}', $missingName]);
        $objectFactory->expects($this->atLeastOnce())->method('submissionUtil');

        $target = new PPRFirstNameEmailService($this->defaultPPRPlugin, $objectFactory);
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

    private function addEditorAndAuthor($objectFactory, $emptyUsers = false) {
        $editors = $emptyUsers ? [] : [$this->getTestUtil()->createUser($this->getRandomId(), 'editorFullName', 'editorFirstName')];
        $authors = $emptyUsers ? [] : [$this->getTestUtil()->createUser($this->getRandomId(), 'authorFullName', 'authorFirstName')];

        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionEditors')->willReturn($editors);
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionAuthors')->willReturn($authors);
    }

    private function addReviewer($objectFactory, $reviewerId) {
        $reviewer =$this->getTestUtil()->createUser($this->getRandomId(), 'reviewerFullName', 'reviewerFirstName');
        $objectFactory->submissionUtil()->expects($this->once())->method('getUser')->with($reviewerId)->willReturn($reviewer);
        return $reviewer;
    }
}