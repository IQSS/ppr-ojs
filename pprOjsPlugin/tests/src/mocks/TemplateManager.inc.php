<?php

class TemplateManager {

    private static $instance;
    private $data;

    public static function getManager($request = null) {
        if (self::$instance === null) {
            self::$instance = new TemplateManager();
            self::$instance->data = [];
        }

        return self::$instance;
    }

    public function getTemplateVars($varName) {
        return $this->data[$varName] ?? null;
    }

    public function assign($variables) {
        $this->data = array_merge($this->data, $variables);
    }

    public function setData($data) {
        $this->data = $data;
    }
}