<?php

import('lib.pkp.controllers.grid.users.reviewer.form.ReviewerNotifyActionForm');
require_once(dirname(__FILE__) . '/../../util/PPRMissingUser.inc.php');

/**
 * This is a copy of the UnassignReviewerForm to customize the OJS functionality
 * lib.pkp.controllers.grid.users.reviewer.form.UnassignReviewerForm
 *
 * Customizations:
 *  - Email template selection based on the assignment status (requested Vs accepted).
 *  - Set template data with new email data.
 *  - Always cancel the review assignment when unassign reviewer is executed.
 *
 * This form is used in the ReviewerGridHandler
 *
 * Issue 092
 */
class PPRUnassignReviewerForm extends ReviewerNotifyActionForm {

    // REQUEST SENT TO REVIEWER, REVIEWER HAS NOT ACCEPTED REQUEST YET
    const UNASSIGN_REQUESTED_REVIEWER_EMAIL = 'PPR_REQUESTED_REVIEWER_UNASSIGN';
    // REQUEST SENT TO REVIEWER, REVIEWER ACCEPTED REQUEST
    const UNASSIGN_CONFIRMED_REVIEWER_EMAIL = 'PPR_CONFIRMED_REVIEWER_UNASSIGN';

    private $pprObjectFactory;

    function __construct($reviewAssignment, $reviewRound, $submission, $pprObjectFactory = null) {
        $this->pprObjectFactory = $pprObjectFactory ?: new PPRObjectFactory();
        parent::__construct($reviewAssignment, $reviewRound, $submission, 'controllers/grid/users/reviewer/form/unassignReviewerForm.tpl');
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

    /**
     * This is a copy from OJS UnassignReviewerForm.execute.
     *
     * We are customizing to always cancel the review assignment. Everything else is left as it is.
     */
    function execute(...$functionArgs) {
        if (!parent::execute(...$functionArgs)) return false;

        $request = Application::get()->getRequest();
        $submission = $this->getSubmission();
        $reviewAssignment = $this->getReviewAssignment();

        // Delete or cancel the review assignment.
        $submissionDao = DAORegistry::getDAO('SubmissionDAO'); /* @var $submissionDao SubmissionDAO */
        $reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO'); /* @var $reviewAssignmentDao ReviewAssignmentDAO */
        $userDao = DAORegistry::getDAO('UserDAO'); /* @var $userDao UserDAO */

        if (isset($reviewAssignment) && $reviewAssignment->getSubmissionId() == $submission->getId() && !HookRegistry::call('EditorAction::clearReview', array(&$submission, $reviewAssignment))) {
            $reviewer = $userDao->getById($reviewAssignment->getReviewerId());
            if (!isset($reviewer)) return false;

            // PPR UPDATE => ALWAYS CANCEL $reviewAssignment, NEVER DELETES
            $reviewAssignment->setCancelled(true);
            $reviewAssignmentDao->updateObject($reviewAssignment);
            // END PPR UPDATE

            // Stamp the modification date
            $submission->stampModified();
            $submissionDao->updateObject($submission);

            $notificationDao = DAORegistry::getDAO('NotificationDAO'); /* @var $notificationDao NotificationDAO */
            $notificationDao->deleteByAssoc(
                ASSOC_TYPE_REVIEW_ASSIGNMENT,
                $reviewAssignment->getId(),
                $reviewAssignment->getReviewerId(),
                NOTIFICATION_TYPE_REVIEW_ASSIGNMENT
            );

            // Insert a trivial notification to indicate the reviewer was removed successfully.
            $currentUser = $request->getUser();
            $notificationMgr = new NotificationManager();
            // PPR UPDATE => ALWAYS CANCEL NOTIFICATION MESSAGE
            $notificationMgr->createTrivialNotification($currentUser->getId(), NOTIFICATION_TYPE_SUCCESS, array('contents' => __('notification.cancelledReviewer')));
            // END PPR UPDATE

            // Add log
            import('lib.pkp.classes.log.SubmissionLog');
            import('classes.log.SubmissionEventLogEntry');
            SubmissionLog::logEvent($request, $submission, SUBMISSION_LOG_REVIEW_CLEAR, 'log.review.reviewCleared', array('reviewAssignmentId' => $reviewAssignment->getId(), 'reviewerName' => $reviewer->getFullName(), 'submissionId' => $submission->getId(), 'stageId' => $reviewAssignment->getStageId(), 'round' => $reviewAssignment->getRound()));

            return true;
        }
        return false;
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