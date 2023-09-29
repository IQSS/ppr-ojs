<?php

/**
 * Utility class for common methods related to submissions and to simplify testing by way of mocking this object.
 */
class PPRSubmissionUtil {

    /**
     * Returns a user that is assigned to a submission as an Associate Editor
     */
    public function getSubmissionEditor($submissionId, $contextId) {
        $editorUserGroupId = $this->getEditorGroupId($contextId);
        if (!$editorUserGroupId) {
            //EDITOR GROUP NOT CONFIGURED
            return null;
        }

        $stageAssignmentDao = DAORegistry::getDAO('StageAssignmentDAO');
        $assignments = $stageAssignmentDao->getBySubmissionAndStageId($submissionId)->toArray();
        $editorAssignment = null;
        foreach ($assignments as $assignment) {
            if ($assignment->getUserGroupId() === $editorUserGroupId){
                $editorAssignment = $assignment;
                break;
            }
        }

        // EDITOR NOT FOUND BY DEFAULT
        $editor = null;
        if ($editorAssignment) {
            $userDao = DAORegistry::getDAO('UserDAO');
            $editor = $userDao->getById($editorAssignment->getUserId());
        }

        return $editor;
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
        $userGroupDao = DAORegistry::getDAO('UserGroupDAO');
        $userGroups = $userGroupDao->getByContextId($contextId)->toArray();

        $editorGroups = array_filter($userGroups, function($userGroup) use ($groupName) {
            return (0 === strcasecmp($userGroup->getLocalizedName(), $groupName));
        });

        return empty($editorGroups) ? null : reset($editorGroups)->getId();
    }
}