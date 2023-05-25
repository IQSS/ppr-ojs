<?php

/**
 * @file SuggestedReviewersDAO.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class SuggestedReviewersDAO
 * @ingroup journal
 * @see Publication
 *
 * @brief Operations for retrieving and modifying Suggested Reviewers objects.
 */

import ('classes.article.AuthorDAO');

class PPRAuthorDAO extends AuthorDAO {

    /**
     * Add category and department to the author object when the user data is copied over while creating a submission
     */
    public function insertObject($object) {
        $request = Application::get()->getRequest();
        if (isset($request->_router->_page) && $request->_router->_page === 'submission') {
            $user = $request->getUser();
            $object->setData('category', $user->getData('category'));
            $object->setData('department', $user->getData('department'));
        }


        return parent::insertObject($object);
    }
}