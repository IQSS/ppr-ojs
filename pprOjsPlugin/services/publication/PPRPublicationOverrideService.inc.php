<?php

/**
 * Service to override the Publication object and PublicationDAO
 */
class PPRPublicationOverrideService {

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->publicationOverrideEnabled()) {
            $this->pprPlugin->import('services.publication.PPRPublicationDAO');
            $pprPublicationDAO = new PPRPublicationDAO();
            DAORegistry::registerDAO('PublicationDAO', $pprPublicationDAO);
        }
    }
}