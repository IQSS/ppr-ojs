<?php

import('lib.pkp.classes.form.Form');

class PPRPluginSettingsForm extends Form {

	const CONFIG_VARS = array(
		'displayContributorsEnabled' => 'bool',
		'displaySuggestedReviewersEnabled' => 'bool',
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
        foreach (self::CONFIG_VARS as $configVar => $type) {
            $this->_data[$configVar] = $plugin->getSetting($contextId, $configVar);
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
		foreach (self::CONFIG_VARS as $configVar => $type) {
            $plugin->updateSetting($contextId, $configVar, $this->getData($configVar), $type);
		}

		parent::execute(...$functionArgs);
	}
}

