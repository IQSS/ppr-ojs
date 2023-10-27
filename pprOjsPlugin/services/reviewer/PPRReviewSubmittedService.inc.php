<?php

/**
 * Service to send an email when a reviewer submits a review
 */
class PPRReviewSubmittedService {

    private $pprPlugin;
    private $pprObjectFactory;

    public function __construct($plugin, $pprObjectFactory = null) {
        $this->pprPlugin = $plugin;
        $this->pprPlugin->import('util.PPRObjectFactory');
        $this->pprObjectFactory = $pprObjectFactory ?: new PPRObjectFactory();
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->reviewSubmittedEmailEnabled()) {
            HookRegistry::register('reviewerreviewstep3form::execute', array($this, 'sendReviewSubmittedConfirmationEmail'));
        }
    }

    function sendReviewSubmittedConfirmationEmail($hookName, $hookArgs) {
        $form = $hookArgs[0];
        $submission = $form->getReviewerSubmission();
        $review = $form->getReviewAssignment();
        $submissionId = $submission->getId();
        $reviewId = $review->getId();

        $reviewerId = $review->getReviewerId();
        $userDao = DAORegistry::getDAO('UserDAO');
        $reviewer = $userDao->getById($reviewerId);
        if (!$reviewer) {
            error_log("PPR[sendReviewSubmittedEmail] review=$reviewId submissionId=$submissionId message=no reviewer found");
            return;
        }

        $request = Application::get()->getRequest();
        $context = $request->getContext();
        $dateFormatShort = $context->getLocalizedDateFormatShort();

        $email = $this->pprObjectFactory->submissionMailTemplate($submission, 'PPR_REVIEW_SUBMITTED');
        $email->setContext($context);
        $email->setFrom($context->getData('contactEmail'), $context->getData('contactName'));
        $email->addRecipient($reviewer->getEmail(), $reviewer->getFullName());
        $email->assignParams([
            'reviewerFullName' => htmlspecialchars($reviewer->getFullName()),
            'reviewerFirstName' => htmlspecialchars($reviewer->getLocalizedGivenName()),
            'reviewerUserName' => htmlspecialchars($reviewer->getUsername()),
            'reviewDueDate' => strftime($dateFormatShort, strtotime($review->getDateDue())),
        ]);
        $email->send();
    }
}