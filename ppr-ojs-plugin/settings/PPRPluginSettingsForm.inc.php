<?php

import('lib.pkp.classes.form.Form');

class PPRPluginSettingsForm extends Form {

	const CONFIG_VARS = array(
        // PROPERTY NAME => [TYPE, DEFAULT VALUE]
        'displayWorkflowMessageEnabled' => ['bool', true],
        'displayContributorsEnabled' => ['bool', null],
		'displaySuggestedReviewersEnabled' => ['bool', null],
		'hideReviewMethodEnabled' => ['bool', null],
		'hideReviewRecommendationEnabled' => ['bool', null],
		'hidePreferredPublicNameEnabled' => ['bool', null],
	);

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
		parent::__construct($plugin->getTemplateResource('ppr/pluginSettingsForm.tpl'));
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	/**
	 * Initialize form data.
	 */
	function initData() {
		$contextId = $this->contextId;
		$plugin =& $this->plugin;
		$this->_data = array();
        foreach (self::CONFIG_VARS as $configVar => $varSettings) {
            $this->_data[$configVar] = $plugin->getSetting($contextId, $configVar) ?? $varSettings[1];
        }
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array_keys(self::CONFIG_VARS));
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
		foreach (self::CONFIG_VARS as $configVar => $varSettings) {
            $plugin->updateSetting($contextId, $configVar, $this->getData($configVar), $varSettings[0]);
		}

        // ENSURE NEW/DEFAULT TEMPLATES ARE LOADED CORRECTLY AFTER CHANGES IN SETTINGS
        $plugin->clearCache();
		parent::execute(...$functionArgs);
	}
}

