<?php

/**
 * Service to send an email when a reviewer accepts a review
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
        $userDao = DAORegistry::getDAO('UserDAO');
        $reviewer = $userDao->getById($reviewerId);
        if (!$reviewer) {
            error_log("PPR[sendReviewAcceptedEmail] review=$reviewId submissionId=$submissionId message=no author found");
            return;
        }

        $request = Application::get()->getRequest();
        $dispatcher = $request->getDispatcher();
        $context = $request->getContext();
        $dateFormatShort = $context->getLocalizedDateFormatShort();

        $editor = $this->pprObjectFactory->submissionUtil()->getSubmissionEditor($submissionId, $context->getId());
        $editorFullName = 'N/A';
        $editorFirstName = 'N/A';
        if($editor) {
            $editorFullName = htmlspecialchars($editor->getFullName());
            $editorFirstName = htmlspecialchars($editor->getLocalizedGivenName());
        }

        $email = $this->pprObjectFactory->submissionMailTemplate($submission, 'PPR_REVIEW_ACCEPTED');
        $email->setContext($context);
        $email->setFrom($context->getData('contactEmail'), $context->getData('contactName'));
        $email->addRecipient($reviewer->getEmail(), $reviewer->getFullName());
        $email->assignParams([
            'reviewerFullName' => htmlspecialchars($reviewer->getFullName()),
            'reviewerFirstName' => htmlspecialchars($reviewer->getLocalizedGivenName()),
            'reviewerUserName' => htmlspecialchars($reviewer->getUsername()),
            'reviewDueDate' => strftime($dateFormatShort, strtotime($review->getDateDue())),
            'editorFullName' => $editorFullName,
            'editorFirstName' => $editorFirstName,
            'passwordResetUrl' => $dispatcher->url($request, ROUTE_PAGE, $context->getPath(), 'login', 'lostPassword'),
            'submissionReviewUrl' => $dispatcher->url($request, ROUTE_PAGE, null, 'reviewer', 'submission', null, array('submissionId' => $review->getSubmissionId()))
        ]);
        $email->send();
    }
}