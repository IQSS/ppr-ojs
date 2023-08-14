<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.email.PPRReviewerFormEmailService');

import('lib.pkp.controllers.grid.users.reviewer.form.AdvancedSearchReviewerForm');
import('lib.pkp.classes.mail.SubmissionMailTemplate');
import('lib.pkp.classes.user.User');
import('lib.pkp.classes.user.UserDAO');

class PPRReviewerFormEmailServiceTest extends PPRTestCase {

    const CONTEXT_ID = 130;
    const BODY_WITH_NAME_VAR = 'Body: {$firstNameOnly} Variable';
    private $defaultPPRPlugin;
    private $dafaultEmailKey;

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, []);
        $this->dafaultEmailKey = PPRReviewerFormEmailService::OJS_ADD_REVIEWER_EMAIL_TEMPLATES[array_rand(PPRReviewerFormEmailService::OJS_ADD_REVIEWER_EMAIL_TEMPLATES)];
    }

    public function test_register_should_not_register_any_hooks_when_addReviewerEmailServiceEnabled_is_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['addReviewerEmailServiceEnabled' => false]);
        $target = new PPRReviewerFormEmailService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_service_hooks_when_addReviewerEmailServiceEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['addReviewerEmailServiceEnabled' => true]);
        $target = new PPRReviewerFormEmailService($pprPluginMock);
        $target->register();

        $this->assertEquals(2, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('advancedsearchreviewerform::display')));
        $this->assertEquals(1, count($this->getHooks('Mail::send')));
    }

    public function test_reviewerFormDisplay_should_set_reviewer_first_name_label_in_template_data_emailVariables() {
        $reviewerForm = $this->createMock(AdvancedSearchReviewerForm::class);

        $templateManager = TemplateManager::getManager();
        $templateManager->setData([
            'emailVariables' => [],
        ]);

        $target = new PPRReviewerFormEmailService($this->defaultPPRPlugin);
        $target->reviewerFormDisplay('advancedsearchreviewerform::display', [$reviewerForm]);

        $this->assertEquals(__('review.ppr.reviewer.firstName.label'), $templateManager->getTemplateVars('emailVariables')['firstNameOnly']);
    }

    public function test_assignReviewerEmailSend_should_not_add_reviewer_name_to_body_when_template_is_not_known() {
        $userDao = $this->createMock(UserDAO::class);
        $userDao->expects($this->never())->method($this->anything());
        DAORegistry::registerDAO('UserDAO', $userDao);

        $mailTemplate = $this->createSubmissionEmailTemplate('unknown_email');
        $mailTemplate->expects($this->never())->method('getBody');
        $mailTemplate->expects($this->never())->method('setBody');

        $target = new PPRReviewerFormEmailService($this->defaultPPRPlugin);
        $response = $target->assignReviewerEmailSend('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_assignReviewerEmailSend_should_add_reviewer_name_to_body_when_template_is_known() {
        $expectedFirstName = 'FirstName';
        $username = 'reviewer';
        $this->addGetReviewerMock($username, $expectedFirstName);

        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, ['reviewerUserName' => $username]);
        $mailTemplate->expects($this->once())->method('getBody')->willReturn(self::BODY_WITH_NAME_VAR);
        $mailTemplate->expects($this->once())->method('setBody')->with('Body: FirstName Variable');

        $target = new PPRReviewerFormEmailService($this->defaultPPRPlugin);
        $response = $target->assignReviewerEmailSend('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_assignReviewerEmailSend_should_not_add_reviewer_name_to_body_when_reviewer_not_found() {
        $username = 'reviewer';
        $this->addGetReviewerMock($username, null);

        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey, ['reviewerUserName' => $username]);
        $mailTemplate->expects($this->never())->method('getBody');
        $mailTemplate->expects($this->never())->method('setBody');

        $target = new PPRReviewerFormEmailService($this->defaultPPRPlugin);
        $response = $target->assignReviewerEmailSend('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_expected_known_templates() {
        $expectedTemplates =   ['REVIEW_REQUEST', 'REVIEW_REQUEST_ONECLICK', 'REVIEW_REQUEST_SUBSEQUENT', 'REVIEW_REQUEST_ONECLICK_SUBSEQUENT'];
        foreach ($expectedTemplates as $expectedTemplate) {
            $this->assertEquals(true, in_array($expectedTemplate, PPRReviewerFormEmailService::OJS_ADD_REVIEWER_EMAIL_TEMPLATES));
        }

        foreach (PPRReviewerFormEmailService::OJS_ADD_REVIEWER_EMAIL_TEMPLATES as $knownTemplate) {
            $this->assertEquals(true, in_array($knownTemplate, $expectedTemplates));
        }
    }

    private function createSubmissionEmailTemplate($emailKey, $templateData = []) {
        $submissionMailTemplate = $this->createMock(SubmissionMailTemplate::class);
        $submissionMailTemplate->emailKey = $emailKey;
        $submissionMailTemplate->params = $templateData;
        return $submissionMailTemplate;
    }

    private function addGetReviewerMock($reviewerUsername, $reviewerFirstName) {
        $user = null;
        if($reviewerFirstName) {
            $user = $this->createMock(User::class);
            $user->method('getLocalizedGivenName')->willReturn($reviewerFirstName);
            $user->method('getUsername')->willReturn($reviewerUsername);
        }

        $userDao = $this->createMock(UserDAO::class);
        $userDao->expects($this->once())
            ->method('getByUsername')
            ->with($reviewerUsername)
            ->willReturn($user);

        DAORegistry::registerDAO('UserDAO', $userDao);
        return $userDao;
    }

}