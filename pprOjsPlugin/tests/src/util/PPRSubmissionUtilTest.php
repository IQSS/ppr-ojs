<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');

import('classes.submission.SubmissionDAO');

class PPRSubmissionUtilTest extends PPRTestCase {

    const CONTEXT_ID = 998765;

    public function test_getEditorGroupId_should_return_groupId_that_matches_associate_editor_text() {
        $this->createUserGroups([
            1234 => 'NoMatch',
            9999 => __('tasks.ppr.editor.groupName'),
            1235 => 'NoMatch',
        ]);

        $target = new PPRSubmissionUtil();
        $result = $target->getEditorGroupId(self::CONTEXT_ID);

        $this->assertEquals(9999, $result);
    }

    public function test_geGroupId_should_return_the_groupId_that_matches_group_text() {
        $this->createUserGroups([
            123 => 'One',
            9999 => 'GroupNameMatch',
            1234 => 'Other',
        ]);

        $target = new PPRSubmissionUtil();
        $result = $target->getGroupId(self::CONTEXT_ID, 'GroupNameMatch');

        $this->assertEquals(9999, $result);
    }

    public function test_geGroupId_should_return_the_first_groupId_that_matches_associate_editor_text() {
        $this->createUserGroups([
            123 => 'No_Match',
            9999 => 'GroupNameMatch',
            1234 => 'GroupNameMatch',
        ]);

        $target = new PPRSubmissionUtil();
        $result = $target->getGroupId(self::CONTEXT_ID, 'GroupNameMatch');

        $this->assertEquals(9999, $result);
    }

    public function test_getGroupId_should_return_null_when_no_matches() {
        $this->createUserGroups([
            1234 => 'One',
            12345 => 'Other',
            12342 => 'Third',
        ]);

        $target = new PPRSubmissionUtil();
        $result = $target->getGroupId(self::CONTEXT_ID, 'NoMatch');

        $this->assertNull($result);
    }

    public function test_getGroupId_should_return_null_when_no_groups() {
        $this->createUserGroups([]);

        $target = new PPRSubmissionUtil();
        $result = $target->getGroupId(self::CONTEXT_ID, 'GroupName');

        $this->assertNull($result);
    }

    public function test_getSubmissionEditors_returns_empty_array_when_no_editor_group_available() {
        $submissionId = $this->getRandomId();
        $this->createUserGroups([]);

        $target = new PPRSubmissionUtil();
        $result = $target->getSubmissionEditors($submissionId, self::CONTEXT_ID);

        $this->assertEmpty($result);
    }

    public function test_getSubmissionEditors_returns_empty_array_when_submission_has_no_editor_assigment() {
        $submissionId = $this->getRandomId();
        $this->createUserGroups([9999 => __('tasks.ppr.editor.groupName')]);
        $this->createAssignments($submissionId, [], 9999);
        $userDao = $this->createMock(UserDAO::class);
        DAORegistry::registerDAO('UserDAO', $userDao);
        $userDao->expects($this->never())->method($this->anything());

        $target = new PPRSubmissionUtil();
        $result = $target->getSubmissionEditors($submissionId, self::CONTEXT_ID);

        $this->assertEmpty($result);
    }

    public function test_getSubmissionEditors_returns_empty_array_when_submission_editor_assignment_cannot_be_found() {
        $submissionId = $this->getRandomId();
        $this->createUserGroups([9999 => __('tasks.ppr.editor.groupName')]);
        $this->createAssignments($submissionId, [9999], 9999);
        $this->createUser(9999, null);

        $target = new PPRSubmissionUtil();
        $result = $target->getSubmissionEditors($submissionId, self::CONTEXT_ID);

        $this->assertEmpty($result);
    }

