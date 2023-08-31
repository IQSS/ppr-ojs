<?php

import('controllers.grid.users.reviewer.ReviewerGridHandler');
require_once(dirname(__FILE__) . '/PPRUnassignReviewerForm.inc.php');

/**
 * Custom ReviewerGridHandler to control the form used when unassigning reviewers
 */
class PPRReviewerGridHandler extends ReviewerGridHandler {

    /**
     * This method is a copy of the parent method, only overriding the PPRUnassignReviewerForm
     */
    function unassignReviewer($args, $request) {
        $reviewAssignment = $this->getAuthorizedContextObject(ASSOC_TYPE_REVIEW_ASSIGNMENT);
        $reviewRound = $this->getReviewRound();
        $submission = $this->getSubmission();

        $unassignReviewerForm = new PPRUnassignReviewerForm($reviewAssignment, $reviewRound, $submission);
        $unassignReviewerForm->initData();

        return new JSONMessage(true, $unassignReviewerForm->fetch($request));
    }

    /**
     * This method is a copy of the parent method, only overriding the PPRUnassignReviewerForm
     */
    function updateUnassignReviewer($args, $request) {
        $reviewAssignment = $this->getAuthorizedContextObject(ASSOC_TYPE_REVIEW_ASSIGNMENT);
        $reviewRound = $this->getReviewRound();
        $submission = $this->getSubmission();

        $unassignReviewerForm = new PPRUnassignReviewerForm($reviewAssignment, $reviewRound, $submission);
        $unassignReviewerForm->readInputData();

        // Unassign the reviewer and return status message
        if (!$unassignReviewerForm->validate()) {
            return new JSONMessage(false, __('editor.review.errorDeletingReviewer'));
        }

        $unassignReviewerForm->execute();
        return DAO::getDataChangedEvent($reviewAssignment->getId());
    }

}