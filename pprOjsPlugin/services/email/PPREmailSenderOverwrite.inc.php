<?php

/**
 * Service to overwrite the senders email
 *
 */
class PPREmailSenderOverwrite {

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {

        $globalEmailSender = $this->pprPlugin->getPluginSettings()->globalEmailSender();

        if (!empty($globalEmailSender)) {
            HookRegistry::register('Mail::send', array($this, 'overwriteSender'));
        }
    }

    /**
     * Overwrite the sender of the email
     */
    function overwriteSender($hookName, $hookArgs) {
        
        $globalEmailSender = $this->pprPlugin->getPluginSettings()->globalEmailSender();

        $mail = $hookArgs[0];
        $mail->setFrom($globalEmailSender, 'Harvard rapidPeer.');
        $mail->setReplyTo($globalEmailSender, 'Harvard rapidPeer.');
        return false; 
    }

}