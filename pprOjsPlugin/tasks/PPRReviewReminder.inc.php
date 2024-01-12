<?php

use function PHP81_BC\strftime;

import('lib.pkp.classes.scheduledTask.ScheduledTask');
require_once(dirname(__FILE__) . '/PPRScheduledTask.inc.php');

/**
 * Task to send reviewers a review due date reminder. Based on OJS ReviewReminder class
 */
class PPRReviewReminder extends PPRScheduledTask {

    function __construct($args, $pprObjectFactory = null) {
        parent::__construct($args, $pprObjectFactory);
    }

    /**
     * @copydoc ScheduledTask::getName()
     */
    function getName() {
        return 'PPRReviewDueDateReviewerNotification';
    }

    function sendReminder ($reviewAssignment, $submission, $emailTemplate, $context) {
        $reviewId = $reviewAssignment->getId();
        $reviewer = $this->getUser($reviewAssignment->getReviewerId());
        if (!$reviewer) {
            $this->log($context, sprintf("Send Notification - No reviewer found submissionId=%s reviewAssigment=%s reviewerId=%s", $submission->getId(), $reviewId, $reviewAssignment->getReviewerId()));
            return false;
        }

        import('lib.pkp.classes.mail.SubmissionMailTemplate');
        $reviewerAccessKeysEnabled = $context->getData('reviewerAccessKeysEnabled');

        $email = new SubmissionMailTemplate($submission, $emailTemplate, $context->getPrimaryLocale(), $context, false);
        $email->setContext($context);
        $email->setReplyTo(null);
        $email->addRecipient($reviewer->getEmail(), $reviewer->getFullName());
        $email->setSubject($email->getSubject($context->getPrimaryLocale()));
        $email->setBody($email->getBody($context->getPrimaryLocale()));
        $email->setFrom($context->getData('contactEmail'), $context->getData('contactName'));

        $reviewUrlArgs = array('submissionId' => $reviewAssignment->getSubmissionId());
        if ($reviewerAccessKeysEnabled) {
            import('lib.pkp.classes.security.AccessKeyManager');
            $accessKeyManager = new AccessKeyManager();

            // Key lifetime is the typical review period plus four weeks
            $keyLifetime = ($context->getData('numWeeksPerReview') + 4) * 7;
            $accessKey = $accessKeyManager->createKey($context->getId(), $reviewer->getId(), $reviewId, $keyLifetime);
            $reviewUrlArgs = array_merge($reviewUrlArgs, array('reviewId' => $reviewId, 'key' => $accessKey));
        }

        $application = Application::get();
        $request = $application->getRequest();
        $dispatcher = $application->getDispatcher();
        $submissionReviewUrl = $dispatcher->url($request, ROUTE_PAGE, $context->getPath(), 'reviewer', 'submission', null, $reviewUrlArgs);

        // Format the review due date
        $reviewDueDate = strtotime($reviewAssignment->getDateDue());
        $dateFormatShort = $context->getLocalizedDateFormatShort();
        if ($reviewDueDate === -1 || $reviewDueDate === false) {
            // Default to something human-readable if no date specified
            $reviewDueDate = '_____';
        } else {
            $reviewDueDate = strftime($dateFormatShort, $reviewDueDate);
        }
        // Format the review response due date
        $responseDueDate = strtotime($reviewAssignment->getDateResponseDue());
        if ($responseDueDate === -1 || $responseDueDate === false) {
            // Default to something human-readable if no date specified
            $responseDueDate = '_____';
        } else {
            $responseDueDate = strftime($dateFormatShort, $responseDueDate);
        }

        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_REVIEWER);
        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_COMMON);
        $email->assignParams([
            'reviewerName' => htmlspecialchars($reviewer->getFullName()),
            'reviewerFirstName' => htmlspecialchars($reviewer->getLocalizedGivenName()),
            'reviewerUserName' => htmlspecialchars($reviewer->getUsername()),
            'reviewDueDate' => $reviewDueDate,
            'responseDueDate' => $responseDueDate,
            'editorialContactSignature' => htmlspecialchars($context->getData('contactName') . "\n" . $context->getLocalizedName()),
            'passwordResetUrl' => $dispatcher->url($request, ROUTE_PAGE, $context->getPath(), 'login', 'resetPassword', $reviewer->getUsername(), array('confirm' => Validation::generatePasswordResetHash($reviewer->getId()))),
            'submissionReviewUrl' => $submissionReviewUrl,
            'messageToReviewer' => __('reviewer.step1.requestBoilerplate'),
            'abstractTermIfEnabled' => htmlspecialchars($submission->getLocalizedAbstract() == '' ? '' : __('common.abstract')),
        ]);

        $email->send();
    }

    function executeForContext($context, $pprPlugin) {
        if (!$pprPlugin->getPluginSettings()->reviewReminderReviewerTaskEnabled()) {
            // THIS IS REQUIRED HERE AS THE CONFIGURED SCHEDULED TASKS ARE LOADED BY THE acron PLUGIN WHEN IT IS RELOADED
            $this->log($context, 'reviewReminderReviewerEnabled=false');
            return;
        }

        $reviewReminderReviewerDaysFromDueDate = $pprPlugin->getPluginSettings()->reviewReminderReviewerDaysFromDueDate();
        $this->log($context, 'Start - $reviewReminderReviewerDaysFromDueDate=' . $reviewReminderReviewerDaysFromDueDate);
        if ($reviewReminderReviewerDaysFromDueDate === null) {
            return;
        }

        $pprNotificationRegistry = new PPRTaskNotificationRegistry($context->getId());

        $reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO');
        $incompleteAssignments = [];
        foreach($reviewAssignmentDao->getIncompleteReviewAssignments() as $reviewAssignment) {
            $submission = $this->getSubmission($reviewAssignment->getSubmissionId());
            // FILTER ASSIGNMENTS FOR THIS CONTEXT
            if($submission->getContextId() === $context->getId()) {
                $incompleteAssignments[] = $reviewAssignment;
            }
        }

        $this->log($context, 'Processing Reviews - $incompleteAssignments=' . count($incompleteAssignments));

        $dueWithFilesNotifications = 0;
        $dueNotifications = 0;
        $pendingWithFilesNotifications = 0;
        $reviewAssignmentsAccepted = 0;
        $reviewAssignmentsDue = 0;
        foreach ($incompleteAssignments as $reviewAssignment) {
            // Fetch the submission
            $submissionId = $reviewAssignment->getSubmissionId();
            $reviewId = $reviewAssignment->getId();
            $submission = $this->getSubmission($submissionId);
            if (!$submission) continue;
            if ($submission->getStatus() != STATUS_QUEUED) continue;
            if (!$reviewAssignment->getDateConfirmed()) continue;

            $reviewAssignmentsAccepted++;
            $reviewAssignmentIsDue = false;
            if ($reviewAssignment->getDateDue()) {
                $checkDate = strtotime($reviewAssignment->getDateDue());
                if (time() - $checkDate > 60 * 60 * 24 * $reviewReminderReviewerDaysFromDueDate) {
                    $reviewAssignmentIsDue = true;
                    $reviewAssignmentsDue++;
                }
            }

            // CHECK IF REVIEW HAS FILES
            $reviewFilesCount = count(iterator_to_array(Services::get('submissionFile')->getMany([
                    'submissionIds' => [$submissionId],
                    'assocTypes' => [ASSOC_TYPE_REVIEW_ASSIGNMENT],
                    'assocIds' => [$reviewId]
                ])));

            $this->log($context, "Pending Review Files - submission=$submissionId review=$reviewId due=$reviewAssignmentIsDue files=$reviewFilesCount");

            if ($reviewAssignmentIsDue && $reviewFilesCount) {
                $notifications = $pprNotificationRegistry->getReviewDueDateWithFilesReviewerNotifications($reviewAssignment->getReviewerId(), $reviewId);
                if (empty($notifications)) {
                    $this->sendReminder($reviewAssignment, $submission, 'PPR_REVIEW_DUE_DATE_WITH_FILES_REVIEWER', $context);
                    $pprNotificationRegistry->registerReviewDueDateWithFilesReviewerNotification($reviewAssignment->getReviewerId(), $reviewId);
                    $dueWithFilesNotifications++;
                }
            } elseif ($reviewAssignmentIsDue) {
                $reviewNotifications = $pprNotificationRegistry->getReviewDueDateReviewerNotifications($reviewAssignment->getReviewerId(), $reviewId);
                if (empty($reviewNotifications)) {
                    $this->sendReminder($reviewAssignment, $submission, 'PPR_REVIEW_DUE_DATE_REVIEWER', $context);
                    $pprNotificationRegistry->registerReviewDueDateReviewerNotification($reviewAssignment->getReviewerId(), $reviewId);
                    $dueNotifications++;
                }
            } elseif ($reviewFilesCount) {
                $pendingReviewWithFilesNotifications = $pprNotificationRegistry->getReviewPendingWithFilesReviewerNotifications($reviewAssignment->getReviewerId(), $reviewId);
                if (empty($pendingReviewWithFilesNotifications)) {
                    $this->sendReminder($reviewAssignment, $submission, 'PPR_REVIEW_PENDING_WITH_FILES_REVIEWER', $context);
                    $pprNotificationRegistry->registerReviewPendingWithFilesReviewerNotification($reviewAssignment->getReviewerId(), $reviewId);
                    $pendingWithFilesNotifications++;
                }
            }
        }

        $this->log($context, "Completed - reviewAssignmentsAccepted=$reviewAssignmentsAccepted reviewAssignmentsDue=$reviewAssignmentsDue dueWithFilesNotifications=$dueWithFilesNotifications dueNotifications=$dueNotifications pendingWithFilesNotifications=$pendingWithFilesNotifications");
    }
}


