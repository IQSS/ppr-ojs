<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.submission.PPRSubmissionCommentsForReviewerService');

import('lib.pkp.controllers.grid.users.reviewer.form.AdvancedSearchReviewerForm');
import('classes.submission.form.SubmissionSubmitStep3Form');
import('classes.submission.reviewer.form.ReviewerReviewStep3Form');
import('classes.submission.Submission');
import('classes.publication.Publication');
import('classes.publication.PublicationDAO');

class PPRSubmissionCommentsForReviewerServiceTest extends PPRTestCase {

    const CONTEXT_ID = 420;
    private $defaultPPRPlugin;

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, []);
    }

    public function test_register_should_not_register_any_hooks_when_submissionCommentsForReviewerEnabled_is_false() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['submissionCommentsForReviewerEnabled' => false]);
        $target = new PPRSubmissionCommentsForReviewerService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_service_hooks_when_submissionCommentsForReviewerEnabled_is_true() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['submissionCommentsForReviewerEnabled' => true]);
        $target = new PPRSubmissionCommentsForReviewerService($pprPluginMock);
        $target->register();

        $this->assertEquals(5, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('Schema::get::publication')));
        $this->assertEquals(1, count($this->getHooks('submissionsubmitstep3form::initdata')));
        $this->assertEquals(1, count($this->getHooks('submissionsubmitstep3form::readuservars')));
        $this->assertEquals(1, count($this->getHooks('submissionsubmitstep3form::execute')));
        $this->assertEquals(1, count($this->getHooks('reviewerreviewstep3form::initdata')));
    }

    public function test_addFieldsToSubmissionDatabaseSchema_should_update_schema() {
        $target = new PPRSubmissionCommentsForReviewerService($this->defaultPPRPlugin);
        $schema = new stdClass();;
        $schema->properties = new stdClass();

        $result = $target->addFieldsToPublicationDatabaseSchema('Schema::get::publication', [$schema]);
        $this->assertEquals(false, $result);
        $this->assertEquals(true, isset($schema->properties->commentsForReviewer));
        $this->assertEquals(true, isset($schema->properties->commentsForReviewer->validation));
        $this->assertEquals(true, isset($schema->properties->commentsForReviewer->multilingual));
        $this->assertEquals('string', $schema->properties->commentsForReviewer->type);
    }

    public function test_initSubmissionFormData_should_update_template_manager_with_commentsForReviewer_value() {
        $expectedComments = 'Some comments';
        $submissionForm = $this->create_submission_form($expectedComments, null, null);

        $target = new PPRSubmissionCommentsForReviewerService($this->defaultPPRPlugin);

        $result = $target->initSubmissionFormData('submissionsubmitstep3form::initdata', [$submissionForm]);
        $this->assertEquals(false, $result);
        $templateManager = TemplateManager::getManager();
        $this->assertEquals($expectedComments, $templateManager->getTemplateVars('commentsForReviewer'));
    }

    public function test_initReviewData_should_update_template_manager_with_commentsForReviewer_value() {
        $expectedComments = 'Some comments';
        $submission = $this->create_submission($expectedComments, null);
        $reviewForm = $this->createMock(ReviewerReviewStep3Form::class);
        $reviewForm->method('getReviewerSubmission')->willReturn($submission);

        $target = new PPRSubmissionCommentsForReviewerService($this->defaultPPRPlugin);

        $result = $target->initReviewData('reviewerreviewstep3form::initdata', [$reviewForm]);
        $this->assertEquals(false, $result);
        $templateManager = TemplateManager::getManager();
        $this->assertEquals($expectedComments, $templateManager->getTemplateVars('commentsForReviewer'));
    }

    public function test_readCommentsForReviewerVars_should_add_expected_variables_to_the_userVars_list() {
        $target = new PPRSubmissionCommentsForReviewerService($this->defaultPPRPlugin);
        $userVars = ['some_value'];

        $result = $target->readCommentsForReviewerVars('submissionsubmitstep3form::readuservars', [null, &$userVars]);
        $this->assertEquals(false, $result);
        $this->assertEquals(2, count($userVars));
        $this->assertEquals('commentsForReviewer', $userVars[1]);
    }

    public function test_executeSubmissionCommentsForReviewer_should_copy_data_from_form_to_publication_and_saved() {
        $expectedComment = 'form comment';
        $submissionForm = $this->create_submission_form(null, '12345', $expectedComment);

        $publicationDao = $this->createMock(PublicationDAO::class);
        $publicationDao->expects($this->once())->method('replace');
        DAORegistry::registerDAO('PublicationDAO', $publicationDao);

        $target = new PPRSubmissionCommentsForReviewerService($this->defaultPPRPlugin);
        $result = $target->executeSubmissionCommentsForReviewer('submissionsubmitstep3form::execute', [$submissionForm]);
        $this->assertEquals(false, $result);
    }

    private function create_submission_form($publicationCommentsForReviewer, $publicationId, $formCommentsForReviewer) {
        $submission = $this->create_submission($publicationCommentsForReviewer, $publicationId);

        $submissionForm = $this->createMock(SubmissionSubmitStep3Form::class);
        $submissionForm->method('getData')->with('commentsForReviewer')->willReturn(['en_US' => $formCommentsForReviewer]);
        $submissionForm->submission = $submission;

        return $submissionForm;
    }

    private function create_submission($publicationCommentsForReviewer, $publicationId) {
        $publication = $this->createMock(Publication::class);
        if($publicationId) $publication->method('getData')->with('id')->willReturn($publicationId);
        if($publicationCommentsForReviewer) {
            $publication->method('getData')->with('commentsForReviewer')->willReturn($publicationCommentsForReviewer);
            $publication->method('getLocalizedData')->with('commentsForReviewer')->willReturn($publicationCommentsForReviewer);
        }

        $submission = $this->createMock(Submission::class);
        $submission->method('getCurrentPublication')->willReturn($publication);

        return $submission;
    }
}