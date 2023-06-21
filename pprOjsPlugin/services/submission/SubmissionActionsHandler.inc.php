<?php

import('classes.handler.Handler');
import('lib.pkp.classes.core.JSONMessage');

/**
 * Handler to change the status of a submission to published (aka closed)
 */
class SubmissionActionsHandler extends Handler {

    function authorize($request, &$args, $roleAssignments) {
        import('lib.pkp.classes.security.authorization.internal.SubmissionRequiredPolicy');
        $this->addPolicy(new SubmissionRequiredPolicy($request, $args, 'submissionId'));

        return parent::authorize($request, $args, $roleAssignments);
    }

    /**
     * Shows the close submission confirmation form.
     */
    function showclose($args, $request) {
        return $this->_showConfirmationPage($request, 'close');
    }

    /**
     * Shows the open submission confirmation form.
     */
    function showopen($args, $request) {
        return $this->_showConfirmationPage($request, 'open');
    }

    private function _showConfirmationPage($request, $pprActionType) {
        $pprPlugin = PluginRegistry::getPlugin('generic', 'peerprereviewprogramplugin');
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('submissionId', $request->getUserVar('submissionId'));
        $templateMgr->assign('pprActionType', $pprActionType);

        $template = $pprPlugin->getTemplateResource('ppr/submission/submissionActionsConfirmationForm.tpl');
        return new JSONMessage(true, $templateMgr->fetch($template));
    }

    /**
     * On close submission, it updates the submission status to published
     */
    function close($args, $request) {
        return $this->_executeAction($request, STATUS_PUBLISHED, Core::getCurrentDate());
    }

    /**
     * On open submission, it updates the submission status to queued
     */
    function open($args, $request) {
        // ON OPEN, WE RESET THE completedDate TO NULL
        return $this->_executeAction($request, STATUS_QUEUED, null);
    }

    private function _executeAction($request, $submissionStatus, $updatedStatusDate) {
        $submission = $this->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION);
        if ($submission) {
            $submissionDao = DAORegistry::getDAO('SubmissionDAO');
            $submission->setStatus($submissionStatus);
            $submission->setData('lastModified', Core::getCurrentDate());
            $submission->setData('closedDate', $updatedStatusDate);
            $submissionDao->updateObject($submission);

            $dispatcher = $this->getDispatcher();
            // REDIRECT TO THE SUBMISSION PAGE TO RELOAD NEW STATUS
            $redirectUrl = $dispatcher->url($request, ROUTE_PAGE, null, 'workflow', 'access', array($submission->getId()));
            return $request->redirectUrlJson($redirectUrl);
        }

        return new JSONMessage(false);
    }

}