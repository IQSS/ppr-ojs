<?php

class TemplateManager {

    private static $instance;
    private $data;

    public static function getManager($request = null) {
        if (self::$instance === null) {
            self::$instance = new TemplateManager();
        }

        return self::$instance;
    }

    public function getTemplateVars($varName) {
        return $this->data[$varName] ?? null;
    }

    public function setData($data) {
        $this->data = $data;
    }
}