<?php

class PPRTaskNotificationRegistry {

    public const NOTIFICATION_TYPE_PPR_PLUGIN = 80880000;
    public const REVIEW_RESPONSE_DUE_DATE_EDITOR_NOTIFICATION = 80880100;
    public const REVIEW_DUE_DATE_EDITOR_NOTIFICATION = 80880200;
    public const REVIEW_DUE_DATE_REVIEWER_NOTIFICATION = 80880400;

    private $notificationDao;
    private $contextId;

    public function __construct($contextId) {
        $this->notificationDao = DAORegistry::getDAO('NotificationDAO');
        $this->contextId = $contextId;
    }

    public function registerReviewDueDateEditorNotification($dueReviewData) {
        return $this->saveNotification($this->calculateReviewDueDateEditorType($dueReviewData) , null, $dueReviewData->getReviewId());
    }

    public function getReviewDueDateEditorNotifications($dueReviewData) {
        $items = $this->getNotifications($this->calculateReviewDueDateEditorType($dueReviewData) , null, $dueReviewData->getReviewId());
        return $items->toArray();
    }

    public function registerReviewDueDateReviewerNotification($reviewerId, $reviewId) {
        return $this->saveNotification(self::REVIEW_DUE_DATE_REVIEWER_NOTIFICATION, $reviewerId, $reviewId);
    }

    public function getReviewDueDateReviewerNotifications($reviewerId, $reviewId) {
        $items = $this->getNotifications(self::REVIEW_DUE_DATE_REVIEWER_NOTIFICATION, $reviewerId, $reviewId);
        return $items->toArray();
    }

    private function calculateReviewDueDateEditorType($dueReviewData) {
        $reviewDueDateType = $dueReviewData->isReviewDueDate() ? self::REVIEW_DUE_DATE_EDITOR_NOTIFICATION : self::REVIEW_RESPONSE_DUE_DATE_EDITOR_NOTIFICATION;
        $type = $reviewDueDateType + abs( $dueReviewData->getDaysFromDueDate());
        if ($dueReviewData->getDaysFromDueDate() < 0) {
            //SET TYPE AS A NEGATIVE NUMBER TO EASILY IDENTIFY IN DATABASE
            $type *=-1;
        }
        return $type;
    }

    private function saveNotification($type, $userId, $objectId) {
        $notification = $this->notificationDao->newDataObject();
        $notification->setUserId((int) $userId);
        $notification->setType(self::NOTIFICATION_TYPE_PPR_PLUGIN);
        $notification->setContextId((int) $this->contextId);
        $notification->setAssocType($type);
        $notification->setAssocId((int) $objectId);
        $notification->setLevel(NOTIFICATION_LEVEL_NORMAL);
        return $this->notificationDao->insertObject($notification);
    }

    private function getNotifications($type, $userId, $objectId) {
        return $this->notificationDao->getByAssoc($type,
            $objectId,
            $userId,
            self::NOTIFICATION_TYPE_PPR_PLUGIN,
            $this->contextId);
    }

}