<?php

import('lib.pkp.classes.linkAction.LinkAction');
import('lib.pkp.classes.linkAction.request.AjaxModal');
class PPRCompleteSubmissionService {

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->submissionCompleteEnabled()) {
            HookRegistry::register('Schema::get::submission', array($this, 'addFieldsToSubmissionDatabaseSchema'));
            HookRegistry::register('TemplateManager::fetch', array($this, 'addCompleteSubmissionButton'));
            HookRegistry::register('LoadComponentHandler', array($this, 'addPPRCompleteSubmissionHandler'));
        }
    }

    /**
     * This is needed to store the new fields in the database.
     * Only the fields in the schema are stored in the database
     */
    function addFieldsToSubmissionDatabaseSchema($hookName, $args) {
        $schema = $args[0];
        $schema->properties->completedDate = new stdClass();
        $schema->properties->completedDate->type = 'string';
        $schema->properties->completedDate->validation = ["nullable", "date:Y-m-d H:i:s"];
    }

    /**
     * Update editorial actions component to add the complete submission button
     */
    function addCompleteSubmissionButton($hookName, $args) {
        $templateName = $args[1];

        if ($templateName === 'workflow/editorialLinkActions.tpl') {
            // THIS TEMPLATE IS RENDERED FROM THE WorkflowHandler
            $request = Application::get()->getRequest();
            $templateMgr = $args[0];
            $submissionStatus = $templateMgr->getTemplateVars('submissionStatus');

            $pprActionType = 'activate';
            if ($submissionStatus === STATUS_QUEUED) {
                $pprActionType = 'complete';
            }

            $submissionId = $request->getUserVar('submissionId');

            $submissionActionModalUrl = $request->getDispatcher()->url(
                $request, ROUTE_COMPONENT, null,
                "pprPlugin.services.CompleteSubmissionHandler",
                "show{$pprActionType}", null, ['submissionId' => $submissionId]
            );

            // ADD COMPLETE SUBMISSION ACTION TO EDITORIAL ACTIONS COMPONENT
            $pprButtonAction = new LinkAction(
                'ppr_submission_action',
                new AjaxModal(
                    $submissionActionModalUrl,
                    __("submission.{$pprActionType}.form.title"),
                    'ppr_submission_action_modal'
                ),
                __("submission.{$pprActionType}.button.title")
            );

            // THE OVERRIDDEN TEMPLATE WILL RENDER NEW ACTION
            $templateVars = ['pprActionType' => $pprActionType, 'pprAction' => $pprButtonAction];
            $templateMgr->assign($templateVars);

        }

        return false;
    }


    /**
     * Add the handler to render the confirmation form and update the submission status to published (aka completed)
     */
    function addPPRCompleteSubmissionHandler($hookName, $args) {
        $component =& $args[0];
        if ($component == 'pprPlugin.services.CompleteSubmissionHandler') {
            // LOAD THE PPR SERVICE FROM THE PLUGIN REPO
            $component =str_replace('/', '.', $this->pprPlugin->getPluginPath()) . '.services.submission.CompleteSubmissionHandler';
            return true;
        }
        return false;
    }

}