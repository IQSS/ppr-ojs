<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.email.PPRStageParticipantGridHandler');

import('lib.pkp.classes.security.authorization.AuthorizationDecisionManager');

class PPRStageParticipantGridHandlerTest extends PPRTestCase {

    public function test_fetchTemplateBody_should_use_firstNamesManagementService_to_replace_first_names_in_email_body() {
        $objectFactory = $this->getTestUtil()->createObjectFactory();
        $submission = $this->getTestUtil()->createSubmission();
        $objectFactory->method('submissionMailTemplate')->willReturn($this->createEmailTemplate($submission, 'original_email_text'));
        $objectFactory->firstNamesManagementService()->method('replaceFirstNames')->with('original_email_text', $submission)->willReturn('updated_email_text');

        $target = new PPRStageParticipantGridHandler($objectFactory);
        $target->_authorizationDecisionManager = $this->createMock(AuthorizationDecisionManager::class);
        $target->_authorizationDecisionManager->method('getAuthorizedContextObject')->willReturn($submission);
        $result = $target->fetchTemplateBody([], $this->getRequestMock());

        $this->assertEquals(true, $result->getStatus());
        $this->assertEquals('updated_email_text', $result->getContent()['body']);
    }

    private function createEmailTemplate($submission, $emailBody) {
        $submissionMailTemplate = $this->createMock(SubmissionMailTemplate::class);
        $submissionMailTemplate->submission = $submission;
        $submissionMailTemplate->emailKey = strval($this->getRandomId());
        $submissionMailTemplate->method('getBody')->willReturn($emailBody);
        return $submissionMailTemplate;
    }
}