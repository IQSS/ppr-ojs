<?php

import('lib.pkp.classes.plugins.GenericPlugin');

class PeerPreReviewProgramPlugin extends GenericPlugin {

	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path, $mainContextId = null) {
		$success = parent::register($category, $path, $mainContextId);
		if ($success && $this->getEnabled()) {
			HookRegistry::register('TemplateResource::getFilename', array($this, '_overridePluginTemplates'));
			$this->setupCustomCss();
		}

		return $success;
	}

    /**
     * @copydoc LazyLoadPlugin::setEnabled()
     */
	function setEnabled($enabled) {
		parent::setEnabled($enabled);
		if (!$enabled) {
			// CLEAR THE TEMPLATE CACHE TO RELOAD DEFAULT TEMPLATES
			// THIS IS AN ISSUE WITH TEMPLATE OVERRIDE IN PLUGINS
			$templateMgr = TemplateManager::getManager(Application::get()->getRequest());
			$templateMgr->clearTemplateCache();
			$templateMgr->clearCssCache();

			$cacheMgr = CacheManager::getManager();
			$cacheMgr->flush();
		}
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
			'iqssCustomCss',
			$request->getBaseUrl() . '/' . $this->getPluginPath() . '/css/iqss.css',
			['contexts' => 'backend']
		);
	}

	/**
	 * @copydoc Plugin::getDisplayName
	 */
	function getDisplayName() {
		return "IQSS Peer Pre-Review Program Plugin";
	}

	/**
	 * @copydoc Plugin::getDescription
	 */
	function getDescription() {
		return "Customizations to the OJS registration and submission workflow for use in the Peer Pre-Review Program at Harvard’s IQSS";
	}
}

