<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.email.PPREmailContributorsService');

import('classes.article.Author');
import('classes.submission.Submission');
import('lib.pkp.controllers.modals.editorDecision.form.SendReviewsForm');
import('lib.pkp.classes.mail.SubmissionMailTemplate');

class PPREmailContributorsServiceTest extends PPRTestCase {

    const CONTEXT_ID = 99543;

    private $defaultPPRPlugin;
    private $dafaultEmailKey;

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, []);
        $this->dafaultEmailKey = PPREmailContributorsService::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES[array_rand(PPREmailContributorsService::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES)];

    }

    public function test_register_should_not_register_any_hooks_when_editorialDecisionsEmailRemoveContributorsEnabled_is_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['emailContributorsEnabled' => false]);
        $target = new PPREmailContributorsService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_service_hooks_when_editorialDecisionsEmailRemoveContributorsEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['emailContributorsEnabled' => true]);
        $target = new PPREmailContributorsService($pprPluginMock);
        $target->register();

        $this->assertEquals(2, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('sendreviewsform::display')));
        $this->assertEquals(1, count($this->getHooks('Mail::send')));
    }

    public function test_sendReviewsFormDisplay_should_delegate_to_firstNameService_when_author_not_null() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $submission = $this->getTestUtil()->createSubmissionWithAuthors('AuthorPrimaryName', []);
        $submission->method('getData')->with('emailContributors')->willReturn(false);
        $author =  $this->getTestUtil()->createUser($this->getRandomId(), 'AuthorFullName');
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionAuthors')->willReturn([$author]);
        $objectFactory->firstNamesManagementService()->expects($this->exactly(3))->method('replaceFirstNames')
            ->withConsecutive(
                ['personalMessageBody', $submission],
                ['revisionsEmailBody', $submission],
                ['resubmitEmailBody', $submission]
            )->willReturnOnConsecutiveCalls('personalEmailBodyUpdated', 'revisionsEmailBodyUpdated', 'resubmitEmailBodyUpdated');


        $sendReviewForm = $this->createMock(SendReviewsForm::class);
        $sendReviewForm->method('getSubmission')->willReturn($submission);
        $sendReviewForm->method('getData')->with('personalMessage')->willReturn('personalMessageBody');

        TemplateManager::getManager()->setData([
            'revisionsEmail' => 'revisionsEmailBody',
            'resubmitEmail' => 'resubmitEmailBody',
        ]);

        $sendReviewForm->expects($this->exactly(4))
            ->method('setData')->withConsecutive(
                ['authorName', 'AuthorFullName'],
                ['personalMessage', 'personalEmailBodyUpdated'],
                ['revisionsEmail', 'revisionsEmailBodyUpdated'],
                ['resubmitEmail', 'resubmitEmailBodyUpdated'],
            );

        $target = new PPREmailContributorsService($this->defaultPPRPlugin, $objectFactory);
        $target->sendReviewsFormDisplay('sendreviewsform::display', [$sendReviewForm]);
    }

    public function test_sendReviewsFormDisplay_should_set_authorName_with_author_full_name_when_emailContributors_is_false() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $submission = $this->getTestUtil()->createSubmissionWithAuthors('AuthorPrimaryName', ['ContributorName']);
        $submission->method('getData')->with('emailContributors')->willReturn(false);
        $author =  $this->getTestUtil()->createUser($this->getRandomId(), 'AuthorFullName');
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionAuthors')->willReturn([$author]);

        $sendReviewForm = $this->createMock(SendReviewsForm::class);
        $sendReviewForm->method('getSubmission')->willReturn($submission);

        $sendReviewForm->expects($this->exactly(4))
            ->method('setData')->withConsecutive(
                ['authorName', 'AuthorFullName'],
                ['personalMessage', null],
                ['revisionsEmail', null],
                ['resubmitEmail', null],
            );

        $target = new PPREmailContributorsService($this->defaultPPRPlugin, $objectFactory);
        $target->sendReviewsFormDisplay('sendreviewsform::display', [$sendReviewForm]);
    }

    public function test_sendReviewsFormDisplay_should_set_authorName_with_contributors_names_when_emailContributors_is_true() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $submission = $this->getTestUtil()->createSubmissionWithAuthors('AuthorPrimaryName', ['ContributorName']);
        $submission->method('getData')->with('emailContributors')->willReturn(true);
        $author =  $this->getTestUtil()->createUser($this->getRandomId(), 'AuthorFullName');
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionAuthors')->willReturn([$author]);

        $sendReviewForm = $this->createMock(SendReviewsForm::class);
        $sendReviewForm->method('getSubmission')->willReturn($submission);

        $sendReviewForm->expects($this->exactly(4))
            ->method('setData')->withConsecutive(
                ['authorName', 'AuthorPrimaryName, ContributorName'],
                ['personalMessage', null],
                ['revisionsEmail', null],
                ['resubmitEmail', null],
            );

        $target = new PPREmailContributorsService($this->defaultPPRPlugin, $objectFactory);
        $target->sendReviewsFormDisplay('sendreviewsform::display', [$sendReviewForm]);
    }

    public function test_sendReviewsFormDisplay_should_not_set_template_data_when_author_is_null() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $sendReviewForm = $this->createMock(SendReviewsForm::class);
        $submission = $this->getTestUtil()->createSubmissionWithAuthors('AuthorPrimaryName', []);
        $sendReviewForm->method('getSubmission')->willReturn($submission);
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionAuthors')->willReturn([]);

        $sendReviewForm->expects($this->never())->method('setData');

        $target = new PPREmailContributorsService($this->defaultPPRPlugin, $objectFactory);
        $target->sendReviewsFormDisplay('sendreviewsform::display', [$sendReviewForm]);
    }

    public function test_addContributorsToEmailRecipients_should_set_author_as_recipient_when_emailContributors_is_null() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, 'SubmissionAuthor', ['ContributorName'], null);
        $author =  $this->getTestUtil()->createUser($this->getRandomId(), 'AuthorFullName');
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionAuthors')->willReturn([$author]);

        $mailTemplate->expects($this->once())->method('setRecipients')
            ->with(array(['name' => 'AuthorFullName', 'email' => 'AuthorFullName@email.com']));

        $target = new PPREmailContributorsService($this->defaultPPRPlugin, $objectFactory);
        $response = $target->addContributorsToEmailRecipients('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_addContributorsToEmailRecipients_should_set_author_as_recipient_when_emailContributors_is_false() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, 'SubmissionAuthor', ['ContributorName'], false);
        $author =  $this->getTestUtil()->createUser($this->getRandomId(), 'AuthorFullName');
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionAuthors')->willReturn([$author]);

        $mailTemplate->expects($this->once())->method('setRecipients')
            ->with(array(['name' => 'AuthorFullName', 'email' => 'AuthorFullName@email.com']));

        $target = new PPREmailContributorsService($this->defaultPPRPlugin, $objectFactory);
        $response = $target->addContributorsToEmailRecipients('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_addContributorsToEmailRecipients_should_set_author_and_contributors_as_recipients_when_emailContributors_is_true() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, 'SubmissionAuthor', ['ContributorName'], true);
        $author =  $this->getTestUtil()->createUser($this->getRandomId(), 'AuthorFullName');
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionAuthors')->willReturn([$author]);

        $mailTemplate->expects($this->once())->method('setRecipients')
            ->with(array(
                ['name' => 'AuthorFullName', 'email' => 'AuthorFullName@email.com'],
                ['name' => 'SubmissionAuthor', 'email' => 'SubmissionAuthor@email.com'],
                ['name' => 'ContributorName', 'email' => 'ContributorName@email.com'],
                ));

        $target = new PPREmailContributorsService($this->defaultPPRPlugin, $objectFactory);
        $response = $target->addContributorsToEmailRecipients('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_addContributorsToEmailRecipients_should_not_duplicate_recipients_when_emailContributors_is_true() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, 'SubmissionAuthor', ['ContributorName'], true);
        $author =  $this->getTestUtil()->createUser($this->getRandomId(), 'SubmissionAuthor');
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionAuthors')->willReturn([$author]);

        $mailTemplate->expects($this->once())->method('setRecipients')
            ->with(array(
                ['name' => 'SubmissionAuthor', 'email' => 'SubmissionAuthor@email.com'],
                ['name' => 'ContributorName', 'email' => 'ContributorName@email.com'],
            ));

        $target = new PPREmailContributorsService($this->defaultPPRPlugin, $objectFactory);
        $response = $target->addContributorsToEmailRecipients('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_addContributorsToEmailRecipients_should_not_set_recipients_when_author_is_null() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, 'Author Name', [], false);
        $objectFactory->submissionUtil()->expects($this->once())->method('getSubmissionAuthors')->willReturn([]);

        $mailTemplate->expects($this->never())->method('setRecipients');

        $target = new PPREmailContributorsService($this->defaultPPRPlugin, $objectFactory);
        $response = $target->addContributorsToEmailRecipients('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_addContributorsToEmailRecipients_should_not_update_template_when_email_key_is_not_known() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $mailTemplate = $this->createSubmissionEmailTemplate('not_known_email', null, [], false);

        $this->assertEquals(false, in_array($mailTemplate->emailKey, PPREmailContributorsService::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES));

        $objectFactory->expects($this->never())->method($this->anything());
        $mailTemplate->expects($this->never())->method($this->anything());

        $target = new PPREmailContributorsService($this->defaultPPRPlugin, $objectFactory);
        $response = $target->addContributorsToEmailRecipients('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_expected_known_templates() {
        $expectedTemplates =  ['SUBMISSION_ACK', 'EDITOR_DECISION_REVISIONS', 'EDITOR_DECISION_RESUBMIT', 'EDITOR_DECISION_INITIAL_DECLINE', 'EDITOR_DECISION_DECLINE', 'PPR_REVIEW_SENT_AUTHOR'];
        foreach ($expectedTemplates as $expectedTemplate) {
            $this->assertEquals(true, in_array($expectedTemplate, PPREmailContributorsService::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES));
        }

        foreach (PPREmailContributorsService::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES as $knownTemplate) {
            $this->assertEquals(true, in_array($knownTemplate, $expectedTemplates));
        }
    }

    private function createSubmissionEmailTemplate($emailKey, $primaryAuthorName, $contributorsNames, $emailContributors) {
        $submission = $this->getTestUtil()->createSubmissionWithAuthors($primaryAuthorName, $contributorsNames);
        $submission->method('getData')->with('emailContributors')->willReturn($emailContributors);
        $submissionMailTemplate = $this->createMock(SubmissionMailTemplate::class);
        $submissionMailTemplate->submission = $submission;
        $submissionMailTemplate->emailKey = $emailKey;
        return $submissionMailTemplate;
    }
}