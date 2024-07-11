<?php

import('lib.pkp.classes.linkAction.LinkAction');
import('lib.pkp.classes.linkAction.request.AjaxModal');

/**
 * Service to attach additional functionality to editor actions panel:
 *  - Add the close submission functionality
 *  - Add the submission approved email to authors
 *
 * Issue 060, Issue 108
 */
class PPRSubmissionActionsService {

    private $pprPlugin;

    private $pprObjectFactory;

    public function __construct($plugin, $pprObjectFactory = null) {
        $this->pprPlugin = $plugin;
        $this->pprPlugin->import('util.PPRObjectFactory');
        $this->pprObjectFactory = $pprObjectFactory ?: new PPRObjectFactory();
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->submissionCloseEnabled()) {
            HookRegistry::register('Schema::get::submission', array($this, 'addFieldsToSubmissionDatabaseSchema'));
            HookRegistry::register('TemplateManager::fetch', array($this, 'addActionSubmissionButton'));
            HookRegistry::register('LoadComponentHandler', array($this, 'addPPRSubmissionActionsHandler'));
        }

        if ($this->pprPlugin->getPluginSettings()->submissionApprovedEmailEnabled()) {
            HookRegistry::register('initiateexternalreviewform::execute', array($this, 'sendSubmissionApprovedEmail'));
        }
    }

    /**
     * This is needed to store the new fields in the database.
     * Only the fields in the schema are stored in the database
     */
    function addFieldsToSubmissionDatabaseSchema($hookName, $args) {
        $schema = $args[0];
        $schema->properties->closedDate = new stdClass();
        $schema->properties->closedDate->type = 'string';
        $schema->properties->closedDate->validation = ["nullable", "date:Y-m-d H:i:s"];
    }

    /**
     * Update editorial actions component to add the close/open submission button
     */
    function addActionSubmissionButton($hookName, $args) {
        $templateName = $args[1];

        if ($templateName === 'workflow/editorialLinkActions.tpl') {
            // THIS TEMPLATE IS RENDERED FROM THE WorkflowHandler AND OVERRIDDEN IN THE PLUGIN
            // WE NEED TO ADD DATA TO THE TEMPLATE MANAGER
            $request = Application::get()->getRequest();
            $templateMgr = $args[0];
            $submissionStatus = $templateMgr->getTemplateVars('submissionStatus');

            $pprActionType = 'open';
            if ($submissionStatus === STATUS_QUEUED) {
                $pprActionType = 'close';
            }

            $submissionId = $request->getUserVar('submissionId');

            $submissionActionModalUrl = $request->getDispatcher()->url(
                $request, ROUTE_COMPONENT, null,
                "pprPlugin.services.SubmissionActionsHandler",
                "show{$pprActionType}", null, ['submissionId' => $submissionId]
            );

            // ADD SUBMISSION ACTION BUTTON TO EDITORIAL ACTIONS COMPONENT
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
     * Add the handler to render the confirmation form and update the submission status to published (aka closed)
     */
    function addPPRSubmissionActionsHandler($hookName, $args) {
        $component =& $args[0];
        if ($component == 'pprPlugin.services.SubmissionActionsHandler') {
            // LOAD THE PPR SERVICE FROM THE PLUGIN REPO
            $component =str_replace('/', '.', $this->pprPlugin->getPluginPath()) . '.services.submission.SubmissionActionsHandler';
            return true;
        }

        return false;
    }

    /**
     * Send the submission approved confirmation email to the author
     */
    function sendSubmissionApprovedEmail($hookName, $args) {
        $form = $args[0];
        $submission = $form->getSubmission();
        $submissionId = $submission->getId();
        $author = $this->getSubmissionAuthor($submissionId);
        if (!$author) {
            error_log("PPR[sendSubmissionApprovedEmail] submissionId=$submissionId message=no author found");
            return;
        }

        $request = Application::get()->getRequest();
        $submissionUrl = $request->getDispatcher()->url($request, ROUTE_PAGE, null, 'authorDashboard', 'submission', $submissionId);
        $email = $this->pprObjectFactory->submissionMailTemplate($submission, 'PPR_SUBMISSION_APPROVED');
        $email->setContext(Application::get()->getRequest()->getContext());
        $email->addRecipient($author->getEmail(), $author->getFullName());
        // EDITOR NAME WILL BE ADDED BY email/PPRFirstNameEmailService
        $email->assignParams([
            'authorFullName' => htmlspecialchars($author->getFullName()),
            'authorFirstName' => htmlspecialchars($author->getLocalizedGivenName()),
            'submissionUrl' => $submissionUrl,
        ]);
        $email->send();
    }

    private function getSubmissionAuthor($submissionId) {
        $submissionAuthors = $this->pprObjectFactory->submissionUtil()->getSubmissionAuthors($submissionId);
        //GET FIRST AUTHOR OR NULL
        return empty($submissionAuthors) ? null : reset($submissionAuthors);
    }
}