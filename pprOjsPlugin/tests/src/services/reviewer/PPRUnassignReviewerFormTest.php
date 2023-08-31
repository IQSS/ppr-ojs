<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('tests.src.mocks.PPRServicesProviderMock');
import('services.reviewer.PPRUnassignReviewerForm');

import('classes.submission.Submission');
import('lib.pkp.classes.submission.reviewAssignment.ReviewAssignment');
import('lib.pkp.classes.submission.reviewRound.ReviewRound');
import('lib.pkp.classes.mail.EmailTemplate');

use \PKP\Services\PKPEmailTemplateService;

class PPRUnassignReviewerFormTest extends PPRTestCase {

    public function setUp(): void {
        parent::setUp();
    }

    public function test_getEmailKey_should_select_PPR_REQUESTED_REVIEWER_UNASSIGN_email_template_when_dateConfirmed_is_null() {
        $reviewAssignment = $this->createMock(ReviewAssignment::class);
        $reviewRound = $this->createMock(ReviewRound::class);
        $submission = $this->createMock(Submission::class);
        $reviewAssignment->expects($this->once())->method('getDateConfirmed')->willReturn(null);
        $reviewRound->expects($this->never())->method($this->anything());
        $submission->expects($this->never())->method($this->anything());

        $target = new PPRUnassignReviewerForm($reviewAssignment, $reviewRound, $submission);
        $this->assertEquals('PPR_REQUESTED_REVIEWER_UNASSIGN', $target->getEmailKey());
    }

    public function test_getEmailKey_should_select_PPR_CONFIRMED_REVIEWER_UNASSIGN_email_template_when_dateConfirmed_is_set() {
        $reviewAssignment = $this->createMock(ReviewAssignment::class);
        $reviewRound = $this->createMock(ReviewRound::class);
        $submission = $this->createMock(Submission::class);
        $reviewAssignment->expects($this->once())->method('getDateConfirmed')->willReturn(Core::getCurrentDate());
        $reviewRound->expects($this->never())->method($this->anything());
        $submission->expects($this->never())->method($this->anything());

        $target = new PPRUnassignReviewerForm($reviewAssignment, $reviewRound, $submission);
        $this->assertEquals('PPR_CONFIRMED_REVIEWER_UNASSIGN', $target->getEmailKey());
    }

    public function test_initData_should_replace_reviewerFirstName_in_the_email_template() {
        $reviewAssignment = $this->createMock(ReviewAssignment::class);
        $reviewRound = $this->createMock(ReviewRound::class);
        $submission = $this->createMock(Submission::class);
        $reviewerId = $this->getRandomId();
        $reviewAssignment->method('getReviewerId')->willReturn($reviewerId);

        $userDao = $this->createMock(UserDAO::class);
        $reviewer = $this->createMock(User::class);
        $reviewer->expects($this->once())->method('getLocalizedGivenName')->willReturn('firstName');
        $userDao->method('getById')->with($reviewerId)->willReturn($reviewer);
        DAORegistry::registerDAO('UserDAO', $userDao);

        $emailServiceMock = $this->createMock(PKPEmailTemplateService::class);
        $emailTemplate = new EmailTemplate();
        $emailTemplate->setData('body', 'email template body: {$reviewerFirstName}', 'en_US');
        $emailServiceMock->expects($this->once())->method('getByKey')->with($this->anything())->willReturn($emailTemplate);

        $this->servicesRegister(['emailTemplate' => $emailServiceMock]);


        $target = new PPRUnassignReviewerForm($reviewAssignment, $reviewRound, $submission);
        $target->initData();

        $this->assertEquals('email template body: firstName', $target->getData('personalMessage'));
    }

}