<?php

import('lib.pkp.controllers.grid.users.reviewer.form.UnassignReviewerForm');

/**
 * Override UnassignReviewerForm to select email template based on the assignment status and set data
 *
 * This form is used in the ReviewerGridHandler
 */
class PPRUnassignReviewerForm extends UnassignReviewerForm {

    // REQUEST SENT TO REVIEWER, REVIEWER HAS NOT ACCEPTED REQUEST YET
    const UNASSIGN_REQUESTED_REVIEWER_EMAIL = 'PPR_REQUESTED_REVIEWER_UNASSIGN';
    // REQUEST SENT TO REVIEWER, REVIEWER ACCEPTED REQUEST
    const UNASSIGN_CONFIRMED_REVIEWER_EMAIL = 'PPR_CONFIRMED_REVIEWER_UNASSIGN';

    /**
     * Override emailKey to based it in the review status
     */
    public function getEmailKey() {
        $reviewAssignment = $this->getReviewAssignment();
        $emailKey = self::UNASSIGN_REQUESTED_REVIEWER_EMAIL;
        if ($reviewAssignment->getDateConfirmed()) {
            $emailKey = self::UNASSIGN_CONFIRMED_REVIEWER_EMAIL;
        }

        return $emailKey;
    }

    /**
     * Override initData to add $reviewerFirstName to the email template
     */
    public function initData() {
        parent::initData();
        $reviewAssignment = $this->getReviewAssignment();
        $reviewerId = $reviewAssignment->getReviewerId();

        $userDao = DAORegistry::getDAO('UserDAO');
        $reviewer = $userDao->getById($reviewerId);
        if ($reviewer) {
            $newBody = str_replace('{$reviewerFirstName}', $reviewer->getLocalizedGivenName(), $this->getData('personalMessage'));
            $this->setData('personalMessage', $newBody);
        }
    }
}