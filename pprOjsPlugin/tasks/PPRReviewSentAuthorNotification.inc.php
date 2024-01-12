<?php

use function PHP81_BC\strftime;

require_once(dirname(__FILE__) . '/PPRScheduledTask.inc.php');

/**
 * Notification to authors to review the program a period after a review has been sent to them
 */
class PPRReviewSentAuthorNotification extends PPRScheduledTask {

    const EMAIL_TEMPLATE = 'PPR_REVIEW_SENT_AUTHOR';

    function __construct($args, $pprObjectFactory = null) {
        parent::__construct($args, $pprObjectFactory);
    }

    function getName() {
        return 'PPRReviewSentAuthorNotification';
    }

    function sendNotification ($reviewFile, $context) {
        $submission = $this->getSubmission($reviewFile->getData('submissionId'));
        $author = $this->getSubmissionAuthor($submission->getId());

        if (!$author) {
            $this->log($context, sprintf("Send Notification - No author assigned submissionId=%s reviewAssigment=%s", $submission->getId(), $reviewFile->getData('assocId')));
            return;
        }

        import('lib.pkp.classes.mail.SubmissionMailTemplate');
        $email = new SubmissionMailTemplate($submission, self::EMAIL_TEMPLATE, $context->getPrimaryLocale(), $context, false);
        $email->setContext($context);
        $email->setReplyTo(null);
        $email->addRecipient($author->getEmail(), $author->getFullName());
        $email->setSubject($email->getSubject());
        $email->setBody($email->getBody());
        $email->setFrom($context->getData('contactEmail'), $context->getData('contactName'));

        $application = Application::get();
        $request = $application->getRequest();
        $dispatcher = $application->getDispatcher();
        $submissionUrl = $dispatcher->url($request, ROUTE_PAGE, $context->getPath(), 'workflow', 'access', [$submission->getId()]);

        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_REVIEWER);
        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_COMMON);

        $editor = $this->getSubmissionEditor($submission->getId(), $context->getId()) ?? PPRMissingUser::defaultMissingUser();

        $email->assignParams([
            'authorFirstName' => htmlspecialchars($author->getLocalizedGivenName()),
            'authorName' => htmlspecialchars($author->getFullName()),
            'editorFirstName' => htmlspecialchars($editor->getLocalizedGivenName()),
            'editorName' => htmlspecialchars($editor->getFullName()),
            'editorialContactSignature' => htmlspecialchars($context->getData('contactName') . "\n" . $context->getLocalizedName()),
            'submissionUrl' => $submissionUrl,
        ]);

        $email->send();
    }

    function executeForContext($context, $pprPlugin) {
        if (!$pprPlugin->getPluginSettings()->reviewSentAuthorTaskEnabled()) {
            // THIS IS REQUIRED HERE AS THE CONFIGURED SCHEDULED TASKS ARE LOADED BY THE acron PLUGIN WHEN IT IS RELOADED
            $this->log($context, 'reviewSentAuthorTaskEnabled=false');
            return;
        }

        $reviewSentWaitingDays = $pprPlugin->getPluginSettings()->reviewSentAuthorWaitingDays();
        $reviewSentAuthorEnabledDate = $pprPlugin->getPluginSettings()->reviewSentAuthorEnabledDate();
        $submissionFileService = Services::get('submissionFile');
        $pprNotificationRegistry = new PPRTaskNotificationRegistry($context->getId());

        import('lib.pkp.classes.submission.SubmissionFile'); // Bring the file constants.
        $reviewFiles = iterator_to_array($submissionFileService->getMany(['fileStages' => [SUBMISSION_FILE_REVIEW_ATTACHMENT]]));
        $this->log($context, sprintf("Start - reviewFiles=%s reviewSentWaitingDays=%s reviewSentAuthorEnabledDate=%s", count($reviewFiles), $reviewSentWaitingDays, $reviewSentAuthorEnabledDate));

        $metrics = [
            'sentReviewFiles' => 0,
            'sentNotifications' => 0,
            'waitingNotifications' => 0,
        ];
        // FIND REVIEW FILES SENT TO SEND NOTIFICATIONS
        foreach ($reviewFiles as $reviewFile) {
            if (strtotime($reviewFile->getData('createdAt')) < strtotime($reviewSentAuthorEnabledDate)) {
                //IGNORE FILES THAT HAVE BEEN CREATED BEFORE THIS FEATURE IS ENABLED
                continue;
            }

            if (!$reviewFile->getViewable()) {
                //IGNORE FILES THAT HAVE NOT BEEN SENT
                continue;
            }

            $metrics['sentReviewFiles']++;

            //REVIEW FILE HAS BEEN SENT => SET NOTIFICATION FLAG
            $reviewerId = $reviewFile->getUploaderUserId();
            $reviewAssignmentId = $reviewFile->getData('assocId');
            $authorNotifications = $pprNotificationRegistry->getReviewSentAuthorNotifications($reviewerId, $reviewAssignmentId);
            if (empty($authorNotifications)) {
                $pprNotificationRegistry->registerReviewSentAuthorNotification($reviewerId, $reviewAssignmentId);
                $authorNotifications = $pprNotificationRegistry->getReviewSentAuthorNotifications($reviewerId, $reviewAssignmentId);
            }

            $authorNotification = reset($authorNotifications);
            if(!$authorNotification->getDateRead()) {
                $metrics['waitingNotifications']++;
                //CHECK DATE
                $checkDate = strtotime($authorNotification->getDateCreated());
                if (time() - $checkDate > 60 * 60 * 24 * $reviewSentWaitingDays) {
                    $this->sendNotification($reviewFile, $context);
                    $pprNotificationRegistry->updateDateRead($authorNotification->getId());
                    $metrics['sentNotifications']++;
                }
            }
        }

        $this->log($context, sprintf("Completed - sentReviewFiles=%s waitingNotifications=%s sentReviewFilesAuthorNotifications=%s", $metrics['sentReviewFiles'], $metrics['waitingNotifications'], $metrics['sentNotifications']));
    }
}


