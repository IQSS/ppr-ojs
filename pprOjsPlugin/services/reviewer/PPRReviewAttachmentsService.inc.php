<?php

/**
 * Service to customize the send review files attachment component
 *
 * In order to achieve the new requirements, we need to override the ReviewAttachmentGridHandler
 * to have control of the ReviewAttachmentGridCellProvider
 *
 * Issue 109
 */
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