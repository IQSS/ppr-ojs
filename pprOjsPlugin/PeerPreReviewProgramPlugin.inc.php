<?php

import('lib.pkp.classes.plugins.GenericPlugin');

class PeerPreReviewProgramPlugin extends GenericPlugin {

    private $pprPluginSettings;
    private $pprPluginSettingsHandler;

    /**
     * @copydoc Plugin::register()
     */
    function register($category, $path, $mainContextId = null) {
        $success = parent::register($category, $path, $mainContextId);
        $currentContextId = ($mainContextId === null) ? $this->getCurrentContextId() : $mainContextId;
        $this->import('settings.PPRPluginSettings');
        $this->pprPluginSettings = new PPRPluginSettings($currentContextId, $this);
        $this->import('settings.PPRPluginSettingsHandler');
        $this->pprPluginSettingsHandler = new PPRPluginSettingsHandler($this);

        if ($success && $this->getEnabled($currentContextId)) {
            $this->AddSettingsToTemplateManager();
            $this->setupCustomCss();

            $this->import('services.PPRTemplateOverrideService');
            $templateOverrideService = new PPRTemplateOverrideService($this);
            $templateOverrideService->register();

            $this->import('services.PPRWorkflowService');
            $workflowService = new PPRWorkflowService($this);
            $workflowService->register();

            $this->import('services.PPRUserCustomFieldsService');
            $userCustomFieldsService = new PPRUserCustomFieldsService($this);
            $userCustomFieldsService->register();

            $this->import('services.PPROnLeaveCustomFieldsService');
            $onLeaveCustomFieldsService = new PPROnLeaveCustomFieldsService($this);
            $onLeaveCustomFieldsService->register();

            $this->import('services.submission.PPRSubmissionCommentsForReviewerService');
            $submissionCommentsForReviewerService = new PPRSubmissionCommentsForReviewerService($this);
            $submissionCommentsForReviewerService->register();

            $this->import('services.submission.PPRSubmissionResearchTypeService');
            $submissionResearchTypeService = new PPRSubmissionResearchTypeService($this);
            $submissionResearchTypeService->register();

            $this->import('services.submission.PPRSubmissionActionsService');
            $submissionActionsService = new PPRSubmissionActionsService($this);
            $submissionActionsService->register();

            $this->import('services.submission.PPRAuthorSubmissionSurveyService');
            $authorSubmissionService = new PPRAuthorSubmissionSurveyService($this);
            $authorSubmissionService->register();

            $this->import('services.publication.PPRPublicationOverrideService');
            $publicationOverrideService = new PPRPublicationOverrideService($this);
            $publicationOverrideService->register();

            $this->import('services.reviewer.PPRReviewerGridService');
            $reviewerGridService = new PPRReviewerGridService($this);
            $reviewerGridService->register();

            $this->import('services.reviewer.PPRReviewAcceptedService');
            $reviewAcceptedService = new PPRReviewAcceptedService($this);
            $reviewAcceptedService->register();

            $this->import('services.reviewer.PPRReviewSubmittedService');
            $reviewSubmittedService = new PPRReviewSubmittedService($this);
            $reviewSubmittedService->register();

            $this->import('services.reviewer.PPRReviewAttachmentsService');
            $reviewAttachmentsService = new PPRReviewAttachmentsService($this);
            $reviewAttachmentsService->register();

            $this->import('services.email.PPRDisableEmailService');
            $disableEmailService = new PPRDisableEmailService($this);
            $disableEmailService->register();

            $this->import('services.email.PPRReviewReminderEmailService');
            $reviewReminderService = new PPRReviewReminderEmailService($this);
            $reviewReminderService->register();

            $this->import('services.email.PPREditorialDecisionsEmailService');
            $editorialDecisionsEmailService = new PPREditorialDecisionsEmailService($this);
            $editorialDecisionsEmailService->register();

            $this->import('services.email.PPRReviewAddEditorEmailService');
            $addEditorEmailService = new PPRReviewAddEditorEmailService($this);
            $addEditorEmailService->register();

            $this->import('services.email.PPRReviewerFormEmailService');
            $reviewerFormEmailService = new PPRReviewerFormEmailService($this);
            $reviewerFormEmailService->register();

            // THIS HOOK WILL ONLY BE CALLED WHEN THE acron PLUGIN IS RELOADED
            HookRegistry::register('AcronPlugin::parseCronTab', array($this, 'addScheduledTasks'));
        }

        return $success;
    }

    function getActions($request, $actionArgs) {
        return $this->pprPluginSettingsHandler->getActions($request, $actionArgs);
    }

    function manage($args, $request) {
        return $this->pprPluginSettingsHandler->manage($args, $request);
    }

    /**
     * @copydoc LazyLoadPlugin::setEnabled()
     */
    function setEnabled($enabled) {
        parent::setEnabled($enabled);
        $this->clearCache();
    }

    /**
     * Clear template/css caches to refresh data when enabling/disabling the plugin and updating its settings
     */
    function clearCache() {
        // CLEAR THE TEMPLATE CACHE TO RELOAD DEFAULT TEMPLATES
        // THIS IS AN ISSUE WITH TEMPLATE OVERRIDE IN PLUGINS
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
        $templateMgr->clearTemplateCache();
        $templateMgr->clearCssCache();

        $cacheMgr = CacheManager::getManager();
        $cacheMgr->flush();
    }

    /**
     * Add the pprPluginSettings to the template manager to have conditional logic in templates
     */
    function AddSettingsToTemplateManager() {
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
        $templateMgr->assign(['pprPluginSettings' => $this->getPluginSettings()]);
    }

    /**
     * Load custom CSS file into all backend pages.
     *
     * @return void
     */
    function setupCustomCss() {
        $request = Application::get()->getRequest();
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->addStyleSheet(
            'pprOjsPluginCustomCss',
            $request->getBaseUrl() . '/' . $this->getPluginPath() . '/css/iqss.css?pprv='.$this->getPluginVersion(),
            ['contexts' => array('frontend', 'backend')]
        );
    }

    /**
     * Added scheduled tasks to the acron plugin
     *
     * @return void
     */
    function addScheduledTasks($hookName, $args) {
        $taskFilesPath =& $args[0];
        $taskFilesPath[] = $this->getPluginPath() . DIRECTORY_SEPARATOR . 'scheduledTasks.xml';
        return false;
    }

    /**
     * @copydoc Plugin::getDisplayName
     */
    function getDisplayName() {
        return __("plugins.generic.pprPlugin.displayName");
    }

    /**
     * @copydoc Plugin::getDescription
     */
    function getDescription() {
        return __("plugins.generic.pprPlugin.description");
    }

    /**
     * Get the plugin version to add to the CSS file. To break client caching
     */
    function getPluginVersion() {
        return $this->getCurrentVersion()->getVersionString();
    }

    public function getPluginSettings() {
        return $this->pprPluginSettings;
    }

}

