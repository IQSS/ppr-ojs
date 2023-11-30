<?php

/**
 * Service to show a submission completed survey to an author only once.
 * This survey is shown in the complete page in the author submission workflow.
 * See SubmissionHandler->step
 */
class PPRAuthorSubmissionSurveyService {

    private $pprPlugin;

    private $pprObjectFactory;

    public function __construct($plugin, $pprObjectFactory = null) {
        $this->pprPlugin = $plugin;
        $this->pprPlugin->import('util.PPRObjectFactory');
        $this->pprObjectFactory = $pprObjectFactory ?: new PPRObjectFactory();
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->authorSubmissionSurveyHtml()) {
            HookRegistry::register('TemplateManager::fetch', array($this, 'addAuthorSurvey'));
        }

    }

    /**
     * This is the only way to intercept the response for the author submission  confirmation page.
     * We need to intercept the rendering of the confirmation template and add our logic to check the author survey.
     */
    function addAuthorSurvey($hookName, $args) {
        $templateName = $args[1];

        if ($templateName === 'submission/form/complete.tpl') {
            $request = Application::get()->getRequest();
            $context = $request->getContext();
            $notificationRegistry = $this->pprObjectFactory->pprTaskNotificationRegistry($context->getId());

            $user = $request->getUser();
            $authorSurveys = $notificationRegistry->getSubmissionSurveyForAuthor($user->getId());
            if (empty($authorSurveys)) {
                //FLAG USER SO THAT THE SURVEY WILL NOT BE SHOWN AGAIN
                $notificationRegistry->registerSubmissionSurveyForAuthor($user->getId());
            }

            // ONLY SHOW SURVEY WHEN $authorSurveys IS EMPTY
            $templateVars = ['showPPRAuthorSurvey' => empty($authorSurveys)];
            $templateMgr = $args[0];
            $templateMgr->assign($templateVars);

        }

        return false;
    }

}