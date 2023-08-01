<?php

import('tests.src.PPRTestCase');
import('tasks.PPRTaskNotificationRegistry');
import('tasks.PPRDueReviewData');

import('classes.notification.Notification');
import('lib.pkp.classes.notification.NotificationDAO');
import('lib.pkp.classes.submission.reviewAssignment.ReviewAssignment');

class PPRTaskNotificationRegistryTest extends PPRTestCase {

    const PPR_NOTIFICATION = PPRTaskNotificationRegistry::NOTIFICATION_TYPE_PPR_PLUGIN;

    const CONTEXT_ID = 100;

    private $EXPECTED_NOTIFICATION_ARRAY = [];

    public function setUp(): void {
        parent::setUp();

        $this->EXPECTED_NOTIFICATION_ARRAY = [new Notification()];
    }

    public function test_registerReviewDueDateEditorNotification_with_REVIEW_RESPONSE_DUE_DATE_TYPE_inserts_the_expected_notification() {
        $daysFromDueDateByNotificationType = [
            -80880103 => -3,
            -80880101 => -1,
            80880100 => 0,
            80880101 => 1,
            80880103 => 3
        ];

        $this->assert_registerReviewDueDateEditorNotification(PPRDueReviewData::REVIEW_RESPONSE_DUE_DATE_TYPE, $daysFromDueDateByNotificationType);
    }

    public function test_registerReviewDueDateEditorNotification_with_REVIEW_DUE_DATE_TYPE_inserts_the_expected_notification() {
        $daysFromDueDateByNotificationType = [
            -80880203 => -3,
            -80880201 => -1,
            80880200 => 0,
            80880201 => 1,
            80880203 => 3
        ];

        $this->assert_registerReviewDueDateEditorNotification(PPRDueReviewData::REVIEW_DUE_DATE_TYPE, $daysFromDueDateByNotificationType);
    }

