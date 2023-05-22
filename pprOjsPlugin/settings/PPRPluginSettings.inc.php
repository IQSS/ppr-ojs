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

    public function displayWorkflowMessageEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'displayWorkflowMessageEnabled') ?? true;
    }

    public function displayContributorsEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'displayContributorsEnabled');
    }

    public function displaySuggestedReviewersEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'displaySuggestedReviewersEnabled');
    }

    public function hideReviewMethodEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'hideReviewMethodEnabled');
    }

    public function hideReviewRecommendationEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'hideReviewRecommendationEnabled');
    }

    public function hidePreferredPublicNameEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'hidePreferredPublicNameEnabled');
    }

    public function userCustomFieldsEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'userCustomFieldsEnabled');
    }

}