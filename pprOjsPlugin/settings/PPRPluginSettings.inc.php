<?php

class PPRPluginSettings {

    const CONFIG_VARS = array(
        // PROPERTY NAME => [TYPE, DEFAULT VALUE]
        'displayWorkflowMessageEnabled' => ['bool', true],
        'displayContributorsEnabled' => ['bool', null],
        'displaySuggestedReviewersEnabled' => ['bool', null],
        'hideReviewMethodEnabled' => ['bool', null],
        'hideReviewRecommendationEnabled' => ['bool', null],
        'hidePreferredPublicNameEnabled' => ['bool', null],
        'userCustomFieldsEnabled' => ['bool', null],
        'categoryOptions' => ['string', 'Faculty, Fellow (Post-Doc), Grad Student, Staff, Student'],
    );

    /** @var $contextId int */
    private $contextId;

    /** @var $pprPlugin object */
    private $pprPlugin;

    public function __construct($contextId, $pprPlugin) {
        $this->contextId = $contextId;
        $this->pprPlugin = $pprPlugin;
    }

    public function displayWorkflowMessageEnabled() {
        return $this->pprPlugin->getSetting($this->contextId, 'displayWorkflowMessageEnabled') ?? self::CONFIG_VARS['displayWorkflowMessageEnabled'][1];
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

    public function getCategoryOptions() {
        $categoriesString =  $this->pprPlugin->getSetting($this->contextId, 'categoryOptions') ?? self::CONFIG_VARS['categoryOptions'][1];
        $categoryOptions = array_map('trim', explode(',', $categoriesString));
        return array_combine($categoryOptions, $categoryOptions);
    }

}