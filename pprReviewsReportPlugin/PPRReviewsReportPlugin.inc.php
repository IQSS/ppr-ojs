<?php

import('lib.pkp.classes.plugins.ReportPlugin');

/**
 * PPR Submission and Review report plugin
 */
class PPRReviewsReportPlugin extends ReportPlugin {

    private $pprPluginSettings;
    private $pprPluginSettingsHandler;

    /**
     * @copydoc Plugin::register()
     */
    function register($category, $path, $mainContextId = null) {
        $success = parent::register($category, $path, $mainContextId);
        $currentContextId = ($mainContextId === null) ? $this->getCurrentContextId() : $mainContextId;

        $this->pprPluginSettings = $this->createPluginSettings($currentContextId);
        $this->import('settings.PPRReportPluginSettingsHandler');
        $this->pprPluginSettingsHandler = new PPRReportPluginSettingsHandler($this);

        $this->addLocaleData();

        if ($success) {
            // THIS HOOK WILL ONLY BE CALLED WHEN THE acron PLUGIN IS RELOADED
            HookRegistry::register('AcronPlugin::parseCronTab', array($this, 'addScheduledTasks'));
        }

        return $success;
    }

    function getActions($request, $actionArgs) {
        return array_merge($this->pprPluginSettingsHandler->getActions($request, $actionArgs), parent::getActions($request, $actionArgs));
    }

    function manage($args, $request) {
        return $this->pprPluginSettingsHandler->manage($args, $request);
    }

    /**
     * Get the name of this plugin. The name must be unique within
     * its category.
     * @return String name of plugin
     */
    function getName() {
        return 'PPRReviewsReportPlugin';
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    function getDisplayName() {
        return __('plugins.report.pprReviewsPlugin.displayName');
    }

    /**
     * @copydoc Plugin::getDescriptionName()
     */
    function getDescription() {
        return __('plugins.report.pprReviewsPlugin.description');
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
     * @copydoc ReportPlugin::display()
     */
    function display($args, $request) {
        $this->import('reports.PPRSubmissionsReviewsReport');
        $report = new PPRSubmissionsReviewsReport();
        $context = $request->getContext();
        $acronym = PKPString::regexp_replace("/[^A-Za-z0-9 ]/", '', $context->getLocalizedAcronym());

        // Prepare for UTF8-encoded CSV output.
        header('content-type: text/comma-separated-values');
        header('content-disposition: attachment; filename=ppr-reviews-' . $acronym . '-' . date('Ymd') . '.csv');

        $report->createReport('php://output', $context->getId());
    }

    public function getPluginSettings() {
        return $this->pprPluginSettings;
    }

    public function createPluginSettings($contextId) {
        $this->import('settings.PPRReportPluginSettings');
        return new PPRReportPluginSettings($contextId, $this);
    }

    public function getPluginSettingsHandler() {
        return $this->pprPluginSettingsHandler;
    }

    function getCurrentContextId() {
        $context = Application::get()->getRequest()->getContext();
        return is_null($context) ? 0 : $context->getId();
    }
}

