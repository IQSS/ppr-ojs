<?php

use function PHP81_BC\strftime;

require_once(dirname(__FILE__) . '/PPRScheduledTask.inc.php');

/**
 * Notification, in the form of an email, to authors. The email will be sent a period of time after
 * a submission is closed. This is used to send a survey to the author.
 * The initial requirement was to send an email after a year of the closed submission,
 * but the time in days is configurable in the plugin settings.
 */
class PPRSubmissionClosedAuthorNotification extends PPRScheduledTask {

    const EMAIL_TEMPLATE = 'PPR_SUBMISSION_CLOSED_AUTHOR';

    function __construct($args, $pprObjectFactory = null) {
        parent::__construct($args, $pprObjectFactory);
    }

    function getName() {
        return 'PPRSubmissionClosedAuthorNotification';
    }

    function sendNotification ($submission, $context) {
        $author = $this->getSubmissionAuthor($submission->getId());

        if (!$author) {
            $this->log($context, sprintf("Send Notification - No author assigned submissionId=%s", $submission->getId()));
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

        // EDITOR NAMES WILL BE ADDED BY services/email/PPRFirstNameEmailService
        $email->assignParams([
            'authorFirstName' => htmlspecialchars($author->getLocalizedGivenName()),
            'authorName' => htmlspecialchars($author->getFullName()),
            'editorialContactSignature' => htmlspecialchars($context->getData('contactName') . "\n" . $context->getLocalizedName()),
            'submissionUrl' => $submissionUrl,
        ]);

        $email->send();
    }

    function executeForContext($context, $pprPluginSettings) {
        if (!$pprPluginSettings->submissionClosedAuthorTaskEnabled()) {
            // THIS IS REQUIRED HERE AS THE CONFIGURED SCHEDULED TASKS ARE LOADED BY THE acron PLUGIN WHEN IT IS RELOADED
            $this->log($context, 'submissionClosedAuthorTaskEnabled=false');
            return;
        }

        $this->log($context, "Start");

        $metrics = [
            'submissions' => 0,
            'closedSubmissions' => 0,
            'closedSubmissionsAfterPeriod' => 0,
            'closedSubmissionsWithNotifications' => 0,
            'sendToAuthorMissing' => 0,
            'reviewFilesMissing' => 0,
            'sentNotifications' => 0,
        ];

        $submissionClosedWaitingDays = $pprPluginSettings->submissionClosedAuthorWaitingDays();
        $pprNotificationRegistry = new PPRTaskNotificationRegistry($context->getId());
        $submissionDao = DAORegistry::getDAO('SubmissionDAO');
        $allSubmissions = $submissionDao->getByContextId($context->getId());

        $closedSubmissions = [];
        while ($submission = $allSubmissions->next()) {
            $metrics['submissions']++;
            $closedDate = $submission->getData('closedDate');
            if ($closedDate) {
                $metrics['closedSubmissions']++;
                $closedSubmissions[] = $submission;
            }
        }

        $this->log($context, sprintf("Submissions to process - allSubmissions=%s closedSubmissions=%s", $metrics['submissions'], $metrics['closedSubmissions']));

        // METRICS
        foreach ($closedSubmissions as $closedSubmission) {
            $closedDate = strtotime($closedSubmission->getData('closedDate'));
            if (time() - $closedDate > 60 * 60 * 24 * $submissionClosedWaitingDays) {
                $metrics['closedSubmissionsAfterPeriod']++;

                if (!$this->requestedRevisionsForSubmission($closedSubmission)) {
                    // ALL CLOSED SUBMISSIONS SHOULD HAVE BEEN SENT TO AUTHOR
                    // LOG TO DEBUG WITH PRODUCT TEAM
                    $this->log($context, sprintf("sendToAuthor not found - closedSubmission=%s", $closedSubmission->getId()));
                    $metrics['sendToAuthorMissing']++;
                    continue;
                }

                if (!$this->reviewFilesForSubmission($closedSubmission)) {
                    // ALL CLOSED SUBMISSIONS SHOULD HAVE REVIEW FILES
                    // LOG TO DEBUG WITH PRODUCT TEAM
                    $this->log($context, sprintf("reviewFiles not found - closedSubmission=%s", $closedSubmission->getId()));
                    $metrics['reviewFilesMissing']++;
                    continue;
                }

                $authorNotifications = $pprNotificationRegistry->getSubmissionClosedAuthorNotification($closedSubmission->getId());
                if (empty($authorNotifications)) {
                    $this->sendNotification($closedSubmission, $context);
                    $pprNotificationRegistry->registerSubmissionClosedAuthorNotification($closedSubmission->getId());
                    $metrics['sentNotifications']++;
                } else {
                    $metrics['closedSubmissionsWithNotifications']++;
                }
            }
        }

        $this->log($context, sprintf("Completed - closedSubmissions=%s closedSubmissionsAfterPeriod=%s sendToAuthorMissing=%s reviewFilesMissing=%s closedSubmissionsWithNotifications=%s sentNotifications=%s",
            $metrics['closedSubmissions'],
            $metrics['closedSubmissionsAfterPeriod'],
            $metrics['sendToAuthorMissing'],
            $metrics['reviewFilesMissing'],
            $metrics['closedSubmissionsWithNotifications'],
            $metrics['sentNotifications']));
    }

    private function reviewFilesForSubmission($submission) {
        import('lib.pkp.classes.submission.SubmissionFile'); // Bring the file constants.
        $params = [
            'submissionIds' => [$submission->getId()],
            'fileStages' => [SUBMISSION_FILE_REVIEW_ATTACHMENT],
        ];
        $submissionFilesIterator = Services::get('submissionFile')->getMany($params);
        $submissionReviewFiles = iterator_to_array($submissionFilesIterator);

        if (empty($submissionReviewFiles)) {
            return false;
        }

        return true;
    }

    private function requestedRevisionsForSubmission($submission) {
        $editDecisionDao = DAORegistry::getDAO('EditDecisionDAO');
        $editorDecisions = $editDecisionDao->getEditorDecisions($submission->getId());
        foreach ($editorDecisions as $decision) {
            if ($decision['decision'] === SUBMISSION_EDITOR_DECISION_PENDING_REVISIONS) {
                return true;
            }
        }

        return false;
    }
}


