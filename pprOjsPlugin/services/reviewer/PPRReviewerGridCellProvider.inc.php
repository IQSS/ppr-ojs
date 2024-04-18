<?php

import('lib.pkp.controllers.grid.users.reviewer.ReviewerGridCellProvider');

/**
 * Overrides the default reviewers list panel cell provider: ReviewerGridCellProvider
 * We want to override the data displayed for review declined.
 */
class PPRReviewerGridCellProvider extends ReviewerGridCellProvider {

    /**
     * Used by the parent class to display the data displayed for the second column ("considered") in the  reviewers list panel.
     * This is a copy of the parent class method with the same name: _getStatusText.
     * We just override the part for the REVIEW_ASSIGNMENT_STATUS_DECLINED state.
     */
    function _getStatusText($state, $row) {
        $reviewAssignment = $row->getData();
        switch ($state) {
            case REVIEW_ASSIGNMENT_STATUS_AWAITING_RESPONSE:
                return '<span class="state">'.__('editor.review.requestSent').'</span><span class="details">'.__('editor.review.responseDue', array('date' => substr($reviewAssignment->getDateResponseDue(),0,10))).'</span>';
            case REVIEW_ASSIGNMENT_STATUS_ACCEPTED:
                return '<span class="state">'.__('editor.review.requestAccepted').'</span><span class="details">'.__('editor.review.reviewDue', array('date' => substr($reviewAssignment->getDateDue(),0,10))).'</span>';
            case REVIEW_ASSIGNMENT_STATUS_COMPLETE:
                return $this->_getStatusWithRecommendation('common.complete', $reviewAssignment);
            case REVIEW_ASSIGNMENT_STATUS_REVIEW_OVERDUE:
                return '<span class="state overdue">'.__('common.overdue').'</span><span class="details">'.__('editor.review.reviewDue', array('date' => substr($reviewAssignment->getDateDue(),0,10))).'</span>';
            case REVIEW_ASSIGNMENT_STATUS_RESPONSE_OVERDUE:
                return '<span class="state overdue">'.__('common.overdue').'</span><span class="details">'.__('editor.review.responseDue', array('date' => substr($reviewAssignment->getDateResponseDue(),0,10))).'</span>';
            case REVIEW_ASSIGNMENT_STATUS_DECLINED:
                // PPR OVERRIDE
                return $this->_getReviewDeclinedText($reviewAssignment);
                // PPR OVERRIDE
            case REVIEW_ASSIGNMENT_STATUS_CANCELLED:
                return '<span class="state declined" title="' . __('editor.review.requestCancelled.tooltip') . '">'.__('editor.review.requestCancelled').'</span>';
            case REVIEW_ASSIGNMENT_STATUS_RECEIVED:
                return  $this->_getStatusWithRecommendation('editor.review.reviewSubmitted', $reviewAssignment);
            case REVIEW_ASSIGNMENT_STATUS_THANKED:
                return  $this->_getStatusWithRecommendation('editor.review.reviewerThanked', $reviewAssignment);
            default:
                return '';
        }
    }

    /**
     * Method to get the custom status text for declined reviews including the DateConfirmed
     * DateConfirmed is the date that the review was declined.
     */
    function _getReviewDeclinedText($reviewAssignment) {
        $date = $reviewAssignment->getDateConfirmed() ? substr($reviewAssignment->getDateConfirmed(),0,10) : __('review.ppr.date.missing');
        return '<span class="state declined" title="' . __('editor.review.requestDeclined.tooltip') . '">'.__('editor.review.requestDeclined').'</span>'.
               '<span class="details">'.__('review.ppr.declined.date', array('date' => $date)).'</span>';
    }

}