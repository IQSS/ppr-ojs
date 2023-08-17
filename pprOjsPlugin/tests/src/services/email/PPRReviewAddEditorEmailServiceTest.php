<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.email.PPRReviewAddEditorEmailService');

import('lib.pkp.classes.security.UserGroupDAO');

class PPRReviewAddEditorEmailServiceTest extends PPRTestCase {

    const CONTEXT_ID = 130;
    private $defaultPPRPlugin;
    private $dafaultEmailKey;

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, []);
        $this->dafaultEmailKey = PPRReviewAddEditorEmailService::TEMPLATES_MANAGING_EDITOR_BCC[array_rand(PPRReviewAddEditorEmailService::TEMPLATES_MANAGING_EDITOR_BCC)];
    }

    public function test_register_should_not_register_any_hooks_when_reviewAddEditorToBccEnabled_is_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['reviewAddEditorToBccEnabled' => false]);
        $target = new PPRReviewAddEditorEmailService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_service_hooks_when_reviewAddEditorToBccEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['reviewAddEditorToBccEnabled' => true]);
        $target = new PPRReviewAddEditorEmailService($pprPluginMock);
        $target->register();

        $this->assertEquals(1, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('Mail::send')));
    }

    public function test_addManagingEditorToBCC_should_add_managing_editor_to_email_bcc_when_emailKey_is_known() {
        $expectedEditorName = "Antonio Santana";
        $this->addGetManagingEditorMock(__('tasks.ppr.managingEditor.groupName'), [$expectedEditorName]);
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey);
        $mailTemplate->expects($this->once())->method('addBcc')->with($expectedEditorName, $expectedEditorName);

        $target = new PPRReviewAddEditorEmailService($this->defaultPPRPlugin);
        $response = $target->addManagingEditorToBCC('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_addManagingEditorToBCC_should_not_add_managing_editor_to_email_bcc_when_emailKey_is_not_known() {
        $userGroupDao = $this->createMock(UserGroupDAO::class);
        $userGroupDao->expects($this->never())->method($this->anything());
        DAORegistry::registerDAO('UserGroupDAO', $userGroupDao);

        $mailTemplate = $this->createSubmissionEmailTemplate('unknown_email');
        $mailTemplate->expects($this->never())->method('addBcc');

        $target = new PPRReviewAddEditorEmailService($this->defaultPPRPlugin);
        $response = $target->addManagingEditorToBCC('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_addManagingEditorToBCC_should_not_call_addBcc_when_no_managing_editors_found() {
        $this->addGetManagingEditorMock(__('tasks.ppr.managingEditor.groupName'), []);
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey);
        $mailTemplate->expects($this->never())->method('addBcc');

        $target = new PPRReviewAddEditorEmailService($this->defaultPPRPlugin);
        $response = $target->addManagingEditorToBCC('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    public function test_addManagingEditorToBCC_should_not_call_addBcc_when_group_not_found() {
        $this->addGetManagingEditorMock('invalid group name', null);
        $mailTemplate = $this->createSubmissionEmailTemplate($this->dafaultEmailKey);
        $mailTemplate->expects($this->never())->method('addBcc');

        $target = new PPRReviewAddEditorEmailService($this->defaultPPRPlugin);
        $response = $target->addManagingEditorToBCC('Mail::send', [$mailTemplate]);
        // SHOULD ALWAYS RETURN FALSE
        $this->assertEquals(false, $response);
    }

    private function createSubmissionEmailTemplate($emailKey) {
        $submissionMailTemplate = $this->createMock(SubmissionMailTemplate::class);
        $submissionMailTemplate->emailKey = $emailKey;
        return $submissionMailTemplate;
    }

    private function addGetManagingEditorMock($groupName, $userNames) {
        $userGroupId = $this->getRandomId();
        $userGroup = $this->createMock(UserGroup::class);
        $userGroup->method('getLocalizedName')->willReturn($groupName);
        $userGroup->method('getId')->willReturn($userGroupId);
        $userGroupDao = $this->createMock(UserGroupDAO::class);
        $userGroupDao->expects($this->once())
            ->method('getByContextId')
            ->with(self::CONTEXT_ID)
            ->willReturn($this->getResultFactoryMock([$userGroup]));

        if($userNames !== null) {
            $editors = [];
            foreach ($userNames as $userName) {
                $editor = $this->createMock(Author::class);
                $editor->method('getFullName')->willReturn($userName);
                $editor->method('getEmail')->willReturn($userName);
                $editors[] = $editor;
            }

            $userGroupDao->expects($this->once())
                ->method('getUsersById')
                ->with($userGroupId, self::CONTEXT_ID)
                ->willReturn($this->getResultFactoryMock($editors));
        } else {
            $userGroupDao->expects($this->never())->method('getUsersById');
        }

        DAORegistry::registerDAO('UserGroupDAO', $userGroupDao);
        return $userGroupDao;
    }

    private function getResultFactoryMock($dataArray) {
        $resultFactory = $this->createMock(DAOResultFactory::class);
        $resultFactory->method('toArray')
            ->willReturn($dataArray);

        return $resultFactory;
    }
}