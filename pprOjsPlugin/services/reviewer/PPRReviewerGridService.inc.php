<?php

/**
 * Service to update the email template used when executing the assign reviewer action
 *
 * In order to achieve the these requirements, we need to override the ReviewerGridHandler
 */
class PPRReviewerGridService {

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->unassignReviewerEmailOverrideEnabled()) {
            HookRegistry::register('LoadComponentHandler', array($this, 'addPPRReviewerGridHandler'));
        }
    }

    function addPPRReviewerGridHandler($hookName, $hookArgs) {
        $component =& $hookArgs[0];
        if ($component === 'grid.users.reviewer.ReviewerGridHandler') {
            // LOAD THE PPR REVIEWER GRID HANDLER FROM THE PLUGIN REPO
            $component =str_replace('/', '.', $this->pprPlugin->getPluginPath()) . '.services.reviewer.PPRReviewerGridHandler';
            return true;
        }

        return false;
    }
}