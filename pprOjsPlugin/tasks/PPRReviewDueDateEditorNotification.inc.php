<?php

use function PHP81_BC\strftime;

import('lib.pkp.classes.scheduledTask.ScheduledTask');
require_once(dirname(__FILE__) . '/PPRNotificationRegistry.inc.php');
require_once(dirname(__FILE__) . '/PPRDueReviewData.inc.php');

/**
 * Custom review due date notification for editors logic
 */
class PPRReviewDueDateEditorNotification extends ScheduledTask {

    private $userCache;
    private $submissionCache;

    const EMAIL_TEMPLATE = 'PPR_REVIEW_DUE_DATE_EDITOR';

    function __construct($args) {
        $this->userCache = [];
        $this->submissionCache = [];

        parent::__construct($args);
    }

    function getName() {
        return 'Peer Pre-Review Program custom review due date notification for editors';
    }

    function sendReminder ($reviewDueData, $submission, $context, $editor) {
        import('lib.pkp.classes.mail.SubmissionMailTemplate');
        $email = new SubmissionMailTemplate($submission, self::EMAIL_TEMPLATE, $context->getPrimaryLocale(), $context, false);
        $email->setContext($context);
        $email->setReplyTo(null);
        $email->addRecipient($editor->getEmail(), $editor->getFullName());
        $email->setSubject($email->getSubject($context->getPrimaryLocale()));
        $email->setBody($email->getBody($context->getPrimaryLocale()));
        $email->setFrom($context->getData('contactEmail'), $context->getData('contactName'));

        $application = Application::get();
        $request = $application->getRequest();
        $dispatcher = $application->getDispatcher();
        $submissionReviewUrl = $dispatcher->url($request, ROUTE_PAGE, $context->getPath(), 'workflow', 'access', [$submission->getId()]);

        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_REVIEWER);
        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_COMMON);

        $stageAssignmentDao = DAORegistry::getDAO('StageAssignmentDAO');
        $userDao = DAORegistry::getDAO('UserDAO');

        $reviewer = $userDao->getById($reviewDueData->getReviewerId());
        if (!isset($reviewer)) return false;

        $assignedAuthors = $stageAssignmentDao->getBySubmissionAndRoleId($reviewDueData->getSubmissionId(), ROLE_ID_AUTHOR, WORKFLOW_STAGE_ID_SUBMISSION)->toArray();
        $authorNames = [];
        foreach ($assignedAuthors as $assignedAuthor) {
            $author = $userDao->getById($assignedAuthor->getUserId());
            $authorNames[] = htmlspecialchars($author->getFullName());
        }
        $authorsString = empty($authorNames) ? 'N/A' : implode(', ', $authorNames);

        $reviewDueDate = strtotime($reviewDueData->getDueDate());
        $dateFormatShort = $context->getLocalizedDateFormatShort();
        if ($reviewDueDate === -1 || $reviewDueDate === false) {
            // Default to something human-readable if no date specified
            $reviewDueDate = '_____';
        } else {
            $reviewDueDate = strftime($dateFormatShort, $reviewDueDate);
        }

        $email->assignParams([
            'reviewDueDate' => $reviewDueDate,
            'authorName' => $authorsString,
            'reviewerName' => htmlspecialchars($reviewer->getFullName()),
            'editorName' => htmlspecialchars($editor->getFullName()),
            'editorialContactSignature' => htmlspecialchars($context->getData('contactName') . "\n" . $context->getLocalizedName()),
            'submissionReviewUrl' => $submissionReviewUrl,
        ]);

        $email->send();
    }

    function executeActions() {
        $contextDao = Application::getContextDAO();
        // PROCESS REVIEW REMINDERS FOR ALL OJS CONTEXTS
        $ojsEnabledContexts = $contextDao->getAll(true)->toArray();
        foreach ($ojsEnabledContexts as $context) {
            $pprPlugin = PluginRegistry::loadPlugin('generic', 'pprOjsPlugin', $context->getId());
            if (!$pprPlugin->getEnabled($context->getId())) {
                // PLUGIN NOT ENABLED FOR CURRENT CONTEXT
                $this->log($context, 'PPRPluginEnabled=false');
                continue;
            }

            if (!$pprPlugin->getPluginSettings()->reviewReminderEditorEnabled()) {
                // THIS IS REQUIRED HERE AS THE CONFIGURED SCHEDULED TASKS ARE LOADED BY THE acron PLUGIN WHEN IT IS RELOADED
                $this->log($context, 'reviewReminderEditorEnabled=false');
                continue;
            }

            $this->reviewRemindersForContext($context, $pprPlugin);
        }

        //RETURN SUCCESS
        return true;
    }


    private function reviewRemindersForContext($context, $pprPlugin) {
        $reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO');
        $incompleteAssignments = [];
        foreach($reviewAssignmentDao->getIncompleteReviewAssignments() as $assignment) {
            $submission = $this->getSubmission($assignment->getSubmissionId());
            // FILTER ASSIGNMENTS FOR THIS CONTEXT
            if($submission->getContextId() === $context->getId()) {
                $incompleteAssignments[] = $assignment;
            }
        }

        $this->log($context, 'Start - $incompleteAssignments=' . count($incompleteAssignments));
        if (empty($incompleteAssignments)) {
            // NO INCOMPLETE ASSIGNMENTS
            return true;
        }

        $pprNotificationRegistry = new PPRTaskNotificationRegistry($context->getId());
        $reviewReminderEditorDaysFromDueDate = $pprPlugin->getPluginSettings()->reviewReminderEditorDaysFromDueDate();
        $this->log($context, 'Processing reviews - $reviewReminderEditorDaysFromDueDate=' . implode(", ", $reviewReminderEditorDaysFromDueDate));
        if (empty($reviewReminderEditorDaysFromDueDate)) {
            // NO CONFIGURED FROM DUE DATES
            return true;
        }

        $assignmentsWithDueReviews = [];
        foreach ($incompleteAssignments as $reviewAssignment) {
            // CHECK REVIEW DUE DATE
            $dueReviewData = $this->checkDate(PPRDueReviewData::REVIEW_DUE_DATE_TYPE, $reviewAssignment, $reviewReminderEditorDaysFromDueDate, $reviewAssignment->getDateDue());
            if ($reviewAssignment->getDateConfirmed() == null) {
                // CHECK REVIEW RESPONSE DUE DATE
                $dueReviewData = $this->checkDate(PPRDueReviewData::REVIEW_RESPONSE_DUE_DATE_TYPE, $reviewAssignment, $reviewReminderEditorDaysFromDueDate, $reviewAssignment->getDateResponseDue());
            }

            if ($dueReviewData) {
                // SET THE DUE REVIEW DATA TO TRIGGER NOTIFICATION
                $assignmentsWithDueReviews[] = $dueReviewData;
            }
        }

        $this->log($context, 'Reviews with due dates - $assignmentsWithDueReviews=' . count($assignmentsWithDueReviews));
        if (empty($assignmentsWithDueReviews)) {
            // NO SUBMISSIONS WITH DUE REVISIONS
            return true;
        }

        $editorGroupId = $this->findEditorGroupId($context->getId());

        $sentNotifications = 0;
        $assignmentsWithNotifications = 0;
        foreach ($assignmentsWithDueReviews as $dueReviewData) {
            // Fetch the submission
            $submission = $this->getSubmission($dueReviewData->getSubmissionId());
            if (!$submission) continue;
            if ($submission->getStatus() != STATUS_QUEUED) continue;

            $editors = $this->findAssociateEditors($editorGroupId, $submission->getId());
            if (empty($editors)) {
                // NO EDITOR ASSIGNED TO SUBMISSION
                $this->log($context, 'Processing reviews - no editors assigned to submission: ' . $submission->getId());
                continue;
            }

            // SKIP REVIEW ASSIGNMENTS THAT HAVE ALREADY SENT A NOTIFICATION
            $reviewNotifications = $pprNotificationRegistry->getReviewDueDateEditorNotifications($dueReviewData);
            if (empty($reviewNotifications)) {
                foreach ($editors as $editor) {
                    $this->sendReminder($dueReviewData, $submission, $context, $editor);
                    $sentNotifications++;
                }
                $pprNotificationRegistry->registerReviewDueDateEditorNotification($dueReviewData);
                $assignmentsWithNotifications++;
            }
        }

        $this->log($context, "Completed - assignmentsWithNotifications=$assignmentsWithNotifications sentNotifications=$sentNotifications");
    }

    private function checkDate($type, $reviewAssignment, $reviewReminderEditorDaysFromDueDate, $reviewAssigmentDate) {
        if ($reviewAssigmentDate === null) {
            return null;
        }

        foreach ($reviewReminderEditorDaysFromDueDate as $daysFromDueDate) {
            if ($reviewAssigmentDate != null) {
                $reviewDueDate = strtotime($reviewAssigmentDate);
                if (time() - $reviewDueDate > 60 * 60 * 24 * $daysFromDueDate) {
                    return new PPRDueReviewData($type, $reviewAssignment, $reviewAssigmentDate, $daysFromDueDate);
                }
            }
        }

        return null;
    }

    private function findEditorGroupId($contextId) {
        $ASSOCIATE_EDITOR_GROUP_NAME = __('tasks.ppr.editor.groupName');
        $userGroupDao = DAORegistry::getDAO('UserGroupDAO');
        $userGroups = $userGroupDao->getByContextId($contextId)->toArray();
        foreach ($userGroups as $userGroup) {
            if (0 === strcasecmp($userGroup->getLocalizedName(), $ASSOCIATE_EDITOR_GROUP_NAME)) {
                return $userGroup->getId();
            }
        }

        return null;
    }

    private function findAssociateEditors($editorGroupId, $submissionId) {
        $stageAssignmentDao = DAORegistry::getDAO('StageAssignmentDAO');
        $stageAssignments = $stageAssignmentDao->getBySubmissionAndStageId($submissionId)->toArray();
        $editors = [];
        foreach ($stageAssignments as $stageAssignment) {
            if ($stageAssignment->getUserGroupId() === $editorGroupId){
                $editors[$stageAssignment->getUserId()] = $this->getUser($stageAssignment->getUserId());
            }
        }

        return array_values($editors);
    }

    private function getUser($userId) {
        if(!isset($this->userCache[$userId])) {
            $userDao = DAORegistry::getDAO('UserDAO');
            $this->userCache[$userId] = $userDao->getById($userId);
        }

        return $this->userCache[$userId];
    }

    private function getSubmission($submissionId) {
        if(!isset($this->submissionCache[$submissionId])) {
            $submissionDao = DAORegistry::getDAO('SubmissionDAO');
            $this->submissionCache[$submissionId] = $submissionDao->getById($submissionId);
        }

        return $this->submissionCache[$submissionId];
    }

    private function log($context, $message) {
        error_log('PPR[PPRReviewDueDateEditorNotification] context=' . $context->getPath() . ' ' .$message);
    }
}


