<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');

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

    public function test_getEditorGroupId_should_return_the_first_groupId_that_matches_associate_editor_text() {
        $this->createUserGroups([
            123 => 'No_Match',
            9999 => __('tasks.ppr.editor.groupName'),
            1234 => __('tasks.ppr.editor.groupName'),
        ]);

        $target = new PPRSubmissionUtil();
        $result = $target->getEditorGroupId(self::CONTEXT_ID);

        $this->assertEquals(9999, $result);
    }

    public function test_getEditorGroupId_should_return_null_when_no_matches() {
        $this->createUserGroups([
            1234 => 'No_Match',
            12345 => 'Other',
            12342 => 'Third',
        ]);

        $target = new PPRSubmissionUtil();
        $result = $target->getEditorGroupId(self::CONTEXT_ID);

        $this->assertNull($result);
    }

    public function test_getEditorGroupId_should_return_null_when_no_groups() {
        $this->createUserGroups([]);

        $target = new PPRSubmissionUtil();
        $result = $target->getEditorGroupId(self::CONTEXT_ID);

        $this->assertNull($result);
    }

    public function test_getSubmissionEditor_returns_null_when_no_editor_group_available() {
        $submissionId = $this->getRandomId();
        $this->createUserGroups([]);

        $target = new PPRSubmissionUtil();
        $result = $target->getSubmissionEditor($submissionId, self::CONTEXT_ID);

        $this->assertNull($result);
    }

    public function test_getSubmissionEditor_returns_null_when_submission_has_no_editor_assigment() {
        $submissionId = $this->getRandomId();
        $this->createUserGroups([9999 => __('tasks.ppr.editor.groupName')]);
        $this->createAssignments($submissionId, [1234, 4567]);
        $userDao = $this->createMock(UserDAO::class);
        DAORegistry::registerDAO('UserDAO', $userDao);
        $userDao->expects($this->never())->method($this->anything());

        $target = new PPRSubmissionUtil();
        $result = $target->getSubmissionEditor($submissionId, self::CONTEXT_ID);

        $this->assertNull($result);
    }

    public function test_getSubmissionEditor_returns_null_when_submission_editor_assignment_cannot_be_found() {
        $submissionId = $this->getRandomId();
        $this->createUserGroups([9999 => __('tasks.ppr.editor.groupName')]);
        $this->createAssignments($submissionId, [9999]);
        $expectedUser = $this->createUser(9999, null);

        $target = new PPRSubmissionUtil();
        $result = $target->getSubmissionEditor($submissionId, self::CONTEXT_ID);

        $this->assertEquals($expectedUser, $result);
    }

    public function test_getSubmissionEditor_returns_editor_user_when_editor_group_available_and_submission_has_editor_assignment() {
        $submissionId = $this->getRandomId();
        $this->createUserGroups([9999 => __('tasks.ppr.editor.groupName')]);
        $this->createAssignments($submissionId, [9999]);
        $expectedUser = $this->createUser(9999, 'EditorName');

        $target = new PPRSubmissionUtil();
        $result = $target->getSubmissionEditor($submissionId, self::CONTEXT_ID);

        $this->assertEquals($expectedUser, $result);
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

    private function createAssignments($submissionId, $userAssignments) {
        $assignmentDao = $this->createMock(StageAssignmentDAO::class);
        DAORegistry::registerDAO('StageAssignmentDAO', $assignmentDao);
        $assignments = [];
        foreach ($userAssignments as $groupId) {
            $assignment = $this->createMock(StageAssignment::class);
            $assignment->method('getUserGroupId')->willReturn($groupId);
            $assignment->method('getUserId')->willReturn($groupId);
            $assignments[] = $assignment;
        }
        $assignmentDao->expects($this->once())->method('getBySubmissionAndStageId')->with($submissionId)->willReturn($this->resultFactoryMock($assignments));
        return $assignmentDao;
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
}