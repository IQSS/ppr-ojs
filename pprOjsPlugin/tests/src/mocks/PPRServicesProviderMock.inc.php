<?php

use \Pimple\Container;
class PPRServicesProviderMock implements \Pimple\ServiceProviderInterface {

    private $servicesMap;

    public function __construct($servicesMap) {
        $this->servicesMap = $servicesMap;
    }

    public function register(Container $pimple) {
        foreach ($this->servicesMap as $serviceName => $serviceImplementation) {
            $pimple[$serviceName] = $serviceImplementation;
        }
    }
}