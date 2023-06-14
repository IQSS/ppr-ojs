<?php

/**
 * Plain old PHP object to hold the required data to send a review notification
 */
class PPRDueReviewData {
    const REVIEW_DUE_DATE_TYPE = 'REVIEW_DUE_DATE';
    const REVIEW_RESPONSE_DUE_DATE_TYPE = 'REVIEW_RESPONSE_DUE_DATE';

    private $type;
    private $reviewAssignment;
    private $dueDate;
    private $daysFromDueDate;

    public function __construct($type, $reviewAssignment, $dueDate, $daysFromDueDate) {
        $this->type = $type;
        $this->reviewAssignment = $reviewAssignment;
        $this->dueDate = $dueDate;
        $this->daysFromDueDate = $daysFromDueDate;
    }

    public function getType() {
        return $this->type;
    }

    public function getSubmissionId() {
        return $this->reviewAssignment->getSubmissionId();
    }

    public function getReviewId() {
        return $this->reviewAssignment->getId();
    }

    public function getReviewerId() {
        return $this->reviewAssignment->getReviewerId();
    }

    public function getDueDate() {
        return $this->dueDate;
    }

    public function getDaysFromDueDate() {
        return $this->daysFromDueDate;
    }

    public function isReviewDueDate() {
        return $this->type === self::REVIEW_DUE_DATE_TYPE;
    }

    public function isReviewResponseDueDate() {
        return $this->type === self::REVIEW_RESPONSE_DUE_DATE_TYPE;
    }
}
