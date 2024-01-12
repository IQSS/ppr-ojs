<?php

/**
 * Utility class for common methods related to submissions and to simplify testing by way of mocking this object.
 */
class PPRSubmissionUtil {

    private static $instance;
    private $groupCache;
    private $userCache;
    private $submissionCache;

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new PPRSubmissionUtil();
        }

        return self::$instance;
    }

    public function __construct() {
        $this->groupCache = [];
        $this->userCache = [];
        $this->submissionCache = [];
        error_log("PPR[PPRSubmissionUtil] created");
    }


    /**
     * Returns the users that are assigned to a submission as Associate Editor
     */
    public function getSubmissionEditors($submissionId, $contextId) {
        $editorUserGroupId = $this->getEditorGroupId($contextId);
        if (!$editorUserGroupId) {
            //EDITOR GROUP NOT CONFIGURED
            return null;
        }

        $stageAssignmentDao = DAORegistry::getDAO('StageAssignmentDAO');
        $editorAssignments = $stageAssignmentDao->getBySubmissionAndStageId($submissionId, WORKFLOW_STAGE_ID_SUBMISSION, $editorUserGroupId)->toArray();
        $editors = [];
        foreach ($editorAssignments as $assignment) {
            //REMOVE DUPLICATES
            if(array_key_exists($assignment->getUserId(), $editors)) continue;
            $editor = $this->getUser($assignment->getUserId());
            if($editor) $editors[$assignment->getUserId()] = $editor;
        }

        return array_values($editors);
    }

    /**
     * Returns a user that is assigned to a submission as an Author
     */
    public function getSubmissionAuthors($submissionId) {
        $stageAssignmentDao = DAORegistry::getDAO('StageAssignmentDAO');

        $assignedAuthors = $stageAssignmentDao->getBySubmissionAndRoleId($submissionId, ROLE_ID_AUTHOR, WORKFLOW_STAGE_ID_SUBMISSION)->toArray();
        $authors = [];
        foreach ($assignedAuthors as $assignment) {
            //REMOVE DUPLICATES
            if(array_key_exists($assignment->getUserId(), $authors)) continue;
            $author = $this->getUser($assignment->getUserId());
            if ($author) $authors[$assignment->getUserId()] = $author;
        }

        return array_values($authors);
    }

    public function getReviewer($reviewAssignmentId) {
        $reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO');
        $reviewAssignment = $reviewAssignmentDao->getById($reviewAssignmentId);
        if(!$reviewAssignment) {
            return null;
        }

        return $this->getUser($reviewAssignment->getReviewerId());
    }

    /**
     * Returns the user based on the userId
     */
    public function getUser($userId) {
        if (!array_key_exists($userId, $this->userCache)) {
            $userDao = DAORegistry::getDAO('UserDAO');
            $user = $userDao->getById($userId);
            $this->userCache[$userId] = $user;
        }

        return $this->userCache[$userId];
    }

    /**
     * Returns a submission based on the submissionId
     */
    public function getSubmission($submissionId) {
        if (!array_key_exists($submissionId, $this->submissionCache)) {
            $submissionDao = DAORegistry::getDAO('SubmissionDAO');
            $this->submissionCache[$submissionId] = $submissionDao->getById($submissionId);
        }

        return $this->submissionCache[$submissionId];
    }

    /**
     * Returns the Associate Editor groupId based on the group name text.
     */
    public function getEditorGroupId($contextId) {
        return $this->getGroupId($contextId, __('tasks.ppr.editor.groupName'));
    }

    /**
     * Returns the groupId based on the group name text.
     */
    public function getGroupId($contextId, $groupName) {
        if (!array_key_exists($groupName, $this->groupCache)) {
            $userGroupDao = DAORegistry::getDAO('UserGroupDAO');
            $userGroups = $userGroupDao->getByContextId($contextId)->toArray();

            $editorGroups = array_filter($userGroups, function($userGroup) use ($groupName) {
                return (0 === strcasecmp($userGroup->getLocalizedName(), $groupName));
            });

            $groupId = empty($editorGroups) ? null : reset($editorGroups)->getId();
            $this->groupCache[$groupName] = $groupId;
        }

        return $this->groupCache[$groupName];
    }
}