<?php

/**
 * Service to send an email to the reviewer when a review is submitted
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
        $reviewer = $this->pprObjectFactory->submissionUtil()->getUser($reviewerId);
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
        // EDITOR NAME WILL BE ADDED BY email/PPRFirstNameEmailService
        $email->assignParams([
            'reviewerFullName' => htmlspecialchars($reviewer->getFullName()),
            'reviewerFirstName' => htmlspecialchars($reviewer->getLocalizedGivenName()),
            'reviewerUserName' => htmlspecialchars($reviewer->getUsername()),
            'reviewDueDate' => strftime($dateFormatShort, strtotime($review->getDateDue())),
        ]);
        $email->send();
    }
}