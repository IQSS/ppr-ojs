<?php

import('settings.PPRPluginSettings');

class PPRPluginMock {

    private $contextId;
    private $data;

    public function __construct($contextId, $data) {
        $this->contextId = $contextId;
        $this->data = $data;
    }

    public function getSetting($contextId, $settingName) {
        return $this->data[$settingName] ?? null;
    }

    public function getPluginSettings() {
        return new PPRPluginSettings($this->contextId, $this);
    }

}