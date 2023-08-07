<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.email.PPREditorialDecisionsEmailService');

import('classes.article.Author');
import('classes.submission.Submission');
import('lib.pkp.controllers.modals.editorDecision.form.SendReviewsForm');
import('lib.pkp.classes.mail.SubmissionMailTemplate');

class PPREditorialDecisionsEmailServiceTest extends PPRTestCase {

    const CONTEXT_ID = 130;
    const SUBJECT_WITH_TITLE_VAR = 'Subject: {$submissionTitle} Variable';
    private $defaultPPRPlugin;
    private $dafaultEmailKey;

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, []);
        $this->dafaultEmailKey = PPREditorialDecisionsEmailService::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES[0];
    }

    public function test_register_should_not_register_any_hooks_when_editorialDecisionsEmailRemoveContributorsEnabled_is_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['editorialDecisionsEmailRemoveContributorsEnabled' => false]);
        $target = new PPREditorialDecisionsEmailService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_service_hooks_when_editorialDecisionsEmailRemoveContributorsEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['editorialDecisionsEmailRemoveContributorsEnabled' => true]);
        $target = new PPREditorialDecisionsEmailService($pprPluginMock);
        $target->register();

        $this->assertEquals(2, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('sendreviewsform::display')));
        $this->assertEquals(1, count($this->getHooks('Mail::send')));
    }

    public function test_sendReviewsFormDisplay_should_set_primary_author_name_in_template_data_when_primary_author_not_null() {
        $expectedAuthorName = 'First Last';

        $sendReviewForm = $this->createMock(SendReviewsForm::class);
        $sendReviewForm->method('getSubmission')->willReturn($this->createSubmissionWithAuthors($expectedAuthorName, []));
        $sendReviewForm->method('getData')->with('personalMessage')->willReturn('Test: {$authorFullName} template text');

        TemplateManager::getManager()->setData([
            'revisionsEmail' => 'Test: {$authorFullName} template text',
            'resubmitEmail' => 'Test: {$authorFullName} template text',
        ]);

        $sendReviewForm->expects($this->exactly(4))
            ->method('setData')->withConsecutive(
                ['authorName', $expectedAuthorName],
                ['personalMessage', "Test: $expectedAuthorName template text"],
                ['revisionsEmail', "Test: $expectedAuthorName template text"],
                ['resubmitEmail', "Test: $expectedAuthorName template text"],
            );

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $target->sendReviewsFormDisplay('sendreviewsform::display', [$sendReviewForm]);
    }

    public function test_sendReviewsFormDisplay_should_set_first_contributor_name_in_template_data_when_primary_author_is_null() {
        $expectedAuthorName = 'First Contributor';

        $sendReviewForm = $this->createMock(SendReviewsForm::class);
        $sendReviewForm->method('getSubmission')->willReturn($this->createSubmissionWithAuthors(null, [$expectedAuthorName, 'invalid', 'invalid']));
        $sendReviewForm->method('getData')->with('personalMessage')->willReturn('Test: {$authorFullName} template text');

        TemplateManager::getManager()->setData([
            'revisionsEmail' => 'Test: {$authorFullName} template text',
            'resubmitEmail' => 'Test: {$authorFullName} template text',
        ]);

        $sendReviewForm->expects($this->exactly(4))
            ->method('setData')->withConsecutive(
                ['authorName', $expectedAuthorName],
                ['personalMessage', "Test: $expectedAuthorName template text"],
                ['revisionsEmail', "Test: $expectedAuthorName template text"],
                ['resubmitEmail', "Test: $expectedAuthorName template text"],
            );

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $target->sendReviewsFormDisplay('sendreviewsform::display', [$sendReviewForm]);
    }

    public function test_sendReviewsFormDisplay_should_not_set_template_data_when_authors_are_null() {
        $sendReviewForm = $this->createMock(SendReviewsForm::class);
        $sendReviewForm->method('getSubmission')->willReturn($this->createSubmissionWithAuthors(null, []));

        $sendReviewForm->expects($this->never())->method('setData');

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $target->sendReviewsFormDisplay('sendreviewsform::display', [$sendReviewForm]);
    }

    public function test_requestRevisionsUpdateRecipients_should_set_recipients_when_primary_author_not_null() {
        $expectedAuthorName = 'Author Name';
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, self::SUBJECT_WITH_TITLE_VAR, 'title', $expectedAuthorName, []);

        $mailTemplate->expects($this->once())->method('setRecipients')->with(array(['name' => $expectedAuthorName, 'email' => $expectedAuthorName]));

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $target->requestRevisionsUpdateRecipients('Mail::send', [$mailTemplate]);
    }

    public function test_requestRevisionsUpdateRecipients_should_set_first_contributor_as_recipients_when_primary_author_is_null() {
        $expectedAuthorName = 'First Contributor';
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, self::SUBJECT_WITH_TITLE_VAR, 'title', null, [$expectedAuthorName, 'invalid', 'not used']);

        $mailTemplate->expects($this->once())->method('setRecipients')->with(array(['name' => $expectedAuthorName, 'email' => $expectedAuthorName]));

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $target->requestRevisionsUpdateRecipients('Mail::send', [$mailTemplate]);
    }

    public function test_requestRevisionsUpdateRecipients_should_not_set_recipients_when_authors_are_null() {
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, self::SUBJECT_WITH_TITLE_VAR, 'title', null, []);

        $mailTemplate->expects($this->never())->method('setRecipients');

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $target->requestRevisionsUpdateRecipients('Mail::send', [$mailTemplate]);
    }

    public function test_requestRevisionsUpdateRecipients_should_set_update_subject_when_submission_title_variable_is_set() {
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, self::SUBJECT_WITH_TITLE_VAR, 'Submission Title', null, []);

        $mailTemplate->expects($this->once())->method('setSubject')->with('Subject: Submission Title Variable');

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $target->requestRevisionsUpdateRecipients('Mail::send', [$mailTemplate]);
    }

    public function test_requestRevisionsUpdateRecipients_should_set_not_subject_when_submission_title_variable_is_not_set() {
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, 'My Subject', 'Submission Title', null, []);

        $mailTemplate->expects($this->once())->method('setSubject')->with('My Subject');

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $target->requestRevisionsUpdateRecipients('Mail::send', [$mailTemplate]);
    }

    public function test_requestRevisionsUpdateRecipients_should_not_update_template_when_email_key_is_not_known() {
        $mailTemplate = $this->createSubmissionEmailTemplate('not_known_email', self::SUBJECT_WITH_TITLE_VAR, 'title', null, []);

        $this->assertEquals(false, in_array($mailTemplate->emailKey, PPREditorialDecisionsEmailService::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES));

        $mailTemplate->expects($this->never())->method($this->anything());

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $target->requestRevisionsUpdateRecipients('Mail::send', [$mailTemplate]);
    }

    public function test_expected_known_templates() {
        $expectedTemplates =  ['EDITOR_DECISION_REVISIONS', 'EDITOR_DECISION_RESUBMIT', 'EDITOR_DECISION_DECLINE'];
        foreach ($expectedTemplates as $expectedTemplate) {
            $this->assertEquals(true, in_array($expectedTemplate, PPREditorialDecisionsEmailService::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES));
        }

        foreach (PPREditorialDecisionsEmailService::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES as $knownTemplate) {
            $this->assertEquals(true, in_array($knownTemplate, $expectedTemplates));
        }
    }

    private function createSubmissionEmailTemplate($emailKey, $subject, $submissionTitle, $primaryAuthorName, $contributorsNames) {
        $submission = $this->createSubmissionWithAuthors($primaryAuthorName, $contributorsNames);
        $submissionMailTemplate = $this->createMock(SubmissionMailTemplate::class);
        $submissionMailTemplate->submission = $submission;
        $submissionMailTemplate->emailKey = $emailKey;
        $emailParams = [];
        if($submissionTitle) {
            $emailParams['submissionTitle'] = $submissionTitle;
        }
        $submissionMailTemplate->params = $emailParams;
        $submissionMailTemplate->method('getSubject')->willReturn($subject);
        return $submissionMailTemplate;
    }

    private function createSubmissionWithAuthors($primaryAuthorName, $contributorsNames = []) {
        $primaryAuthor = null;

        if ($primaryAuthorName) {
            $primaryAuthor = $this->createMock(Author::class);
            $primaryAuthor->method('getFullName')->willReturn($primaryAuthorName);
            $primaryAuthor->method('getEmail')->willReturn($primaryAuthorName);
        }

        $contributors = [];
        foreach ($contributorsNames as $name) {
            $contributor = $this->createMock(Author::class);
            $contributor->method('getFullName')->willReturn($name);
            $contributor->method('getEmail')->willReturn($name);
            $contributors[] = $contributor;
        }

        $submission = $this->createMock(Submission::class);
        $submission->method('getPrimaryAuthor')->willReturn($primaryAuthor);
        $submission->method('getAuthors')->willReturn($contributors);
        return $submission;
    }

}