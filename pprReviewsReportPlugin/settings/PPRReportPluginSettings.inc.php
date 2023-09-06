<?php

class PPRReportPluginSettings {

    const CONFIG_VARS = array(
        // PROPERTY NAME => [TYPE, DEFAULT VALUE]
        'submissionsReviewsReportEnabled' => ['bool', null],
        'submissionsReviewsReportRecipients' => ['string', null],
    );

    private $pprPlugin;
    private $contextId;

    public function __construct($contextId, $pprPlugin) {
        $this->contextId = $contextId;
        $this->pprPlugin = $pprPlugin;
    }

    public function getContextId() {
        return $this->contextId;
    }

    public function submissionsReviewsReportEnabled() {
        return $this->getValue('submissionsReviewsReportEnabled');
    }

    public function submissionsReviewsReportRecipients() {
        $recipientsString =  $this->getValue('submissionsReviewsReportRecipients');
        return array_values(array_filter(array_map('trim', explode(',', $recipientsString))));
    }

    private function getValue($propertyName) {
        return $this->pprPlugin->getSetting($this->contextId, $propertyName) ?? self::CONFIG_VARS[$propertyName][1];
    }
}