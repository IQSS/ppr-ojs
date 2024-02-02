
<?php

import('tests.src.PPRTestCase');

class PPRFirstNamesManagementServiceTest extends PPRTestCase {

    public function test_getReviewer_should_use_reviewerId_if_provided() {
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);
        $reviewerId = $this->getRandomId();
        $reviewer = $this->addReviewer($submissionUtil, $reviewerId);

        $target = new PPRFirstNamesManagementService($submissionUtil);
        $result = $target->getReviewer($reviewerId);
        $this->assertEquals($reviewer, $result);
    }

    public function test_getReviewer_should_use_request_reviewerId_reviewerId_is_not_provided() {
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);
        $reviewerId = $this->getRandomId();
        $reviewer = $this->addReviewer($submissionUtil, $reviewerId);
        $this->getRequestMock()->method('getUserVar')->with('reviewerId')->willReturn($reviewerId);

        $target = new PPRFirstNamesManagementService($submissionUtil);
        $result = $target->getReviewer(null);
        $this->assertEquals($reviewer, $result);
    }

    public function test_getReviewer_should_use_request_reviewAssignmentId_when_parameter_and_request_reviewerId_not_set() {
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);
        $reviewAssignmentId = $this->getRandomId();
        $this->getRequestMock()->method('getUserVar')->withConsecutive(['reviewerId'], ['reviewAssignmentId'])
            ->willReturnOnConsecutiveCalls(null, $reviewAssignmentId);
        $reviewer =$this->getTestUtil()->createUser($this->getRandomId(), 'Reviewer');
        $submissionUtil->expects($this->once())->method('getReviewer')->with($reviewAssignmentId)->willReturn($reviewer);

        $target = new PPRFirstNamesManagementService($submissionUtil);
        $result = $target->getReviewer(null);
        $this->assertEquals($reviewer, $result);
    }

    public function test_getReviewer_should_use_authorized_object_when_all_others_are_not_provided() {
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);
        $this->getRequestMock()->method('getUserVar')->withConsecutive(['reviewerId'], ['reviewAssignmentId'])
            ->willReturnOnConsecutiveCalls(null, null);
        $review = $this->getTestUtil()->createReview();
        $this->getRequestMock()->getRouter()->getHandler()->expects($this->once())
            ->method('getAuthorizedContextObject')->with(ASSOC_TYPE_REVIEW_ASSIGNMENT)->willReturn($review);
        $reviewer =$this->getTestUtil()->createUser($this->getRandomId(), 'Reviewer');
        $submissionUtil->expects($this->once())->method('getReviewer')->with($review->getId())->willReturn($reviewer);

        $target = new PPRFirstNamesManagementService($submissionUtil);
        $result = $target->getReviewer(null);
        $this->assertEquals($reviewer, $result);
    }

    public function test_addFirstNameLabelsToTemplate_should_update_template_manager_with_first_name_labels() {
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);
        TemplateManager::getManager()->setData([
            'templateVariableName' => ['existingKey' => 'existingValue']
        ]);
        $target = new PPRFirstNamesManagementService($submissionUtil);
        $target->addFirstNameLabelsToTemplate('templateVariableName');
        $result = TemplateManager::getManager()->getTemplateVars('templateVariableName');

        $this->assertFirstNameLabels(['existingKey' => 'existingValue'], $result);
    }

    public function test_addFirstNameLabelsToTemplate_should_create_template_variable_if_not_available() {
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);
        TemplateManager::getManager()->setData([
            'templateVariableName' => null
        ]);
        $target = new PPRFirstNamesManagementService($submissionUtil);
        $target->addFirstNameLabelsToTemplate('templateVariableName');
        $result = TemplateManager::getManager()->getTemplateVars('templateVariableName');

        $this->assertFirstNameLabels([], $result);
    }

    private function assertFirstNameLabels($expectedLabels, $result) {
        $expectedLabels['reviewerName'] = __('review.ppr.reviewer.name.label');
        $expectedLabels['reviewerFullName'] = __('review.ppr.reviewer.name.label');
        $expectedLabels['reviewerFirstName'] = __('review.ppr.reviewer.firstName.label');
        $expectedLabels['firstNameOnly'] = __('review.ppr.reviewer.firstName.label');
        $expectedLabels['authorName'] = __('review.ppr.author.name.label');
        $expectedLabels['authorFullName'] = __('review.ppr.author.name.label');
        $expectedLabels['authorFirstName'] = __('review.ppr.author.firstName.label');
        $expectedLabels['editorName'] = __('review.ppr.editor.name.label');
        $expectedLabels['editorFullName'] = __('review.ppr.editor.name.label');
        $expectedLabels['editorFirstName'] = __('review.ppr.editor.firstName.label');
        $this->assertEquals($expectedLabels, $result);
    }

    public function test_addFirstNamesToEmailTemplate_should_not_update_mail_template_when_no_submission_provided() {
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);
        $mailTemplate = $this->createSubmissionEmailTemplate(false);
        $submissionUtil->expects($this->never())->method($this->anything());
        $mailTemplate->expects($this->never())->method('addPrivateParam');

        $target = new PPRFirstNamesManagementService($submissionUtil);
        $target->addFirstNamesToEmailTemplate($mailTemplate);
    }

    public function test_addFirstNamesToEmailTemplate_should_add_author_editor_reviewer_names() {
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);
        $mailTemplate = $this->createSubmissionEmailTemplate();
        $this->addEditorAndAuthor($submissionUtil);
        $reviewerId = $this->getRandomId();
        $mailTemplate->method('getData')->with('reviewerId')->willReturn($reviewerId);
        $this->addReviewer($submissionUtil, $reviewerId);

        $mailTemplate->expects($this->exactly(10))->method('addPrivateParam')
            ->withConsecutive(
                ['{$authorName}', 'authorFullName'], ['{$authorFullName}', 'authorFullName'], ['{$authorFirstName}', 'authorFirstName'],
                ['{$editorName}', 'editorFullName'], ['{$editorFullName}', 'editorFullName'], ['{$editorFirstName}', 'editorFirstName'],
                ['{$reviewerName}', 'reviewerFullName'], ['{$reviewerFullName}', 'reviewerFullName'], ['{$reviewerFirstName}', 'reviewerFirstName'], ['{$firstNameOnly}', 'reviewerFirstName']);

        $target = new PPRFirstNamesManagementService($submissionUtil);
        $target->addFirstNamesToEmailTemplate($mailTemplate);
    }

    public function test_addFirstNamesToEmailTemplate_should_handle_missing_author_editor_and_reviewer() {
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);
        $mailTemplate = $this->createSubmissionEmailTemplate();
        $this->addEditorAndAuthor($submissionUtil, true);

        $missingName = __('ppr.user.missing.name');
        $mailTemplate->expects($this->exactly(10))->method('addPrivateParam')
            ->withConsecutive(
                ['{$authorName}', $missingName], ['{$authorFullName}', $missingName], ['{$authorFirstName}', $missingName],
                ['{$editorName}', $missingName], ['{$editorFullName}', $missingName], ['{$editorFirstName}', $missingName],
                ['{$reviewerName}', $missingName], ['{$reviewerFullName}', $missingName], ['{$reviewerFirstName}', $missingName], ['{$firstNameOnly}', $missingName]);

        $target = new PPRFirstNamesManagementService($submissionUtil);
        $target->addFirstNamesToEmailTemplate($mailTemplate);
    }

    public function test_replaceFirstNames_should_replace_author_editor_reviewer_first_names_when_submission_is_provided_and_reviewer_found() {
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);
        $submission = $this->getTestUtil()->createSubmission();
        $reviewerId = $this->getRandomId();
        $author = $this->getTestUtil()->createUser($this->getRandomId(), 'AuthorName', 'AuthorFirst');
        $editor = $this->getTestUtil()->createUser($this->getRandomId(), 'EditorName', 'EditorFirst');
        $reviewer = $this->getTestUtil()->createUser($reviewerId, 'ReviewerName', 'ReviewerFirst');
        $submissionUtil->expects($this->once())->method('getSubmissionAuthors')->with($submission->getId())->willReturn([$author]);
        $submissionUtil->expects($this->once())->method('getSubmissionEditors')->with($submission->getId(), $submission->getContextId())->willReturn([$editor]);
        $submissionUtil->expects($this->once())->method('getUser')->with($reviewerId)->willReturn($reviewer);
        $textToReplace = $this->createTextToReplace();
        $expectedText = 'Author: AuthorName - AuthorName - AuthorFirst, Editor: EditorName - EditorName - EditorFirst, Reviewer: ReviewerName - ReviewerName - ReviewerFirst - ReviewerFirst';

        $target = new PPRFirstNamesManagementService($submissionUtil);
        $result = $target->replaceFirstNames($textToReplace, $submission, $reviewerId);
        $this->assertEquals($expectedText, $result);
    }

    public function test_replaceFirstNames_should_handle_missing_submission() {
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);
        $reviewerId = $this->getRandomId();
        $reviewer = $this->getTestUtil()->createUser($reviewerId, 'ReviewerName', 'ReviewerFirst');
        $submissionUtil->expects($this->never())->method('getSubmissionAuthors');
        $submissionUtil->expects($this->never())->method('getSubmissionEditors');
        $submissionUtil->expects($this->once())->method('getUser')->with($reviewerId)->willReturn($reviewer);
        $textToReplace = $this->createTextToReplace();
        $missingName = __('ppr.user.missing.name');
        $expectedText = sprintf('Author: %s - %s - %s, Editor: %s - %s - %s, Reviewer: ReviewerName - ReviewerName - ReviewerFirst - ReviewerFirst', $missingName, $missingName, $missingName, $missingName, $missingName, $missingName);

        $target = new PPRFirstNamesManagementService($submissionUtil);
        $result = $target->replaceFirstNames($textToReplace, null, $reviewerId);
        $this->assertEquals($expectedText, $result);
    }

    public function test_replaceFirstNames_should_handle_missing_author_editor_reviewer() {
        $submissionUtil = $this->createMock(PPRSubmissionUtil::class);
        $submission = $this->getTestUtil()->createSubmission();
        $reviewerId = $this->getRandomId();
        $submissionUtil->expects($this->once())->method('getSubmissionAuthors')->with($submission->getId())->willReturn([]);
        $submissionUtil->expects($this->once())->method('getSubmissionEditors')->with($submission->getId(), $submission->getContextId())->willReturn([]);
        $submissionUtil->expects($this->once())->method('getUser')->with($reviewerId)->willReturn(null);
        $textToReplace = $this->createTextToReplace();
        $missingName = __('ppr.user.missing.name');
        $expectedText = sprintf('Author: %s - %s - %s, Editor: %s - %s - %s, Reviewer: %s - %s - %s - %s', $missingName, $missingName, $missingName, $missingName, $missingName, $missingName, $missingName, $missingName, $missingName, $missingName);

        $target = new PPRFirstNamesManagementService($submissionUtil);
        $result = $target->replaceFirstNames($textToReplace, $submission, $reviewerId);
        $this->assertEquals($expectedText, $result);
    }

    private function createSubmissionEmailTemplate($createSubmission = true) {
        $submissionMailTemplate = $this->createMock(SubmissionMailTemplate::class);
        if ($createSubmission) {
            $submission = $this->getTestUtil()->createSubmissionWithAuthors('AuthorName');
            $submissionMailTemplate->submission = $submission;
        }
        return $submissionMailTemplate;
    }

    private function addEditorAndAuthor($submissionUtil, $emptyUsers = false) {
        $editors = $emptyUsers ? [] : [$this->getTestUtil()->createUser($this->getRandomId(), 'editorFullName', 'editorFirstName')];
        $authors = $emptyUsers ? [] : [$this->getTestUtil()->createUser($this->getRandomId(), 'authorFullName', 'authorFirstName')];

        $submissionUtil->expects($this->once())->method('getSubmissionEditors')->willReturn($editors);
        $submissionUtil->expects($this->once())->method('getSubmissionAuthors')->willReturn($authors);
    }

    private function addReviewer($submissionUtil, $reviewerId) {
        $reviewer =$this->getTestUtil()->createUser($this->getRandomId(), 'reviewerFullName', 'reviewerFirstName');
        $submissionUtil->expects($this->once())->method('getUser')->with($reviewerId)->willReturn($reviewer);
        return $reviewer;
    }

    private function createTextToReplace() {
        $text = [];
        $text[] = 'Author: {$authorName} - {$authorFullName} - {$authorFirstName}';
        $text[] = 'Editor: {$editorName} - {$editorFullName} - {$editorFirstName}';
        $text[] = 'Reviewer: {$reviewerName} - {$reviewerFullName} - {$reviewerFirstName} - {$firstNameOnly}';

        return implode(', ', $text);
    }
}