<?php

/**
 * Service to send an email when a reviewer accepts a review
 *
 * Issue 112
 */
class PPRReviewAcceptedService {

    private $pprPlugin;
    private $pprObjectFactory;

    public function __construct($plugin, $pprObjectFactory = null) {
        $this->pprPlugin = $plugin;
        $this->pprPlugin->import('util.PPRObjectFactory');
        $this->pprObjectFactory = $pprObjectFactory ?: new PPRObjectFactory();
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->reviewAcceptedEmailEnabled()) {
            HookRegistry::register('pkpreviewerreviewstep1form::execute', array($this, 'sendReviewAcceptedConfirmationEmail'));
        }
    }

    function sendReviewAcceptedConfirmationEmail($hookName, $hookArgs) {
        $form = $hookArgs[0];
        $submission = $form->getReviewerSubmission();
        $review = $form->getReviewAssignment();
        $submissionId = $submission->getId();
        $reviewId = $review->getId();

        $reviewerId = $review->getReviewerId();
        $reviewer = $this->pprObjectFactory->submissionUtil()->getUser($reviewerId);
        if (!$reviewer) {
            error_log("PPR[sendReviewAcceptedEmail] review=$reviewId submissionId=$submissionId message=no reviewer found");
            return;
        }

        $request = Application::get()->getRequest();
        $dispatcher = $request->getDispatcher();
        $context = $request->getContext();
        $dateFormatShort = $context->getLocalizedDateFormatShort();

        $reviewUrlArgs = array('submissionId' => $review->getSubmissionId());
        $accessKeyLifeTime = $this->pprPlugin->getPluginSettings()->accessKeyLifeTime();
        if ($accessKeyLifeTime) {
            import('lib.pkp.classes.security.AccessKeyManager');
            $accessKeyManager = $this->pprObjectFactory->accessKeyManager();

            $accessKey = $accessKeyManager->createKey($context->getId(), $reviewer->getId(), $reviewId, $accessKeyLifeTime);
            $reviewUrlArgs = array_merge($reviewUrlArgs, array('reviewId' => $reviewId, 'key' => $accessKey));
        }

        $email = $this->pprObjectFactory->submissionMailTemplate($submission, 'PPR_REVIEW_ACCEPTED');
        $email->setContext($context);
        $email->setFrom($context->getData('contactEmail'), $context->getData('contactName'));
        $email->addRecipient($reviewer->getEmail(), $reviewer->getFullName());
        // EDITOR NAME WILL BE ADDED BY email/PPRFirstNameEmailService
        $email->assignParams([
            'reviewerFullName' => htmlspecialchars($reviewer->getFullName()),
            'reviewerFirstName' => htmlspecialchars($reviewer->getLocalizedGivenName()),
            'reviewerUserName' => htmlspecialchars($reviewer->getUsername()),
            'reviewDueDate' => strftime($dateFormatShort, strtotime($review->getDateDue())),
            'passwordResetUrl' => $dispatcher->url($request, ROUTE_PAGE, $context->getPath(), 'login', 'lostPassword'),
            'submissionReviewUrl' => $dispatcher->url($request, ROUTE_PAGE, null, 'reviewer', 'submission', null, $reviewUrlArgs)
        ]);
        $email->send();
    }
}