    public function test_getSubmissionEditors_returns_editor_user_when_editor_group_available_and_submission_has_editor_assignment() {
        $submissionId = $this->getRandomId();
        $this->createUserGroups([9999 => __('tasks.ppr.editor.groupName')]);
        $this->createAssignments($submissionId, [9999], 9999);
        $expectedUser = $this->createUser(9999, 'EditorName');

        $target = new PPRSubmissionUtil();
        $result = $target->getSubmissionEditors($submissionId, self::CONTEXT_ID);

        $this->assertEquals([$expectedUser], $result);
    }

    public function test_getSubmissionAuthors_returns_author_user_when_submission_has_author_assignment() {
        $submissionId = $this->getRandomId();
        $userId = $this->getRandomId();
        $this->createAuthorAssignments($submissionId, [$userId]);
        $expectedUser = $this->createUser($userId, 'AuthorName');

        $target = new PPRSubmissionUtil();
        $result = $target->getSubmissionAuthors($submissionId);

        $this->assertEquals([$expectedUser], $result);
    }

    public function test_getSubmissionAuthors_returns_empty_array_when_submission_does_not_have_author_assignment() {
        $submissionId = $this->getRandomId();
        $this->createAuthorAssignments($submissionId, []);

        $target = new PPRSubmissionUtil();
        $result = $target->getSubmissionAuthors($submissionId);

        $this->assertEmpty($result);
    }

    public function test_getSubmissionAuthors_returns_empty_array_when_submission_author_assignment_cannot_be_found() {
        $submissionId = $this->getRandomId();
        $userId = $this->getRandomId();
        $this->createAuthorAssignments($submissionId, [$userId]);
        $this->createUser($userId, null);

        $target = new PPRSubmissionUtil();
        $result = $target->getSubmissionAuthors($submissionId);

        $this->assertEmpty($result);
    }

    public function test_getReviewer_returns_reviewAssignment_reviewer() {
        $reviewAssignmentId = $this->getRandomId();
        $reviewerId = $this->getRandomId();
        $this->createReviewAssignment($reviewAssignmentId, $reviewerId);
        $expectedUser = $this->createUser($reviewerId, 'Reviewer');

        $target = new PPRSubmissionUtil();
        $result = $target->getReviewer($reviewAssignmentId);

        $this->assertEquals($expectedUser, $result);
    }

    public function test_getReviewer_returns_null_when_reviewAssignment_cannot_be_found() {
        $reviewAssignmentId = $this->getRandomId();
        $this->createReviewAssignment($reviewAssignmentId, null);

        $target = new PPRSubmissionUtil();
        $result = $target->getReviewer($reviewAssignmentId);

        $this->assertNull($result);
    }

    public function test_getReviewer_returns_null_when_reviewAssignment_reviewer_cannot_be_found() {
        $reviewAssignmentId = $this->getRandomId();
        $reviewerId = $this->getRandomId();
        $this->createReviewAssignment($reviewAssignmentId, $reviewerId);
        $this->createUser($reviewerId, null);

        $target = new PPRSubmissionUtil();
        $result = $target->getReviewer($reviewAssignmentId);

        $this->assertNull($result);
    }

    public function test_getUser_returns_user() {
        $userId = $this->getRandomId();
        $expectedUser = $this->createUser($userId, 'UserName');

        $target = new PPRSubmissionUtil();
        $result = $target->getUser($userId);

        $this->assertEquals($expectedUser, $result);
    }

    public function test_getUser_returns_null_when_user_cannot_be_found() {
        $userId = $this->getRandomId();
        $this->createUser($userId, null);

        $target = new PPRSubmissionUtil();
        $result = $target->getUser($userId);

        $this->assertNull($result);
    }

    public function test_getSubmission_returns_submission() {
        $submissionId = $this->getRandomId();
        $expectedSubmission = $this->createSubmission($submissionId, 'UserName');

        $target = new PPRSubmissionUtil();
        $result = $target->getSubmission($submissionId);

        $this->assertEquals($expectedSubmission, $result);
    }

