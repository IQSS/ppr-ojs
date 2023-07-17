<?php

/**
 * Service manage reviewer email notifications
 */
class PPRReviewerRegistrationEmailService {

    const REVIEWER_HANDLER_COMPONENT = 'grid.users.reviewer.ReviewerGridHandler';
    const CREATE_REVIEWER_OPERATION = 'createReviewer';

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->reviewerRegistrationEmailDisabled()) {
            HookRegistry::register('Mail::send', array($this, 'stopReviewerRegistrationEmail'));
        }
    }

    function stopReviewerRegistrationEmail($hookName, $hookArgs) {
        $emailTemplate =& $hookArgs[0];
        if (isset($emailTemplate->emailKey) && $emailTemplate->emailKey === 'REVIEWER_REGISTER') {
            $request = Application::get()->getRequest();
            $router = $request->getRouter();
            $componentId = $router->getRequestedComponent($request);
            $operation = $router->getRequestedOp($request);
            if ($componentId === self::REVIEWER_HANDLER_COMPONENT && $operation === self::CREATE_REVIEWER_OPERATION) {
                // STOP THE REGISTRATION EMAIL FOR CREATE REVIEWER OPERATION
                error_log('PPR[PPRReviewerRegistrationEmailService] action=stopReviewerRegistrationEmail result=success');
                return true;
            }
        }
        return false;
    }
}