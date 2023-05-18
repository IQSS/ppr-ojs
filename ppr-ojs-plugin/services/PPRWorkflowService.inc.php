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
        if ($this->pprPlugin->getPluginSettings()->displayContributorsEnabled()) {
            HookRegistry::register('authorgridhandler::initfeatures', array($this, 'updateContributorsGrid'));
            HookRegistry::register('Template::Workflow', array($this, 'addContributorsToWorkflow'));
        }

        if ($this->pprPlugin->getPluginSettings()->displaySuggestedReviewersEnabled()) {
            HookRegistry::register('Template::Workflow', array($this, 'addSuggestedReviewersToWorkflow'));
        }
    }


    /**
     * Updates to the AuthorGridHandler to add the institution data to the contributors component.
     * @param $hookName
     * @param $hookArgs
     * @return false
     */
    public function updateContributorsGrid($hookName, $hookArgs) {
        $authorGridHandler = $hookArgs[0];
        $this->pprPlugin->import('services.PPRAuthorGridCellProvider');
        $cellProvider = new PPRAuthorGridCellProvider($authorGridHandler->getPublication());
        $authorGridHandler->addColumn(new GridColumn('name')); //NEEDED TO KEEP THE ORDER AND MAKE INSTITUTION THE SECOND COLUMN
        $authorGridHandler->addColumn(
            new GridColumn(
                'institution',
                'user.affiliation',
                null,
                null,
                $cellProvider,
                array('width' => 30, 'alignment' => COLUMN_ALIGNMENT_LEFT)
            )
        );

        return false;
    }

    /**
     * Adds the suggested reviewers component into the Workflow page using an existing template hook
     */
    public function addSuggestedReviewersToWorkflow($hookName, $hookArgs) {
        $smarty =& $hookArgs[1];
        $output =& $hookArgs[2];

        // ADD THE SUGGESTED REVIEWERS COMPONENT TO THE WORKFLOW TEMPLATE
        $output .= $smarty->fetch($this->pprPlugin->getTemplateResource('ppr/workflowSuggestedReviewers.tpl'));

        return false;
    }

    /**
     * Adds the contributors component into the Workflow page using an existing template hook
     */
    public function addContributorsToWorkflow($hookName, $hookArgs) {
        $smarty =& $hookArgs[1];
        $output =& $hookArgs[2];

        // ADD THE CONTRIBUTORS COMPONENT TO THE WORKFLOW TEMPLATE
        $output .= $smarty->fetch($this->pprPlugin->getTemplateResource('ppr/workflowContributors.tpl'));

        return false;
    }
}