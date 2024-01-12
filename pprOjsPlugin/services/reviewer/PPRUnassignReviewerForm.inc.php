<?php

import('lib.pkp.controllers.grid.users.reviewer.form.UnassignReviewerForm');
require_once(dirname(__FILE__) . '/../../util/PPRMissingUser.inc.php');

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

    private $pprObjectFactory;

    function __construct($reviewAssignment, $reviewRound, $submission, $pprObjectFactory = null) {
        $this->pprObjectFactory = $pprObjectFactory ?: new PPRObjectFactory();
        parent::__construct($reviewAssignment, $reviewRound, $submission);
    }

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
     * Override initData to add reviewer, editor, and author names to the email template
     */
    public function initData() {
        parent::initData();
        $reviewAssignment = $this->getReviewAssignment();
        $reviewerId = $reviewAssignment->getReviewerId();
        $submission = $this->getSubmission();

        $reviewer = $this->getReviewer($reviewerId);
        $editor = $this->getSubmissionEditor($submission->getId(), $submission->getContextId());
        $author = $this->getSubmissionAuthor($submission->getId());

        // WE NEED ANY EMAIL TEMPLATE TO OVERRIDE THE BODY AND USE THE replaceParams METHOD
        $mailTemplate = new MailTemplate();
        $mailTemplate->setBody($this->getData('personalMessage'));
        $mailTemplate->assignParams([
            'reviewerName' =>  htmlspecialchars($reviewer->getFullName()),
            'reviewerFirstName' =>  htmlspecialchars($reviewer->getLocalizedGivenName()),
            'editorName' => htmlspecialchars($editor->getFullName()),
            'editorFirstName' => htmlspecialchars($editor->getLocalizedGivenName()),
            'authorName' => htmlspecialchars($author->getFullName()),
            'authorFirstName' => htmlspecialchars($author->getLocalizedGivenName()),
        ]);
        $mailTemplate->replaceParams();
        $this->setData('personalMessage', $mailTemplate->getBody());
    }

    private function getReviewer($reviewerId) {
        $userDao = DAORegistry::getDAO('UserDAO');
        return $userDao->getById($reviewerId) ?? new PPRMissingUser(__('ppr.user.missing.name'));
    }

    private function getSubmissionEditor($submissionId, $contextId) {
        $submissionEditors = $this->pprObjectFactory->submissionUtil()->getSubmissionEditors($submissionId, $contextId);
        //GET FIRST EDITOR
        return empty($submissionEditors) ? new PPRMissingUser(__('ppr.user.missing.name')) : reset($submissionEditors);
    }

    private function getSubmissionAuthor($submissionId) {
        $submissionAuthors = $this->pprObjectFactory->submissionUtil()->getSubmissionAuthors($submissionId);
        //GET FIRST AUTHOR
        return empty($submissionAuthors) ? new PPRMissingUser(__('ppr.user.missing.name')) : reset($submissionAuthors);
    }
}