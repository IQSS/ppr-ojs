<?php

/**
 * Service to disable email notifications
 *
 * Issue 068, Issue 078
 */
class PPRDisableEmailService {

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->reviewerRegistrationEmailDisabled()) {
            HookRegistry::register('Mail::send', array($this, 'disableReviewerRegistrationEmail'));
        }

        if ($this->pprPlugin->getPluginSettings()->submissionConfirmationContributorsEmailDisabled()) {
            HookRegistry::register('Mail::send', array($this, 'disableSubmissionConfirmationContributorsEmail'));
        }
    }

    function disableReviewerRegistrationEmail($hookName, $hookArgs) {
        $emailTemplate =& $hookArgs[0];
        if (isset($emailTemplate->emailKey) && $emailTemplate->emailKey === 'REVIEWER_REGISTER') {
            $request = Application::get()->getRequest();
            $this->log($request, 'disableReviewerRegistrationEmail');
            return true;
        }
        return false;
    }

    function disableSubmissionConfirmationContributorsEmail($hookName, $hookArgs) {
        $emailTemplate =& $hookArgs[0];
        if (isset($emailTemplate->emailKey) && $emailTemplate->emailKey === 'SUBMISSION_ACK_NOT_USER') {
            $request = Application::get()->getRequest();
            $this->log($request, 'disableSubmissionConfirmationContributorsEmail');
            return true;
        }
        return false;
    }

    private function log($request, $action) {
        $router = $request->getRouter();
        $page = method_exists($router, 'getRequestedPage') ? $router->getRequestedPage($request) : '';
        $component = method_exists($router, 'getRequestedComponent') ? $router->getRequestedComponent($request) : '';
        $operation = method_exists($router, 'getRequestedOp') ? $router->getRequestedOp($request) : '';
        error_log("PPR[PPRDisableEmailService] action=$action page=$page component=$component operation=$operation result=success");
    }
}