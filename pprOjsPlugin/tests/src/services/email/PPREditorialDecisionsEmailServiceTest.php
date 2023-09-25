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
        $this->dafaultEmailKey = PPREditorialDecisionsEmailService::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES[array_rand(PPREditorialDecisionsEmailService::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES)];

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

    public function test_sendReviewsFormDisplay_should_set_primary_author_data_in_template_data_when_primary_author_not_null() {
        $expectedAuthorName = 'John';

        $sendReviewForm = $this->createMock(SendReviewsForm::class);
        $sendReviewForm->method('getSubmission')->willReturn($this->getTestUtil()->createSubmissionWithAuthors($expectedAuthorName, []));
        $sendReviewForm->method('getData')->with('personalMessage')->willReturn($this->createStringTemplate('personal'));

        TemplateManager::getManager()->setData([
            'revisionsEmail' => $this->createStringTemplate('revisions'),
            'resubmitEmail' => $this->createStringTemplate('resubmit'),
        ]);

        $sendReviewForm->expects($this->exactly(4))
            ->method('setData')->withConsecutive(
                ['authorName', 'John'],
                ['personalMessage', 'Test - Full: John First: John template personal'],
                ['revisionsEmail', 'Test - Full: John First: John template revisions'],
                ['resubmitEmail', 'Test - Full: John First: John template resubmit'],
            );

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $target->sendReviewsFormDisplay('sendreviewsform::display', [$sendReviewForm]);
    }

    public function test_sendReviewsFormDisplay_should_set_first_contributor_name_in_template_data_when_primary_author_is_null() {
        $expectedAuthorName = 'First Contributor';

        $sendReviewForm = $this->createMock(SendReviewsForm::class);
        $sendReviewForm->method('getSubmission')->willReturn($this->getTestUtil()->createSubmissionWithAuthors(null, [$expectedAuthorName, 'invalid', 'invalid']));
        $sendReviewForm->method('getData')->with('personalMessage')->willReturn($this->createStringTemplate('personal'),);

        TemplateManager::getManager()->setData([
            'revisionsEmail' => $this->createStringTemplate('revisions'),
            'resubmitEmail' => $this->createStringTemplate('resubmit'),
        ]);

        $sendReviewForm->expects($this->exactly(4))
            ->method('setData')->withConsecutive(
                ['authorName', 'First Contributor'],
                ['personalMessage', 'Test - Full: First Contributor First: First Contributor template personal'],
                ['revisionsEmail', 'Test - Full: First Contributor First: First Contributor template revisions'],
                ['resubmitEmail', 'Test - Full: First Contributor First: First Contributor template resubmit'],
            );

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $target->sendReviewsFormDisplay('sendreviewsform::display', [$sendReviewForm]);
    }

    public function test_sendReviewsFormDisplay_should_not_set_template_data_when_authors_are_null() {
        $sendReviewForm = $this->createMock(SendReviewsForm::class);
        $sendReviewForm->method('getSubmission')->willReturn($this->getTestUtil()->createSubmissionWithAuthors(null, []));

        $sendReviewForm->expects($this->never())->method('setData');

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $target->sendReviewsFormDisplay('sendreviewsform::display', [$sendReviewForm]);
    }

    public function test_editorDecisionEmailsSetRecipients_should_set_recipients_when_primary_author_not_null() {
        $expectedAuthorName = 'Author Name';
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, self::SUBJECT_WITH_TITLE_VAR, 'title', $expectedAuthorName, []);

        $mailTemplate->expects($this->once())->method('setRecipients')->with(array(['name' => 'Author Name', 'email' => 'Author Name@email.com']));

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $response = $target->editorDecisionEmailsSetRecipients('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_editorDecisionEmailsSetRecipients_should_set_first_contributor_as_recipients_when_primary_author_is_null() {
        $expectedAuthorName = 'First Contributor';
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, self::SUBJECT_WITH_TITLE_VAR, 'title', null, [$expectedAuthorName, 'invalid', 'not used']);

        $mailTemplate->expects($this->once())->method('setRecipients')->with(array(['name' => 'First Contributor', 'email' => 'First Contributor@email.com']));

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $response = $target->editorDecisionEmailsSetRecipients('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_editorDecisionEmailsSetRecipients_should_not_set_recipients_when_authors_are_null() {
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, self::SUBJECT_WITH_TITLE_VAR, 'title', null, []);

        $mailTemplate->expects($this->never())->method('setRecipients');

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $response = $target->editorDecisionEmailsSetRecipients('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_editorDecisionEmailsSetRecipients_should_not_update_template_when_email_key_is_not_known() {
        $mailTemplate = $this->createSubmissionEmailTemplate('not_known_email', self::SUBJECT_WITH_TITLE_VAR, 'title', null, []);

        $this->assertEquals(false, in_array($mailTemplate->emailKey, PPREditorialDecisionsEmailService::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES));

        $mailTemplate->expects($this->never())->method($this->anything());

        $target = new PPREditorialDecisionsEmailService($this->defaultPPRPlugin);
        $response = $target->editorDecisionEmailsSetRecipients('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_expected_known_templates() {
        $expectedTemplates =  ['EDITOR_DECISION_REVISIONS', 'EDITOR_DECISION_RESUBMIT', 'EDITOR_DECISION_INITIAL_DECLINE', 'EDITOR_DECISION_DECLINE'];
        foreach ($expectedTemplates as $expectedTemplate) {
            $this->assertEquals(true, in_array($expectedTemplate, PPREditorialDecisionsEmailService::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES));
        }

        foreach (PPREditorialDecisionsEmailService::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES as $knownTemplate) {
            $this->assertEquals(true, in_array($knownTemplate, $expectedTemplates));
        }
    }

    private function createStringTemplate($type) {
        return 'Test - Full: {$authorFullName} First: {$authorFirstName} template ' . $type;
    }

    private function createSubmissionEmailTemplate($emailKey, $subject, $submissionTitle, $primaryAuthorName, $contributorsNames) {
        $submission = $this->getTestUtil()->createSubmissionWithAuthors($primaryAuthorName, $contributorsNames);
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
}