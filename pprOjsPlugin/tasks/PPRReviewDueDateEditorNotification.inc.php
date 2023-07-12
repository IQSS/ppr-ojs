<?php

use function PHP81_BC\strftime;

import('lib.pkp.classes.scheduledTask.ScheduledTask');

/**
 * Custom review due date notification for editors logic
 */
class PPRReviewDueDateEditorNotification extends ScheduledTask {

    private $userCache;

    const EMAIL_TEMPLATE = 'PPR_REVIEW_DUE_DATE_EDITOR';

    private $pprPlugin;

    function __construct($args) {
        $this->pprPlugin = PluginRegistry::getPlugin('generic', 'peerprereviewprogramplugin');
        if (!$this->pprPlugin) {
            // IF PLUGIN IS NOT CURRENTLY LOADED, LOAD MANUALLY
            $this->pprPlugin = PluginRegistry::loadPlugin('generic', 'pprOjsPlugin');
        }

        assert(isset($this->pprPlugin));
        $this->pprPlugin->import('tasks.PPRNotificationRegistry');
        $this->pprPlugin->import('tasks.PPRDueReviewData');

        $this->userCache = [];

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
        if (!$this->pprPlugin->getPluginSettings()->reviewReminderEditorEnabled()) {
            // THIS IS REQUIRED HERE AS THE CONFIGURED SCHEDULED TASKS ARE LOADED BY THE acron PLUGIN WHEN IT IS RELOADED
            $this->log('disabled=true');
            return true;
        }

        $reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO'); /* @var $reviewAssignmentDao ReviewAssignmentDAO */
        $submissionDao = DAORegistry::getDAO('SubmissionDAO'); /* @var $submissionDao SubmissionDAO */

        $incompleteAssignments = $reviewAssignmentDao->getIncompleteReviewAssignments();
        $this->log('Start - $incompleteAssignments=' . count($incompleteAssignments));
        if (empty($incompleteAssignments)) {
            // NO INCOMPLETE ASSIGNMENTS
            return true;
        }

        $pprNotificationRegistry = new PPRTaskNotificationRegistry($this->pprPlugin->getPluginSettings()->getContextId());
        $reviewReminderEditorDaysFromDueDate = $this->pprPlugin->getPluginSettings()->reviewReminderEditorDaysFromDueDate();
        $this->log('Processing reviews - $reviewReminderEditorDaysFromDueDate=' . implode(", ", $reviewReminderEditorDaysFromDueDate));
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

        $this->log('Reviews with due dates - $assignmentsWithDueReviews=' . count($assignmentsWithDueReviews));
        if (empty($assignmentsWithDueReviews)) {
            // NO SUBMISSIONS WITH DUE REVISIONS
            return true;
        }

        $editorGroupId = $this->findEditorGroupId($this->pprPlugin->getPluginSettings()->getContextId());

        $contextDao = Application::getContextDAO();
        $context = $contextDao->getById($this->pprPlugin->getPluginSettings()->getContextId());

        $sentNotifications = 0;
        $assignmentsWithNotifications = 0;
        foreach ($assignmentsWithDueReviews as $dueReviewData) {
            // Fetch the submission
            $submission = $submissionDao->getById($dueReviewData->getSubmissionId());
            if (!$submission) continue;
            if ($submission->getStatus() != STATUS_QUEUED) continue;
            if ($submission->getContextId() !== $this->pprPlugin->getPluginSettings()->getContextId()) continue;

            $editors = $this->findAssociateEditors($editorGroupId, $submission->getId());
            if (empty($editors)) {
                // NO EDITOR ASSIGNED TO SUBMISSION
                $this->log('Processing reviews - no editors assigned to submission: ' . $submission->getId());
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

        $this->log("Completed - assignmentsWithNotifications=$assignmentsWithNotifications sentNotifications=$sentNotifications");
        return true;
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

    private function log($message) {
        error_log('PPR[PPRReviewDueDateEditorNotification] ' . $message);
    }
}