    private function assert_registerReviewDueDateEditorNotification($reviewDataType, $daysFromDueDateByNotificationType) {
        $reviewId = 250;
        $dueDate = Core::getCurrentDate();

        foreach ($daysFromDueDateByNotificationType as $reviewDueDateNotificationType => $daysFromDueDate){
            $this->addCreateNotificationMock($reviewDueDateNotificationType, $reviewId, null);

            $reviewAssignment = new ReviewAssignment();
            $reviewAssignment->_data = ['id' => $reviewId];
            $reviewData = new PPRDueReviewData($reviewDataType, $reviewAssignment, $dueDate, $daysFromDueDate);

            $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);
            $target->registerReviewDueDateEditorNotification($reviewData);
        }
    }

    public function test_getReviewDueDateEditorNotifications_with_REVIEW_RESPONSE_DUE_DATE_TYPE_calls_NotificationDAO() {
        $daysFromDueDateByNotificationType = [
            -80880103 => -3,
            -80880101 => -1,
            80880100 => 0,
            80880101 => 1,
            80880103 => 3
        ];

        $this->assert_getReviewDueDateEditorNotifications(PPRDueReviewData::REVIEW_RESPONSE_DUE_DATE_TYPE, $daysFromDueDateByNotificationType);
    }

    public function test_getReviewDueDateEditorNotifications_with_REVIEW_DUE_DATE_TYPE_calls_NotificationDAO() {
        $daysFromDueDateByNotificationType = [
            -80880203 => -3,
            -80880201 => -1,
            80880200 => 0,
            80880201 => 1,
            80880203 => 3
        ];

        $this->assert_getReviewDueDateEditorNotifications(PPRDueReviewData::REVIEW_DUE_DATE_TYPE, $daysFromDueDateByNotificationType);
    }

    private function assert_getReviewDueDateEditorNotifications($reviewDataType, $daysFromDueDateByNotificationType) {
        $reviewId = 100;
        $dueDate = Core::getCurrentDate();
        foreach ($daysFromDueDateByNotificationType as $reviewDueDateNotificationType => $daysFromDueDate){
            $this->addGetNotificationMock($reviewDueDateNotificationType, $reviewId, null);

            $reviewAssignment = new ReviewAssignment();
            $reviewAssignment->_data = ['id' => $reviewId];
            $reviewData = new PPRDueReviewData($reviewDataType, $reviewAssignment, $dueDate, $daysFromDueDate);

            $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);
            $result = $target->getReviewDueDateEditorNotifications($reviewData);
            $this->assertEquals($this->EXPECTED_NOTIFICATION_ARRAY, $result);
        }
    }

    public function test_registerReviewDueDateReviewerNotification_inserts_the_expected_notification() {
        $reviewDueDateNotificationType = PPRTaskNotificationRegistry::REVIEW_DUE_DATE_REVIEWER_NOTIFICATION;
        $reviewerId = 100;
        $reviewId = 250;

        $this->addCreateNotificationMock($reviewDueDateNotificationType, $reviewId, $reviewerId);

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $target->registerReviewDueDateReviewerNotification($reviewerId, $reviewId);
    }

    public function test_getReviewDueDateReviewerNotifications_calls_NotificationDAO() {
        $reviewDueDateNotificationType = PPRTaskNotificationRegistry::REVIEW_DUE_DATE_REVIEWER_NOTIFICATION;
        $reviewerId = 123;
        $reviewId = 100;
        $this->createMock(NotificationDAO::class);
        $this->addGetNotificationMock($reviewDueDateNotificationType, $reviewId, $reviewerId);

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $result = $target->getReviewDueDateReviewerNotifications($reviewerId, $reviewId);
        $this->assertEquals($this->EXPECTED_NOTIFICATION_ARRAY, $result);
    }

    private function addCreateNotificationMock($reviewDueDateNotificationType, $reviewId, $userId) {
        $notificationDao = $this->createMock(NotificationDAO::class);
        $notificationDao->expects($this->once())->method('newDataObject')->willReturn(new Notification());
        $notificationDao->expects($this->once())
            ->method('insertObject')
            ->with($this->callback(function ($notification) use ($reviewDueDateNotificationType, $reviewId, $userId) {
                return $this->assertNotification($notification,
                    $reviewDueDateNotificationType,
                    $reviewId,
                    $userId);
            }));
        DAORegistry::registerDAO('NotificationDAO', $notificationDao);
        return $notificationDao;
    }

    private function addGetNotificationMock($reviewDueDateNotificationType, $reviewId, $userId) {
        $notificationDao = $this->createMock(NotificationDAO::class);
        $notificationDao->expects($this->once())
            ->method('getByAssoc')
            ->with($reviewDueDateNotificationType, $reviewId, $userId, self::PPR_NOTIFICATION, self::CONTEXT_ID)
            ->willReturn($this->getResultFactoryMock($this->EXPECTED_NOTIFICATION_ARRAY));

        DAORegistry::registerDAO('NotificationDAO', $notificationDao);
        return $notificationDao;
    }

    private function assertNotification($notification, $notificationType, $typeId, $userId) {
        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals($notification->getType(), self::PPR_NOTIFICATION, 'notification getType');
        $this->assertEquals($notification->getContextId(), self::CONTEXT_ID, 'notification getContextId');
        $this->assertEquals($notification->getLevel(), NOTIFICATION_LEVEL_NORMAL, 'notification setLevel');

        $this->assertEquals($notification->getAssocType(), $notificationType, 'notification getAssocType');
        $this->assertEquals($notification->getAssocId(), $typeId, 'notification getAssocId');
        $this->assertEquals($notification->getUserId(), $userId, 'notification getUserId');
        return true;
    }

    private function getResultFactoryMock($dataArray) {
        $resultFactory = $this->createMock(DAOResultFactory::class);
        $resultFactory->method('toArray')
            ->willReturn($dataArray);

        return $resultFactory;
    }
}