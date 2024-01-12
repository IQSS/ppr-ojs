<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.reviewer.PPRReviewSubmittedService');
import('util.PPRObjectFactory');

import('lib.pkp.classes.core.Dispatcher');
import('lib.pkp.classes.context.Context');
import('lib.pkp.classes.linkAction.LinkAction');
import('classes.submission.reviewer.form.ReviewerReviewStep3Form');
import('lib.pkp.classes.submission.reviewAssignment.ReviewAssignment');
import('lib.pkp.classes.security.AccessKeyManager');
import('lib.pkp.classes.security.UserGroup');
import('lib.pkp.classes.security.UserGroupDAO');
import('lib.pkp.classes.stageAssignment.StageAssignmentDAO');

class PPRReviewSubmittedServiceTest extends PPRTestCase {

    const CONTEXT_ID = 99123;
    private $defaultPPRPlugin;

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, ['accessKeyLifeTime' => 99]);
    }

    public function test_register_should_not_register_any_hooks_when_service_toggles_are_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['reviewSubmittedEmailEnabled' => false], true);
        $target = new PPRReviewSubmittedService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_service_hooks_when_reviewSubmittedEmailEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['reviewSubmittedEmailEnabled' => true], false);
        $target = new PPRReviewSubmittedService($pprPluginMock);
        $target->register();

        $this->assertEquals(1, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('reviewerreviewstep3form::execute')));
    }

    public function test_sendReviewSubmittedConfirmationEmail_should_send_email() {
        $reviewerId = $this->getRandomId();
        $reviewerName = 'Sebastian';
        $form = $this->createReviewFormWithReviewer($reviewerId);

        $context = $this->createMock(Context::class);
        $context->method('getData')->withConsecutive(['contactEmail'], ['contactName'])
            ->willReturnOnConsecutiveCalls('context@email.com', 'ContextName');
        $context->expects($this->once())->method('getLocalizedDateFormatShort')->willReturn('%Y-%m-%d');
        $context->method('getId')->willReturn(self::CONTEXT_ID);
        $this->getRequestMock()->expects($this->once())->method('getContext')->willReturn($context);

        $objectFactory = $this->setSendEmailAssertions($context, $form->getReviewerSubmission(), $reviewerId, $reviewerName);

        $target = new PPRReviewSubmittedService($this->defaultPPRPlugin, $objectFactory);
        $target->sendReviewSubmittedConfirmationEmail('pkpreviewerreviewstep1form::execute', [$form]);
    }

    public function test_sendReviewSubmittedConfirmationEmail_should_send_email_with_no_editor_information_when_no_editor() {
        $reviewerId = $this->getRandomId();
        $reviewerName = 'Santana';
        $form = $this->createReviewFormWithReviewer($reviewerId);

        $context = $this->createMock(Context::class);
        $context->method('getData')->withConsecutive(['contactEmail'], ['contactName'])
            ->willReturnOnConsecutiveCalls('context@email.com', 'ContextName');
        $context->expects($this->once())->method('getLocalizedDateFormatShort')->willReturn('%Y-%m-%d');
        $context->method('getId')->willReturn(self::CONTEXT_ID);
        $this->getRequestMock()->expects($this->once())->method('getContext')->willReturn($context);

        $objectFactory = $this->setSendEmailAssertions($context, $form->getReviewerSubmission(), $reviewerId, $reviewerName);

        $target = new PPRReviewSubmittedService($this->defaultPPRPlugin, $objectFactory);
        $target->sendReviewSubmittedConfirmationEmail('pkpreviewerreviewstep1form::execute', [$form]);
    }

    public function test_sendReviewSubmittedConfirmationEmail_should_not_send_email_if_reviewer_cannot_be_found() {
        $reviewerId = $this->getRandomId();
        $form = $this->createReviewFormWithReviewer($reviewerId);

        $objectFactory = $this->createMock(PPRObjectFactory::class);
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);
        $submissionUtil->expects($this->once())->method('getUser')->with($reviewerId)->willReturn(null);
        $objectFactory->expects($this->once())->method('submissionUtil')->willReturn($submissionUtil);
        $objectFactory->expects($this->never())->method('submissionMailTemplate');
        $objectFactory->expects($this->never())->method('accessKeyManager');

        $target = new PPRReviewSubmittedService($this->defaultPPRPlugin, $objectFactory);
        $target->sendReviewSubmittedConfirmationEmail('pkpreviewerreviewstep1form::execute', [$form]);
    }

    private function createReviewFormWithReviewer($reviewerId) {
        $submission = $this->getTestUtil()->createSubmissionWithAuthors('AuthorName');
        $review = $this->createMock(ReviewAssignment::class);
        $review->method('getId')->willReturn($this->getRandomId());
        $review->method('getReviewerId')->willReturn($reviewerId);
        $review->method('getDateDue')->willReturn('2099-12-31 00:00:00');


        $form = $this->createMock(ReviewerReviewStep3Form::class);
        $form->method('getReviewAssignment')->willReturn($review);
        $form->method('getReviewerSubmission')->willReturn($submission);
        return $form;
    }

    private function setSendEmailAssertions($context, $submission, $reviewerId, $reviewerName) {
        $objectFactory = $this->createMock(PPRObjectFactory::class);
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);

        $reviewer = null;
        if($reviewerId) {
            $reviewer = $this->getTestUtil()->createUser($reviewerId, $reviewerName, $reviewerName);
        }

        $submissionUtil->expects($this->once())->method('getUser')->with($reviewerId)->willReturn($reviewer);
        $objectFactory->expects($this->once())->method('submissionUtil')->willReturn($submissionUtil);

        $submissionTemplate = $this->createMock(SubmissionMailTemplate::class);
        $objectFactory->expects($this->once())->method('submissionMailTemplate')->with($submission, 'PPR_REVIEW_SUBMITTED')->willReturn($submissionTemplate);
        $submissionTemplate->expects($this->once())->method('setContext')->with($context);
        $submissionTemplate->expects($this->once())->method('setFrom')->with('context@email.com', 'ContextName');
        $submissionTemplate->expects($this->once())->method('addRecipient')->with("$reviewerName@email.com", $reviewerName);
        $submissionTemplate->expects($this->once())->method('assignParams')->with([
            'reviewerFullName' => $reviewerName,
            'reviewerFirstName' => $reviewerName,
            'reviewerUserName' => strtolower($reviewerName),
            'reviewDueDate' => '2099-12-31',
        ]);
        $submissionTemplate->expects($this->once())->method('send');

        return $objectFactory;
    }

}