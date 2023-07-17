<?php

/**
 * Helper class with utility methods for the submission review report.
 */
class PPRReportUtil {

    /**
     * Converts a string into a DateTime objct to do time calculations.
     */
    public function stringToDate($dateAsString) {
        return new DateTime($dateAsString);
    }

    /**
     * Removes the time portion of a DateTime string
     */
    public function formatDate($dateAsString) {
        if (!$dateAsString) {
            return '';
        }

        return substr($dateAsString, 0, 11);
    }

    /**
     * Returns the status text based on the submission and review assignment status.
     */
    public function getStatusText($submissionSentToReview, $reviewAssigmentStatus) {
        if ($reviewAssigmentStatus === null && !$submissionSentToReview) {
            return __('plugins.report.pprReviewsPlugin.status.submissionSubmitted');
        }

        if ($reviewAssigmentStatus === null && $submissionSentToReview) {
            return __('plugins.report.pprReviewsPlugin.status.reviewerSelect');
        }

        switch ($reviewAssigmentStatus) {
            case REVIEW_ASSIGNMENT_STATUS_AWAITING_RESPONSE:
                return __('plugins.report.pprReviewsPlugin.status.requestSent');
            case REVIEW_ASSIGNMENT_STATUS_ACCEPTED:
                return __('plugins.report.pprReviewsPlugin.status.requestAccepted');
            case REVIEW_ASSIGNMENT_STATUS_COMPLETE:
                return __('plugins.report.pprReviewsPlugin.status.complete');
            case REVIEW_ASSIGNMENT_STATUS_REVIEW_OVERDUE:
                return __('plugins.report.pprReviewsPlugin.status.reviewOverdue');
            case REVIEW_ASSIGNMENT_STATUS_RESPONSE_OVERDUE:
                return __('plugins.report.pprReviewsPlugin.status.responseOverdue');
            case REVIEW_ASSIGNMENT_STATUS_DECLINED:
                return __('plugins.report.pprReviewsPlugin.status.declined');
            case REVIEW_ASSIGNMENT_STATUS_CANCELLED:
                return __('plugins.report.pprReviewsPlugin.status.cancelled');
            case REVIEW_ASSIGNMENT_STATUS_RECEIVED:
                return  __('plugins.report.pprReviewsPlugin.status.received');
            case REVIEW_ASSIGNMENT_STATUS_THANKED:
                return  __('plugins.report.pprReviewsPlugin.status.reviewerThanked');
            default:
                return '';
        }
    }
}