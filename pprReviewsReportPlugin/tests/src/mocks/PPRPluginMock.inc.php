<?php

import('settings.PPRReportPluginSettings');

class PPRPluginMock {

    private $contextId;
    private $data;
    private $defaultValue;

    public function __construct($contextId, $data, $defaultValue = null) {
        $this->contextId = $contextId;
        $this->data = $data;
        $this->defaultValue = $defaultValue;
    }

    public function getSetting($contextId, $settingName) {
        return $this->data[$settingName] ?? $this->defaultValue;
    }

    public function getPluginSettings() {
        return new PPRReportPluginSettings($this->contextId, $this);
    }

    public function getPluginPath() {
        return 'plugins/generic/pprOjsPlugin';
    }

    public function import($className) {
        import($className);
    }

}