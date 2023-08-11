<?php

/**
 * Service to add the managing editor to emails related to reviews
 */
class PPRReviewAddEditorEmailService {

    const TEMPLATES_MANAGING_EDITOR_BCC = ['REVIEW_ACK'];

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->reviewAddEditorToBccEnabled()) {
            HookRegistry::register('Mail::send', array($this, 'addManagingEditorToBCC'));
        }
    }

    /**
     * Adds the system Managing Editor as BCC to the thank reviewer email
     */
    function addManagingEditorToBCC($hookName, $hookArgs) {
        $emailTemplate =& $hookArgs[0];
        if ($emailTemplate instanceof MailTemplate && in_array($emailTemplate->emailKey,self::TEMPLATES_MANAGING_EDITOR_BCC)) {
            $editors = $this->findEditors($this->pprPlugin->getPluginSettings()->getContextId());
            foreach($editors as $managingEditor) {
                $emailTemplate->addBcc($managingEditor->getEmail(), $managingEditor->getFullName());
            }
        }

        return false;
    }

    private function findEditors($contextId) {
        $MANAGING_EDITOR_GROUP_NAME = __('tasks.ppr.managingEditor.groupName');
        $userGroupDao = DAORegistry::getDAO('UserGroupDAO');
        $userGroups = $userGroupDao->getByContextId($contextId)->toArray();
        $editorGroupId = null;
        foreach ($userGroups as $userGroup) {
            if(0 === strcasecmp($userGroup->getLocalizedName(), $MANAGING_EDITOR_GROUP_NAME)) {
                $editorGroupId = $userGroup->getId();
                break;
            }
        }

        return $editorGroupId ? $userGroupDao->getUsersById($editorGroupId, $contextId)->toArray() : [];
    }

}