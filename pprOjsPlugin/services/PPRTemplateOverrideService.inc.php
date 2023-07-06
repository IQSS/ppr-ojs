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
            $this->overriddenTemplates[] = 'lib/pkp/templates/frontend/pages/userRegister.tpl';
            $this->overriddenTemplates[] = 'lib/pkp/templates/user/contactForm.tpl';
            $this->overriddenTemplates[] = 'lib/pkp/templates/user/identityForm.tpl';
        }

        if ($this->pprPlugin->getPluginSettings()->userOnLeaveEnabled()) {
            $this->overriddenTemplates[] = 'lib/pkp/templates/common/userDetails.tpl';
            $this->overriddenTemplates[] = 'lib/pkp/templates/user/identityForm.tpl';
        }

        if ($this->pprPlugin->getPluginSettings()->submissionCustomFieldsEnabled()) {
            $this->overriddenTemplates[] = 'lib/pkp/templates/submission/submissionMetadataFormTitleFields.tpl';
            $this->overriddenTemplates[] = 'templates/reviewer/review/step3.tpl';
        }

        if ($this->pprPlugin->getPluginSettings()->submissionCloseEnabled()) {
            $this->overriddenTemplates[] = 'lib/pkp/templates/workflow/editorialLinkActions.tpl';
        }

        if ($this->pprPlugin->getPluginSettings()->submissionConfirmationChecklistEnabled()) {
            $this->overriddenTemplates[] = 'lib/pkp/templates/submission/form/step4.tpl';
        }

        if ($this->pprPlugin->getPluginSettings()->submissionUploadFileValidationEnabled()) {
            $this->overriddenTemplates[] = 'lib/pkp/templates/submission/form/step2.tpl';
            $this->overriddenTemplates[] = 'lib/pkp/templates/ppr/modalMessage.tpl';
        }

        if ($this->pprPlugin->getPluginSettings()->submissionRequestRevisionsFileValidationEnabled()) {
            $this->overriddenTemplates[] = 'lib/pkp/templates/controllers/modals/editorDecision/form/sendReviewsForm.tpl';
        }
    }

    function register() {
        HookRegistry::register('TemplateResource::getFilename', array($this, 'overrideTemplate'));
    }

    function overrideTemplate($hookName, $args) {
        $templateFilePath = &$args[0];
        $origin_template_suffix = '.load_ojs';

        if (str_ends_with($templateFilePath, $origin_template_suffix)) {
            // SPECIAL TEMPLATE EXTENSION => REMOVE EXTENSION AND DO NOT OVERRIDE
            $templateFilePath = substr($templateFilePath, 0, -strlen($origin_template_suffix));
            return false;
        }

        if (in_array($templateFilePath, $this->overriddenTemplates)) {
            return $this->pprPlugin->_overridePluginTemplates($hookName, $args);
        }

        // CURRENT TEMPLATE NOT IN OUR LIST => IGNORE OVERRIDES
        return false;
    }

}