    public function test_getSubmission_returns_null_when_submission_cannot_be_found() {
        $submissionId = $this->getRandomId();
        $expectedSubmission = $this->createSubmission($submissionId, null);

        $target = new PPRSubmissionUtil();
        $result = $target->getSubmission($submissionId);

        $this->assertNull($result);
    }

    private function createUserGroups($groupNames) {
        $groups = [];
        foreach ($groupNames as $groupId => $groupName) {
            $userGroup = $this->createMock(UserGroup::class);
            $userGroup->method('getId')->willReturn($groupId);
            $userGroup->method('getLocalizedName')->willReturn($groupName);
            $groups[] = $userGroup;
        }

        $userGroupDao = $this->createMock(UserGroupDAO::class);
        DAORegistry::registerDAO('UserGroupDAO', $userGroupDao);
        $userGroupDao->expects($this->once())->method('getByContextId')->willReturn($this->resultFactoryMock($groups));
        return $userGroupDao;
    }

    private function createAssignments($submissionId, $userAssignments, $editorGroupId) {
        $assignmentDao = $this->createMock(StageAssignmentDAO::class);
        DAORegistry::registerDAO('StageAssignmentDAO', $assignmentDao);
        $assignments = [];
        foreach ($userAssignments as $groupId) {
            $assignment = $this->createMock(StageAssignment::class);
            $assignment->method('getUserGroupId')->willReturn($groupId);
            $assignment->method('getUserId')->willReturn($groupId);
            $assignments[] = $assignment;
        }
        $assignmentDao->expects($this->once())->method('getBySubmissionAndStageId')->with($submissionId, WORKFLOW_STAGE_ID_SUBMISSION, $editorGroupId)->willReturn($this->resultFactoryMock($assignments));
        return $assignmentDao;
    }

    private function createAuthorAssignments($submissionId, $userAssignments) {
        $assignmentDao = $this->createMock(StageAssignmentDAO::class);
        DAORegistry::registerDAO('StageAssignmentDAO', $assignmentDao);
        $assignments = [];
        foreach ($userAssignments as $groupId) {
            $assignment = $this->createMock(StageAssignment::class);
            $assignment->method('getUserGroupId')->willReturn($groupId);
            $assignment->method('getUserId')->willReturn($groupId);
            $assignments[] = $assignment;
        }
        $assignmentDao->expects($this->once())->method('getBySubmissionAndRoleId')->with($submissionId, ROLE_ID_AUTHOR, WORKFLOW_STAGE_ID_SUBMISSION)->willReturn($this->resultFactoryMock($assignments));
        return $assignmentDao;
    }

    private function createReviewAssignment($reviewAssignmentId, $reviewerId) {
        $reviewAssignmentDao = $this->createMock(ReviewAssignmentDAO::class);
        DAORegistry::registerDAO('ReviewAssignmentDAO', $reviewAssignmentDao);
        $assignment = null;
        if ($reviewerId) {
            $assignment = $this->getTestUtil()->createReview($reviewerId);
        }
        $reviewAssignmentDao->expects($this->once())->method('getById')->with($reviewAssignmentId)->willReturn($assignment);
        return $reviewAssignmentDao;
    }

    private function createUser($userId, $name) {
        $user = null;
        if ($name) {
            $user = $this->getTestUtil()->createUser($userId, $name, $name);
        }
        $userDao = $this->createMock(UserDAO::class);
        DAORegistry::registerDAO('UserDAO', $userDao);
        $userDao->expects($this->once())->method('getById')->with($userId)->willReturn($user);
        return $user;
    }

    private function createSubmission($submissionId, $name) {
        $submission = null;
        if ($name) {
            $submission = $this->getTestUtil()->createSubmission($submissionId);
        }
        $submissionDao = $this->createMock(SubmissionDAO::class);
        DAORegistry::registerDAO('SubmissionDAO', $submissionDao);
        $submissionDao->expects($this->once())->method('getById')->with($submissionId)->willReturn($submission);
        return $submission;
    }
}