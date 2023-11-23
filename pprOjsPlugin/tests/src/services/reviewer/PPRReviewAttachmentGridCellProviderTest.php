<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.reviewer.PPRReviewAttachmentGridCellProvider');

import('lib.pkp.classes.submission.SubmissionFile');
import('lib.pkp.classes.controllers.grid.GridColumn');
import('lib.pkp.controllers.grid.files.SubmissionFilesGridRow');

class PPRReviewAttachmentGridCellProviderTest extends PPRTestCase {

    public function test_getTemplateVarsFromRowColumn_should_add_submission_label_for_submission_files() {
        [$row, $column] = $this->createRowAndColumn(SUBMISSION_FILE_REVIEW_FILE, true);
        $target = new PPRReviewAttachmentGridCellProvider();
        $result = $target->getTemplateVarsFromRowColumn($row, $column);

        $this->assertNotNull($result['label']);
        $label = $result['label'];
        $this->assertStringContainsString(__('review.ppr.files.type.submission'), $label);
    }

    public function test_getTemplateVarsFromRowColumn_should_add_new_label_for_review_files_not_sent() {
        [$row, $column] = $this->createRowAndColumn(SUBMISSION_FILE_REVIEW_ATTACHMENT, false);
        $target = new PPRReviewAttachmentGridCellProvider();
        $result = $target->getTemplateVarsFromRowColumn($row, $column);

        $this->assertNotNull($result['label']);
        $label = $result['label'];
        $this->assertStringContainsString(__('review.ppr.files.status.new'), $label);
    }

    public function test_getTemplateVarsFromRowColumn_should_add_sent_label_for_review_files_sent() {
        [$row, $column] = $this->createRowAndColumn(SUBMISSION_FILE_REVIEW_ATTACHMENT, true);
        $target = new PPRReviewAttachmentGridCellProvider();
        $result = $target->getTemplateVarsFromRowColumn($row, $column);

        $this->assertNotNull($result['label']);
        $label = $result['label'];
        $this->assertStringContainsString(__('review.ppr.files.status.sent'), $label);
    }

    public function test_getTemplateVarsFromRowColumn_should_add_owner_label_when_owner_available() {
        [$row, $column] = $this->createRowAndColumn(SUBMISSION_FILE_REVIEW_ATTACHMENT, true, 'Owner Name');
        $target = new PPRReviewAttachmentGridCellProvider();
        $result = $target->getTemplateVarsFromRowColumn($row, $column);

        $this->assertNotNull($result['label']);
        $label = $result['label'];
        $this->assertStringContainsString(__('review.ppr.files.status.sent'), $label);
        $this->assertStringContainsString('ppr-attachments-reviewer', $label);
        $this->assertStringContainsString('Owner Name', $label);
    }

    public function test_getTemplateVarsFromRowColumn_should_not_add_owner_label_when_owner_not_available() {
        [$row, $column] = $this->createRowAndColumn(SUBMISSION_FILE_REVIEW_ATTACHMENT, true, null);
        $target = new PPRReviewAttachmentGridCellProvider();
        $result = $target->getTemplateVarsFromRowColumn($row, $column);

        $this->assertNotNull($result['label']);
        $label = $result['label'];
        $this->assertStringContainsString(__('review.ppr.files.status.sent'), $label);
        $this->assertStringNotContainsString('ppr-attachments-reviewer', $label);
    }

    private function createRowAndColumn($fileType, $sent, $fileOwner = null) {
        $row = $this->createMock(SubmissionFilesGridRow::class);
        $ownerId = $this->getRandomId();
        $submissionFile = $this->createMock(SubmissionFile::class);
        $submissionFile->method('getFileStage')->willReturn($fileType);
        $submissionFile->method('getViewable')->willReturn($sent);
        $submissionFile->method('getUploaderUserId')->willReturn($ownerId);

        $owner = null;
        if ($fileOwner) {
            $owner = $this->getTestUtil()->createUser($ownerId, $fileOwner, $fileOwner);
            $submissionFile->method('getUploaderUserId')->willReturn($ownerId);
        }
        $userDao = $this->createMock(UserDAO::class);
        $userDao->expects($this->once())->method('getById')->with($ownerId)->willReturn($owner);
        DAORegistry::registerDAO('UserDAO', $userDao);

        $row->expects($this->once())->method('getData')->willReturn(['submissionFile' => $submissionFile]);

        $column = $this->createMock(GridColumn::class);
        $column->expects($this->once())->method('getId')->willReturn($this->getRandomId());
        return [$row, $column];
    }
}