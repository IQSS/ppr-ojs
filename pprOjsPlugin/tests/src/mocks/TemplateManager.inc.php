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

    public function assign($variable, $value = null, $nocache = false) {
        if (is_array($variable)) {
            foreach ($variable as $_key => $_val) {
                $this->assign($_key, $_val, $nocache);
            }
        } else {
            $this->data[ $variable ] = $value;
        }
    }

    public function setData($data) {
        $this->data = $data;
    }
}