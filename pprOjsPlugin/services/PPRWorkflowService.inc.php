<?php

/**
 * Service to add/update components in the OJS workflow page
 * Workflow page controls the submission and review workflows
 *
 * Issue 036, Issue 037, Issue 050, Issue 052
 */
class PPRWorkflowService {
    private $pprPlugin;
    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->authorDashboardSurveyHtml()) {
            HookRegistry::register('Template::Workflow', array($this, 'addAuthorDashboardSurveyToWorkflow'));
        }

        if ($this->pprPlugin->getPluginSettings()->submissionCommentsForReviewerEnabled()) {
            //ISSUE 037
            HookRegistry::register('Template::Workflow', array($this, 'addCommentsForReviewerToWorkflow'));
        }

        if ($this->pprPlugin->getPluginSettings()->submissionResearchTypeEnabled()) {
            HookRegistry::register('Template::Workflow', array($this, 'addResearchTypeToWorkflow'));
        }

        if ($this->pprPlugin->getPluginSettings()->displayContributorsEnabled()) {
            // ISSUE 050, ISSUE 052, ISSUE 064
            HookRegistry::register('Template::Workflow', array($this, 'addContributorsToWorkflow'));
            HookRegistry::register('LoadComponentHandler', array($this, 'addPPRAuthorGridHandler'));
        }

        if ($this->pprPlugin->getPluginSettings()->displaySuggestedReviewersEnabled()) {
            //ISSUE 036
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

    function addAuthorDashboardSurveyToWorkflow($hookName, $hookArgs) {
        $smarty =& $hookArgs[1];
        $output =& $hookArgs[2];

        if ( $smarty->tpl_vars['requestedPage'] == 'authorDashboard' && $smarty->tpl_vars['submission']) {
            // ADD THE AUTHOR SURVEY HTML TO THE WORKFLOW TEMPLATE
            $submission = $smarty->tpl_vars['submission']->value;
            $reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO');
            $reviews = $reviewAssignmentDao->getBySubmissionId($submission->getId());
            $atLeastOneReviewCompleted = array_reduce($reviews, function ($reviewCompleted, $review) {
                return $reviewCompleted || $review->getDateCompleted();
            }, false);

            if($atLeastOneReviewCompleted) {
                $output .= $smarty->fetch($this->pprPlugin->getTemplateResource('ppr/workflowSurvey.tpl'));
            }
        }

        return false;
    }

    function addCommentsForReviewerToWorkflow($hookName, $hookArgs) {
        $smarty =& $hookArgs[1];
        $output =& $hookArgs[2];

        // ADD THE COMMENTS FOR REVIEWER CUSTOM FIELD TO THE WORKFLOW TEMPLATE
        $output .= $smarty->fetch($this->pprPlugin->getTemplateResource('ppr/workflowCommentsForReviewer.tpl'));

        return false;
    }

    function addResearchTypeToWorkflow($hookName, $hookArgs) {
        $smarty =& $hookArgs[1];
        $output =& $hookArgs[2];

        // ADD THE RESEARCH TYPE CUSTOM FIELD TO THE WORKFLOW TEMPLATE
        $output .= $smarty->fetch($this->pprPlugin->getTemplateResource('ppr/workflowResearchType.tpl'));

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