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

    public function test_initData_should_add_reviewer_editor_and_author_names_to_email_template() {
        $this->assert_initData(
            'email template body: Reviewer({$reviewerFirstName}, {$reviewerName}) - Editor({$editorFirstName}, {$editorName}) - Author({$authorFirstName}, {$authorName})',
            'email template body: Reviewer(reviewerFirstName, reviewerFullName) - Editor(editorFirstName, editorFullName) - Author(authorFirstName, authorFullName)',
            true);
    }

    public function test_initData_should_add_handle_missing_editor_and_author() {
        $missingName = __('ppr.user.missing.name');
        $this->assert_initData(
            'email template body: Reviewer({$reviewerFirstName}, {$reviewerName}) - Editor({$editorFirstName}, {$editorName}) - Author({$authorFirstName}, {$authorName})',
            "email template body: Reviewer(reviewerFirstName, reviewerFullName) - Editor($missingName, $missingName) - Author($missingName, $missingName)",
        false);
    }

    public function assert_initData($emailBody, $expectedText, $addAuthorAndEditor) {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $reviewAssignment = $this->createMock(ReviewAssignment::class);
        $reviewRound = $this->createMock(ReviewRound::class);
        $submission = $this->createMock(Submission::class);
        $reviewerId = $this->getRandomId();
        $reviewAssignment->method('getReviewerId')->willReturn($reviewerId);

        $this->addUsers($objectFactory, $reviewerId, $addAuthorAndEditor);

        $emailServiceMock = $this->createMock(PKPEmailTemplateService::class);
        $emailTemplate = new EmailTemplate();
        $emailTemplate->setData('body', $emailBody, 'en_US');
        $emailServiceMock->expects($this->once())->method('getByKey')->with($this->anything())->willReturn($emailTemplate);

        $this->servicesRegister(['emailTemplate' => $emailServiceMock]);

        $target = new PPRUnassignReviewerForm($reviewAssignment, $reviewRound, $submission, $objectFactory);
        $target->initData();

        $this->assertEquals($expectedText, $target->getData('personalMessage'));
    }

    private function addUsers($objectFactory, $reviewerId, $addAuthorAndEditor = true) {
        $userDao = $this->createMock(UserDAO::class);
        $reviewer = $this->getTestUtil()->createUser($this->getRandomId(), 'reviewerFullName', 'reviewerFirstName');
        $userDao->method('getById')->with($reviewerId)->willReturn($reviewer);
        DAORegistry::registerDAO('UserDAO', $userDao);

        $editors = $addAuthorAndEditor ? [$this->getTestUtil()->createUser($this->getRandomId(), 'editorFullName', 'editorFirstName')] : [];
        $authors = $addAuthorAndEditor ? [$this->getTestUtil()->createUser($this->getRandomId(), 'authorFullName', 'authorFirstName')] : [];

        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionEditors')->willReturn($editors);
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionAuthors')->willReturn($authors);
    }

}