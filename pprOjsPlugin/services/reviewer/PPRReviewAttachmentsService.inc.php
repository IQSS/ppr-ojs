<?php

class PPRReviewAttachmentsService {

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->reviewAttachmentsOverrideEnabled()) {
            HookRegistry::register('LoadComponentHandler', array($this, 'addPPRReviewAttachmentsGridHandler'));
        }
    }

    function addPPRReviewAttachmentsGridHandler($hookName, $hookArgs) {
        $component =& $hookArgs[0];
        if ($component === 'grid.files.attachment.EditorSelectableReviewAttachmentsGridHandler') {
            // LOAD THE PPR REVIEW ATTACHMENTS HANDLER FROM THE PLUGIN REPO
            $component =str_replace('/', '.', $this->pprPlugin->getPluginPath()) . '.services.reviewer.PPRReviewAttachmentsGridHandler';
            return true;
        }
        return false;
    }
}