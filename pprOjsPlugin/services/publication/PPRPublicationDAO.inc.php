<?php

import ('classes.publication.PublicationDAO');
require_once(dirname(__FILE__) . '/PPRPublication.inc.php');

/**
 * Override PublicationDAO to use the PPRPublication object.
 */
class PPRPublicationDAO extends PublicationDAO {

    public function newDataObject() {
        return new PPRPublication();
    }
}