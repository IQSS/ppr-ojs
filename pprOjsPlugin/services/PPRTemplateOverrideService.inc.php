<?php

class PPRTemplateOverrideService {

    private $pprPlugin;

    private $overriddenTemplates;
    public function __construct($plugin) {
        $this->pprPlugin = $plugin;

        $this->overriddenTemplates = [];

        if ($this->pprPlugin->getPluginSettings()->displayWorkflowMessageEnabled()) {
            $this->overriddenTemplates[] = 'lib/pkp/templates/ppr/workflowInvalidTabMessage.tpl';
            $this->overriddenTemplates[] = 'lib/pkp/templates/controllers/tab/authorDashboard/editorial.tpl';
            $this->overriddenTemplates[] = 'templates/controllers/tab/authorDashboard/production.tpl';
            $this->overriddenTemplates[] = 'lib/pkp/templates/controllers/tab/workflow/editorial.tpl';
            $this->overriddenTemplates[] = 'templates/controllers/tab/workflow/production.tpl';
        }

        if ($this->pprPlugin->getPluginSettings()->hideReviewMethodEnabled()) {
            $this->overriddenTemplates[] = 'lib/pkp/templates/controllers/grid/users/reviewer/form/reviewerFormFooter.tpl';
        }

        if ($this->pprPlugin->getPluginSettings()->hideReviewRecommendationEnabled()) {
            $this->overriddenTemplates[] = 'templates/reviewer/review/step3.tpl';
            $this->overriddenTemplates[] = 'templates/controllers/grid/users/reviewer/readReview.tpl';
        }

        if ($this->pprPlugin->getPluginSettings()->hidePreferredPublicNameEnabled()) {
            $this->overriddenTemplates[] = 'lib/pkp/templates/common/userDetails.tpl';
        }

        if ($this->pprPlugin->getPluginSettings()->userCustomFieldsEnabled()) {
            $this->overriddenTemplates[] = 'lib/pkp/templates/controllers/grid/users/reviewer/form/createReviewerForm.tpl';
            $this->overriddenTemplates[] = 'lib/pkp/templates/common/userDetails.tpl';
            $this->overriddenTemplates[] = 'lib/pkp/templates/frontend/components/registrationForm.tpl';
            $this->overriddenTemplates[] = 'lib/pkp/templates/user/contactForm.tpl';
        }
    }

    function register() {
        HookRegistry::register('TemplateResource::getFilename', array($this, 'overrideTemplate'));
    }

    function overrideTemplate($hookName, $args) {
        $templateFilePath = $args[0];

        if (in_array($templateFilePath, $this->overriddenTemplates)) {
            return $this->pprPlugin->_overridePluginTemplates($hookName, $args);
        }

        // CURRENT TEMPLATE NOT IN OUR LIST => IGNORE OVERRIDES
        return false;
    }

}