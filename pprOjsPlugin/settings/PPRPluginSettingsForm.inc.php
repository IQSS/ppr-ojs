<?php

import('lib.pkp.classes.form.Form');

class PPRPluginSettingsForm extends Form {

    /** @var $contextId int */
    private $contextId;

    /** @var $plugin object */
    private $plugin;

    /**
     * Constructor
     * @param $plugin object
     * @param $contextId int
     */
    function __construct($plugin, $contextId) {
        $this->contextId = $contextId;
        $this->plugin = $plugin;
        //IMPORT SETTINGS TO USE CONFIG VARIABLES
        $this->plugin->import('settings.PPRPluginSettings');
        $this->plugin->import('settings.PPRReviewReminderDaysValidator');
        $this->plugin->import('settings.PPRAccessKeyLifeTimeValidator');

        parent::__construct($plugin->getTemplateResource('ppr/pluginSettingsForm.tpl'));
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
        $this->addCheck(new PPRReviewReminderDaysValidator($this));
        $this->addCheck(new PPRAccessKeyLifeTimeValidator($this));
    }

    /**
     * Initialize form data.
     */
    function initData() {
        $contextId = $this->contextId;
        $plugin =& $this->plugin;
        $this->_data = array();
        foreach (PPRPluginSettings::CONFIG_VARS as $configVar => $varSettings) {
            $this->_data[$configVar] = $plugin->getSetting($contextId, $configVar) ?? $varSettings[1];
        }
    }

    /**
     * Assign form data to user-submitted data.
     */
    function readInputData() {
        $this->readUserVars(array_keys(PPRPluginSettings::CONFIG_VARS));
    }

    /**
     * Fetch the form.
     * @copydoc Form::fetch()
     */
    function fetch($request, $template = null, $display = false) {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->plugin->getName());
        $templateMgr->assign('applicationName', Application::get()->getName());
        return parent::fetch($request, $template, $display);
    }

    /**
     * @copydoc Form::execute()
     */
    function execute(...$functionArgs) {
        $plugin =& $this->plugin;
        $contextId = $this->contextId;
        foreach (PPRPluginSettings::CONFIG_VARS as $configVar => $varSettings) {
            $plugin->updateSetting($contextId, $configVar, $this->getData($configVar), $varSettings[0]);
        }

        // ENSURE NEW/DEFAULT TEMPLATES ARE LOADED CORRECTLY AFTER CHANGES IN SETTINGS
        $plugin->clearCache();

        // RESET SCHEDULED TASKS => THIS IS USEFUL FOR TESTING AND TRIGGERING WHEN NECESSARY
        $reviewReminderEditorReset = Application::get()->getRequest()->getUserVar('scheduledTasksReset');
        if($reviewReminderEditorReset) {
            $taskDao = DAORegistry::getDAO('ScheduledTaskDAO');
            $taskDao->updateLastRunTime('plugins.generic.pprOjsPlugin.tasks.PPRReviewDueDateEditorNotification', strtotime('2000-01-01'));
            $taskDao->updateLastRunTime('plugins.generic.pprOjsPlugin.tasks.PPRReviewReminder', strtotime('2000-01-01'));
            $taskDao->updateLastRunTime('plugins.generic.pprOjsPlugin.tasks.PPRReviewSentAuthorNotification', strtotime('2000-01-01'));
            $taskDao->updateLastRunTime('plugins.generic.pprOjsPlugin.tasks.PPRSubmissionClosedAuthorNotification', strtotime('2000-01-01'));
        }

        parent::execute(...$functionArgs);
    }
}

