<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.submission.PPRSubmissionEmailContributorsService');

import('classes.submission.form.SubmissionSubmitStep3Form');
import('classes.submission.Submission');

class PPRSubmissionEmailContributorsServiceTest extends PPRTestCase {

    const CONTEXT_ID = 986654;
    private $defaultPPRPlugin;

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, []);
    }

    public function test_register_should_not_register_any_hooks_when_emailContributorsEnabled_is_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['emailContributorsEnabled' => false]);
        $target = new PPRSubmissionEmailContributorsService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_service_hooks_when_emailContributorsEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['emailContributorsEnabled' => true]);
        $target = new PPRSubmissionEmailContributorsService($pprPluginMock);
        $target->register();

        $this->assertEquals(4, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('Schema::get::submission')));
        $this->assertEquals(1, count($this->getHooks('submissionsubmitstep3form::initdata')));
        $this->assertEquals(1, count($this->getHooks('submissionsubmitstep3form::readuservars')));
        $this->assertEquals(1, count($this->getHooks('submissionsubmitstep3form::execute')));
    }

    public function test_addFieldsToSubmissionDatabaseSchema_should_update_schema() {
        $target = new PPRSubmissionEmailContributorsService($this->defaultPPRPlugin);
        $schema = new stdClass();;
        $schema->properties = new stdClass();

        $result = $target->addFieldsToSubmissionDatabaseSchema('Schema::get::submission', [$schema]);
        $this->assertEquals(false, $result);
        $this->assertEquals(true, isset($schema->properties->emailContributors));
        $this->assertEquals(true, isset($schema->properties->emailContributors->validation));
        $this->assertEquals('boolean', $schema->properties->emailContributors->type);
    }

    public function test_initSubmissionFormData_should_update_template_manager_with_custom_fields() {
        $expectedEmailContributors = true;
        $submissionForm = $this->create_submission_form($expectedEmailContributors, null);

        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['researchTypeOptions' => 'paper, research']);
        $target = new PPRSubmissionEmailContributorsService($pprPluginMock);

        $result = $target->initSubmissionFormData('submissionsubmitstep3form::initdata', [$submissionForm]);
        $this->assertEquals(false, $result);
        $templateManager = TemplateManager::getManager();
        $this->assertEquals($expectedEmailContributors, $templateManager->getTemplateVars('emailContributors'));
    }

    public function test_readSubmissionFormVars_should_add_expected_variables_to_the_userVars_list() {
        $target = new PPRSubmissionEmailContributorsService($this->defaultPPRPlugin);

        $userVars = ['some_value'];

        $result = $target->readSubmissionFormVars('submissionsubmitstep3form::readuservars', [null, &$userVars]);
        $this->assertEquals(false, $result);
        $this->assertEquals(2, count($userVars));
        $this->assertEquals('emailContributors', $userVars[1]);
    }

    public function test_executeSubmissionSubmissionForm_should_copy_custom_fields_data_from_form_to_submission() {
        $expectedEmailContributors = true;
        $submissionForm = $this->create_submission_form(null, $expectedEmailContributors);
        $submissionForm->submission->expects($this->once())->method('setData')->with($expectedEmailContributors);

        $target = new PPRSubmissionEmailContributorsService($this->defaultPPRPlugin);

        $result = $target->executeSubmissionSubmissionForm('submissionsubmitstep3form::execute', [$submissionForm]);
        $this->assertEquals(false, $result);
    }

    private function create_submission_form($submissionEmailContributors, $formEmailContributors) {
        $submission = $this->createMock(Submission::class);
        $submission->method('getData')->with('emailContributors')->willReturn($submissionEmailContributors);

        $submissionForm = $this->createMock(SubmissionSubmitStep3Form::class);
        $submissionForm->method('getData')->with('emailContributors')->willReturn($formEmailContributors);
        $submissionForm->submission = $submission;

        return $submissionForm;
    }
}