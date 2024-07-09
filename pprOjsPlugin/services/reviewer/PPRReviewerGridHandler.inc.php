<?php

import('controllers.grid.users.reviewer.ReviewerGridHandler');
require_once(dirname(__FILE__) . '/PPRUnassignReviewerForm.inc.php');
require_once(dirname(__FILE__) . '/PPRReviewerGridCellProvider.inc.php');

/**
 * Custom ReviewerGridHandler to control the form used when unassigning reviewers
 * and the text for the "considered" column in the reviewers list panel.
 *
 * Issue 092
 */
class PPRReviewerGridHandler extends ReviewerGridHandler {

    /**
     * We need to override the initialize method to override the cell provider for the considered column
     * The considered column renders the status of the review assigment in the reviewers list panel.
     */
    function initialize($request, $args = null) {
        parent::initialize($request, $args);
        // THIS IS A COPY OF THE addColumn METHOD USED IN PKPReviewerGridHandler.initialize
        // USED FOR THE 'considered' COLUMN => WE JUST OVERRIDE THE cellProvider
        $cellProvider = new PPRReviewerGridCellProvider($this->_isCurrentUserAssignedAuthor);
        $this->addColumn(
            new GridColumn(
                'considered',
                'common.status',
                null,
                null,
                $cellProvider,
                array('anyhtml' => true)
            )
        );
    }

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