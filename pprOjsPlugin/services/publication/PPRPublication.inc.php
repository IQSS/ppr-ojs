<?php

import('classes.publication.Publication');

class PPRPublication extends Publication {

    /**
     * Override getShortAuthorString to display the primary contributor name in the submissions list panel and submission detail view
     */
    public function getShortAuthorString() {
        $contributors = $this->getData('authors');

        if (empty($contributors)) {
            return '';
        }

        $author = $this->getPrimaryAuthor();
        if (!isset($author)) {
            $author = $contributors[0];
        }

        $str = $author->getLocalizedFamilyName();
        if (!$str) {
            $str = $author->getLocalizedGivenName();
        }

        return $str;
    }
}