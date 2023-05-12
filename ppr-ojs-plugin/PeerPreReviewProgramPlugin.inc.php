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

        if ($success && $this->getEnabled()) {
            HookRegistry::register('TemplateResource::getFilename', array($this, '_overridePluginTemplates'));
            $this->setupCustomCss();

            $this->import('services.PPRWorkflowService');
            $workflowService = new PPRWorkflowService($this);
            $workflowService->register();
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
		return __("plugins.generic.pprPlugin.displayName");
	}

	/**
	 * @copydoc Plugin::getDescription
	 */
	function getDescription() {
        return __("plugins.generic.pprPlugin.description");
	}

    public function getPluginSettings() {
        return $this->pprPluginSettings;
    }


}

