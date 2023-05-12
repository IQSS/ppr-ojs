<?php

class PPRPluginSettings {

    /** @var $contextId int */
    private $contextId;

    /** @var $pprPlugin object */
    private $pprPlugin;

    public function __construct($contextId, $pprPlugin) {
        $this->contextId = $contextId;
        $this->pprPlugin = $pprPlugin;
    }

    public function displayContributorsEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'displayContributorsEnabled');
    }

    public function displaySuggestedReviewersEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'displaySuggestedReviewersEnabled');
    }


}