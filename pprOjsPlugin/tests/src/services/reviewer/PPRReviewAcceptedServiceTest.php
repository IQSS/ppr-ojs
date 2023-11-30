<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.reviewer.PPRReviewAcceptedService');
import('util.PPRObjectFactory');

import('lib.pkp.classes.core.Dispatcher');
import('lib.pkp.classes.context.Context');
import('lib.pkp.classes.linkAction.LinkAction');
import('lib.pkp.classes.submission.reviewer.form.PKPReviewerReviewStep1Form');
import('lib.pkp.classes.submission.reviewAssignment.ReviewAssignment');
import('lib.pkp.classes.security.AccessKeyManager');
import('lib.pkp.classes.security.UserGroup');
import('lib.pkp.classes.security.UserGroupDAO');
import('lib.pkp.classes.stageAssignment.StageAssignmentDAO');

class PPRReviewAcceptedServiceTest extends PPRTestCase {

    const CONTEXT_ID = 99123;
    private $defaultPPRPlugin;

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, ['accessKeyLifeTime' => 99]);
    }

    public function test_register_should_not_register_any_hooks_when_service_toggles_are_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['reviewAcceptedEmailEnabled' => false], true);
        $target = new PPRReviewAcceptedService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_service_hooks_when_reviewAcceptedEmailEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['reviewAcceptedEmailEnabled' => true], false);
        $target = new PPRReviewAcceptedService($pprPluginMock);
        $target->register();

        $this->assertEquals(1, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('pkpreviewerreviewstep1form::execute')));
    }

    public function test_sendReviewAcceptedConfirmationEmail_should_send_email() {
        $form = $this->createReviewFormWithReviewer('Santana');

        $this->getDispatcherMock()->method('url')->willReturnOnConsecutiveCalls('http://password/reset', 'http://submission/url');

        $context = $this->createMock(Context::class);
        $context->method('getData')->withConsecutive(['contactEmail'], ['contactName'])
            ->willReturnOnConsecutiveCalls('context@email.com', 'ContextName');
        $context->expects($this->once())->method('getLocalizedDateFormatShort')->willReturn('%Y-%m-%d');
        $context->method('getId')->willReturn(self::CONTEXT_ID);
        $this->getRequestMock()->expects($this->once())->method('getContext')->willReturn($context);

        $objectFactory = $this->setSendEmailAssertions($context, $form->getReviewerSubmission(), 'EditorFull', 'EditorFirst');
        $accessKeyManager = $this->createMock(AccessKeyManager::class);
        $accessKeyManager->expects($this->once())->method('createKey');
        $objectFactory->expects($this->once())->method('accessKeyManager')->willReturn($accessKeyManager);

        $target = new PPRReviewAcceptedService($this->defaultPPRPlugin, $objectFactory);
        $target->sendReviewAcceptedConfirmationEmail('pkpreviewerreviewstep1form::execute', [$form]);
    }

    public function test_sendReviewAcceptedConfirmationEmail_should_send_email_with_no_access_key_when_accessKeyLifeTime_has_not_been_setup() {
        $form = $this->createReviewFormWithReviewer('Santana');

        $this->getDispatcherMock()->method('url')->willReturnOnConsecutiveCalls('http://password/reset', 'http://submission/url');

        $context = $this->createMock(Context::class);
        $context->method('getData')->withConsecutive(['contactEmail'], ['contactName'])
            ->willReturnOnConsecutiveCalls('context@email.com', 'ContextName');
        $context->expects($this->once())->method('getLocalizedDateFormatShort')->willReturn('%Y-%m-%d');
        $context->method('getId')->willReturn(self::CONTEXT_ID);
        $this->getRequestMock()->expects($this->once())->method('getContext')->willReturn($context);

        $objectFactory = $this->setSendEmailAssertions($context, $form->getReviewerSubmission(), 'EditorFull', 'EditorFirst');
        $objectFactory->expects($this->never())->method('accessKeyManager');

        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['accessKeyLifeTime' => 0]);
        $target = new PPRReviewAcceptedService($pprPluginMock, $objectFactory);
        $target->sendReviewAcceptedConfirmationEmail('pkpreviewerreviewstep1form::execute', [$form]);
    }

    public function test_sendReviewAcceptedConfirmationEmail_should_send_email_with_no_editor_information_when_no_editor() {
        $form = $this->createReviewFormWithReviewer('Santana');

        $this->getDispatcherMock()->method('url')->willReturnOnConsecutiveCalls('http://password/reset', 'http://submission/url');

        $context = $this->createMock(Context::class);
        $context->method('getData')->withConsecutive(['contactEmail'], ['contactName'])
            ->willReturnOnConsecutiveCalls('context@email.com', 'ContextName');
        $context->expects($this->once())->method('getLocalizedDateFormatShort')->willReturn('%Y-%m-%d');
        $context->method('getId')->willReturn(self::CONTEXT_ID);
        $this->getRequestMock()->expects($this->once())->method('getContext')->willReturn($context);

        $objectFactory = $this->setSendEmailAssertions($context, $form->getReviewerSubmission(), null, null);
        $accessKeyManager = $this->createMock(AccessKeyManager::class);
        $accessKeyManager->expects($this->once())->method('createKey');
        $objectFactory->expects($this->once())->method('accessKeyManager')->willReturn($accessKeyManager);

        $target = new PPRReviewAcceptedService($this->defaultPPRPlugin, $objectFactory);
        $target->sendReviewAcceptedConfirmationEmail('pkpreviewerreviewstep1form::execute', [$form]);
    }

    public function test_sendReviewAcceptedConfirmationEmail_should_not_send_email_if_reviewer_cannot_be_found() {
        $form = $this->createReviewFormWithReviewer(null);

        $objectFactory = $this->createMock(PPRObjectFactory::class);
        $objectFactory->expects($this->never())->method('submissionMailTemplate');
        $objectFactory->expects($this->never())->method('accessKeyManager');

        $target = new PPRReviewAcceptedService($this->defaultPPRPlugin, $objectFactory);
        $target->sendReviewAcceptedConfirmationEmail('pkpreviewerreviewstep1form::execute', [$form]);
    }

    private function createReviewFormWithReviewer($reviewerName) {
        $submission = $this->getTestUtil()->createSubmissionWithAuthors('AuthorName');
        $review = $this->createMock(ReviewAssignment::class);
        $review->method('getId')->willReturn($this->getRandomId());
        $reviewerId = $this->getRandomId();
        $review->method('getReviewerId')->willReturn($reviewerId);
        $review->method('getDateDue')->willReturn('2099-12-31 00:00:00');

        $reviewer = null;
        if($reviewerName) {
            $reviewer = $this->getTestUtil()->createUser($reviewerId, $reviewerName, $reviewerName);
        }

        $userDao = $this->createMock(UserDAO::class);
        $userDao->expects($this->once())->method('getById')->with($reviewerId)->willReturn($reviewer);
        DAORegistry::registerDAO('UserDAO', $userDao);


        $form = $this->createMock(PKPReviewerReviewStep1Form::class);
        $form->method('getReviewAssignment')->willReturn($review);
        $form->method('getReviewerSubmission')->willReturn($submission);
        return $form;
    }

    private function setSendEmailAssertions($context, $submission, $editorFullName, $editorFirstName) {
        $objectFactory = $this->createMock(PPRObjectFactory::class);
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);

        $editors = [];
        if($editorFullName) {
            $editors[] = $this->getTestUtil()->createUser($this->getRandomId(), $editorFullName, $editorFirstName);
        } else {
            // SET THE EXPECTED VALUES WHEN NO EDITOR IS FOUND
            $editorFullName = 'N/A';
            $editorFirstName = 'N/A';
        }
        $submissionUtil->expects($this->once())->method('getSubmissionEditors')->willReturn($editors);
        $objectFactory->expects($this->once())->method('submissionUtil')->willReturn($submissionUtil);

        $submissionTemplate = $this->createMock(SubmissionMailTemplate::class);
        $objectFactory->expects($this->once())->method('submissionMailTemplate')->with($submission, 'PPR_REVIEW_ACCEPTED')->willReturn($submissionTemplate);
        $submissionTemplate->expects($this->once())->method('setContext')->with($context);
        $submissionTemplate->expects($this->once())->method('setFrom')->with('context@email.com', 'ContextName');
        $submissionTemplate->expects($this->once())->method('addRecipient')->with('Santana@email.com', 'Santana');
        $submissionTemplate->expects($this->once())->method('assignParams')->with([
            'reviewerFullName' => 'Santana',
            'reviewerFirstName' => 'Santana',
            'reviewerUserName' => 'santana',
            'reviewDueDate' => '2099-12-31',
            'editorFullName' => $editorFullName,
            'editorFirstName' => $editorFirstName,
            'passwordResetUrl' => 'http://password/reset',
            'submissionReviewUrl' => 'http://submission/url',
        ]);
        $submissionTemplate->expects($this->once())->method('send');

        return $objectFactory;
    }

}