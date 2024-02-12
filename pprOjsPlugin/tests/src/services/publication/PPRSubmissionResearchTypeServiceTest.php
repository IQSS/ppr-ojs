<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.submission.PPRSubmissionResearchTypeService');

import('lib.pkp.controllers.grid.users.reviewer.form.AdvancedSearchReviewerForm');
import('classes.submission.form.SubmissionSubmitStep3Form');
import('classes.submission.Submission');

class PPRSubmissionResearchTypeServiceTest extends PPRTestCase {

    const CONTEXT_ID = 130;
    private $defaultPPRPlugin;

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, []);
    }

    public function test_register_should_not_register_any_hooks_when_submissionResearchTypeEnabled_is_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['submissionResearchTypeEnabled' => false]);
        $target = new PPRSubmissionResearchTypeService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_service_hooks_when_submissionResearchTypeEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['submissionResearchTypeEnabled' => true]);
        $target = new PPRSubmissionResearchTypeService($pprPluginMock);
        $target->register();

        $this->assertEquals(7, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('Schema::get::submission')));
        $this->assertEquals(1, count($this->getHooks('submissionsubmitstep3form::initdata')));
        $this->assertEquals(1, count($this->getHooks('submissionsubmitstep3form::readuservars')));
        $this->assertEquals(1, count($this->getHooks('submissionsubmitstep3form::execute')));
        $this->assertEquals(1, count($this->getHooks('advancedsearchreviewerform::display')));
        $this->assertEquals(1, count($this->getHooks('createreviewerform::display')));
        $this->assertEquals(1, count($this->getHooks('enrollexistingreviewerform::display')));
    }

    public function test_addFieldsToSubmissionDatabaseSchema_should_update_schema() {
        $target = new PPRSubmissionResearchTypeService($this->defaultPPRPlugin);
        $schema = new stdClass();;
        $schema->properties = new stdClass();

        $result = $target->addFieldsToSubmissionDatabaseSchema('Schema::get::submission', [$schema]);
        $this->assertEquals(false, $result);
        $this->assertEquals(true, isset($schema->properties->researchType));
        $this->assertEquals(true, isset($schema->properties->researchType->validation));
        $this->assertEquals('string', $schema->properties->researchType->type);
    }

    public function test_initResearchTypeData_should_update_template_manager_with_research_type_value_and_options() {
        $expectedResearchType = 'research type';
        $submissionForm = $this->create_submission_form($expectedResearchType, null);

        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['researchTypeOptions' => 'paper, research']);
        $target = new PPRSubmissionResearchTypeService($pprPluginMock);

        $result = $target->initResearchTypeData('submissionsubmitstep3form::initdata', [$submissionForm]);
        $this->assertEquals(false, $result);
        $templateManager = TemplateManager::getManager();
        $this->assertEquals($expectedResearchType, $templateManager->getTemplateVars('researchType'));
        $this->assertEquals(['paper' => 'paper', 'research' => 'research'], $templateManager->getTemplateVars('researchTypes'));
    }

    public function test_readResearchTypeVars_should_add_expected_variables_to_the_userVars_list() {
        $target = new PPRSubmissionResearchTypeService($this->defaultPPRPlugin);

        $userVars = ['some_value'];

        $result = $target->readResearchTypeVars('submissionsubmitstep3form::readuservars', [null, &$userVars]);
        $this->assertEquals(false, $result);
        $this->assertEquals(2, count($userVars));
        $this->assertEquals('researchType', $userVars[1]);
    }

    public function test_executeSubmissionResearchType_should_copy_researchType_data_from_form_to_submission() {
        $expectedResearchType = 'form research type';
        $submissionForm = $this->create_submission_form(null, $expectedResearchType);
        $submissionForm->submission->expects($this->once())->method('setData')->with('researchType', $expectedResearchType);

        $target = new PPRSubmissionResearchTypeService($this->defaultPPRPlugin);

        $result = $target->executeSubmissionResearchType('submissionsubmitstep3form::execute', [$submissionForm]);
        $this->assertEquals(false, $result);
    }

    public function test_initReviewerFormData_should_update_form_with_research_type_and_form_id() {
        $expectedResearchType = 'research type';
        $expectedFormId = $this->getRandomId();
        $submissionForm = $this->create_reviewer_form($expectedResearchType);
        $submissionForm->expects($this->exactly(2))->method('setData')->withConsecutive(
            ['submissionResearchType', $expectedResearchType],
            ['reviewFormId', $expectedFormId],
        );

        TemplateManager::getManager()->setData([
            'reviewForms' => [$expectedFormId => 'expected form' , 2 => 'other form'],
        ]);

        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['researchTypeOptions' => 'research type = expected form, other = other form']);
        $target = new PPRSubmissionResearchTypeService($pprPluginMock);

        $result = $target->initReviewerFormData('advancedsearchreviewerform::display', [$submissionForm]);
        $this->assertEquals(false, $result);
    }

    public function test_initReviewerFormData_should_not_update_form_id_when_research_type_form_does_not_match_the_list_in_template() {
        $invalidResearchType = 'invalid';
        $submissionForm = $this->create_reviewer_form($invalidResearchType);
        $submissionForm->expects($this->once())->method('setData')->with('submissionResearchType', $invalidResearchType);

        TemplateManager::getManager()->setData([
            'reviewForms' => [1 => 'expected form' , 2 => 'other form'],
        ]);

        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['researchTypeOptions' => 'research type = no match form, other = other form']);
        $target = new PPRSubmissionResearchTypeService($pprPluginMock);

        $result = $target->initReviewerFormData('advancedsearchreviewerform::display', [$submissionForm]);
        $this->assertEquals(false, $result);
    }

    public function test_initReviewerFormData_should_not_update_form_id_when_research_type_not_available_in_research_options() {
        $expectedResearchType = 'research type';
        $submissionForm = $this->create_reviewer_form($expectedResearchType);
        $submissionForm->expects($this->once())->method('setData')->with('submissionResearchType', $expectedResearchType);

        TemplateManager::getManager()->setData([
            'reviewForms' => [1 => 'expected form' , 2 => 'other form'],
        ]);

        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['researchTypeOptions' => 'type1 = expected form, type2 = other form']);
        $target = new PPRSubmissionResearchTypeService($pprPluginMock);

        $result = $target->initReviewerFormData('advancedsearchreviewerform::display', [$submissionForm]);
        $this->assertEquals(false, $result);
    }

    private function create_submission_form($submissionResearchType, $formResearchType) {
        $submission = $this->createMock(Submission::class);
        $submission->method('getData')->with('researchType')->willReturn($submissionResearchType);

        $submissionForm = $this->createMock(SubmissionSubmitStep3Form::class);
        $submissionForm->method('getData')->with('researchType')->willReturn($formResearchType);
        $submissionForm->submission = $submission;

        return $submissionForm;
    }

    private function create_reviewer_form($submissionResearchType) {
        $submission = $this->createMock(Submission::class);
        $submission->method('getData')->with('researchType')->willReturn($submissionResearchType);

        $reviewerForm = $this->createMock(AdvancedSearchReviewerForm::class);
        $reviewerForm->method('getSubmission')->willReturn($submission);

        return $reviewerForm;
    }
}