<?php

class PPRPluginMock {

    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function getSetting($contextId, $settingName) {
        return $this->data[$settingName] ?? null;
    }

}