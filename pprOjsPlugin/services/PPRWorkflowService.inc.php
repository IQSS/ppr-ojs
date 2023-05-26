<?php

/**
 * Service to add/update components in the OJS workflow page
 * Workflow page controls the submission and review workflows
 */
class PPRWorkflowService {
    private $pprPlugin;
    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->submissionCustomFieldsEnabled()) {
            HookRegistry::register('Template::Workflow', array($this, 'addCommentsForReviewerToWorkflow'));
        }

        if ($this->pprPlugin->getPluginSettings()->displayContributorsEnabled()) {
            HookRegistry::register('Template::Workflow', array($this, 'addContributorsToWorkflow'));
            HookRegistry::register('LoadComponentHandler', array($this, 'addPPRAuthorGridHandler'));

        }

        if ($this->pprPlugin->getPluginSettings()->displaySuggestedReviewersEnabled()) {
            HookRegistry::register('Template::Workflow', array($this, 'addSuggestedReviewersToWorkflow'));
        }
    }

    /**
     * Custom AuthorGridHandler to add the institution, category, and department data to the contributors component.
     * @param $hookName
     * @param $hookArgs
     * @return false
     */
    function addPPRAuthorGridHandler($hookName, $hookArgs) {
        $component =& $hookArgs[0];
        if ($component === 'grid.users.author.AuthorGridHandler') {
            // LOAD THE PPR AUTHOR HANDLER FROM THE PLUGIN REPO
            $component =str_replace('/', '.', $this->pprPlugin->getPluginPath()) . '.services.PPRAuthorGridHandler';
            return true;
        }
        return false;
    }

    function addCommentsForReviewerToWorkflow($hookName, $hookArgs) {
        $smarty =& $hookArgs[1];
        $output =& $hookArgs[2];

        // ADD THE SUGGESTED REVIEWERS COMPONENT TO THE WORKFLOW TEMPLATE
        $output .= $smarty->fetch($this->pprPlugin->getTemplateResource('ppr/workflowCommentsForReviewer.tpl'));

        return false;
    }

    /**
     * Adds the comments for reviewer component into the Workflow page using an existing template hook
     */
    function addSuggestedReviewersToWorkflow($hookName, $hookArgs) {
        $smarty =& $hookArgs[1];
        $output =& $hookArgs[2];

        // ADD THE SUGGESTED REVIEWERS COMPONENT TO THE WORKFLOW TEMPLATE
        $output .= $smarty->fetch($this->pprPlugin->getTemplateResource('ppr/workflowSuggestedReviewers.tpl'));

        return false;
    }

    /**
     * Adds the contributors component into the Workflow page using an existing template hook
     */
    function addContributorsToWorkflow($hookName, $hookArgs) {
        $smarty =& $hookArgs[1];
        $output =& $hookArgs[2];

        // ADD THE CONTRIBUTORS COMPONENT TO THE WORKFLOW TEMPLATE
        $output .= $smarty->fetch($this->pprPlugin->getTemplateResource('ppr/workflowContributors.tpl'));

        return false;
    }
}