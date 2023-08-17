<?php

/**
 * Service update the email variables for the add reviewer email
 */
class PPRReviewerFormEmailService {

    const OJS_ADD_REVIEWER_EMAIL_TEMPLATES = ['REVIEW_REQUEST', 'REVIEW_REQUEST_ONECLICK', 'REVIEW_REQUEST_SUBSEQUENT', 'REVIEW_REQUEST_ONECLICK_SUBSEQUENT'];

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->addReviewerEmailServiceEnabled()) {
            HookRegistry::register('advancedsearchreviewerform::display', array($this, 'reviewerFormDisplay'));
            HookRegistry::register('Mail::send', array($this, 'assignReviewerEmailSend'));
        }
    }

    function reviewerFormDisplay($hookName, $arguments) {
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
        $emailVariables = $templateMgr->getTemplateVars('emailVariables');
        $emailVariables['firstNameOnly'] = __('review.ppr.reviewer.firstName.label');
        $templateMgr->assign('emailVariables', $emailVariables);

        return false;
    }

    function assignReviewerEmailSend($hookName, $arguments) {
        $emailTemplate = $arguments[0];
        if ($emailTemplate instanceof SubmissionMailTemplate && in_array($emailTemplate->emailKey,self::OJS_ADD_REVIEWER_EMAIL_TEMPLATES)) {
            $reviewerUsername = $emailTemplate->params['reviewerUserName'];
            $userDao = DAORegistry::getDAO('UserDAO');
            $reviewer = $userDao->getByUsername($reviewerUsername);
            if ($reviewer) {
                $newBody = str_replace('{$firstNameOnly}', $reviewer->getLocalizedGivenName(), $emailTemplate->getBody());
                $emailTemplate->setBody($newBody);
            }
        }

        return false;
    }
}