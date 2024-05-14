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
        $reviewId = $this->getRandomId();
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
        $reviewId = $this->getRandomId();
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
        $reviewerId = $this->getRandomId();
        $reviewId = $this->getRandomId();
        $this->addCreateNotificationMock($reviewDueDateNotificationType, $reviewId, $reviewerId);

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $target->registerReviewDueDateReviewerNotification($reviewerId, $reviewId);
    }

    public function test_getReviewDueDateReviewerNotifications_calls_NotificationDAO() {
        $reviewDueDateNotificationType = PPRTaskNotificationRegistry::REVIEW_DUE_DATE_REVIEWER_NOTIFICATION;
        $reviewerId = $this->getRandomId();
        $reviewId = $this->getRandomId();
        $this->addGetNotificationMock($reviewDueDateNotificationType, $reviewId, $reviewerId);

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $result = $target->getReviewDueDateReviewerNotifications($reviewerId, $reviewId);
        $this->assertEquals($this->EXPECTED_NOTIFICATION_ARRAY, $result);
    }

    public function test_registerReviewDueDateWithFilesReviewerNotification_inserts_the_expected_notification() {
        $reviewDueDateNotificationType = PPRTaskNotificationRegistry::REVIEW_DUE_DATE_WITH_FILES_REVIEWER_NOTIFICATION;
        $reviewerId = $this->getRandomId();
        $reviewId = $this->getRandomId();
        $this->addCreateNotificationMock($reviewDueDateNotificationType, $reviewId, $reviewerId);

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $target->registerReviewDueDateWithFilesReviewerNotification($reviewerId, $reviewId);
    }

    public function test_getReviewDueDateWithFilesReviewerNotifications_calls_NotificationDAO() {
        $reviewDueDateNotificationType = PPRTaskNotificationRegistry::REVIEW_DUE_DATE_WITH_FILES_REVIEWER_NOTIFICATION;
        $reviewerId = $this->getRandomId();
        $reviewId = $this->getRandomId();
        $this->addGetNotificationMock($reviewDueDateNotificationType, $reviewId, $reviewerId);

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $result = $target->getReviewDueDateWithFilesReviewerNotifications($reviewerId, $reviewId);
        $this->assertEquals($this->EXPECTED_NOTIFICATION_ARRAY, $result);
    }

    public function test_registerReviewPendingWithFilesReviewerNotification_inserts_the_expected_notification() {
        $reviewDueDateNotificationType = PPRTaskNotificationRegistry::REVIEW_PENDING_WITH_FILES_REVIEWER_NOTIFICATION;
        $reviewerId = $this->getRandomId();
        $reviewId = $this->getRandomId();
        $this->addCreateNotificationMock($reviewDueDateNotificationType, $reviewId, $reviewerId);

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $target->registerReviewPendingWithFilesReviewerNotification($reviewerId, $reviewId);
    }

    public function test_getReviewPendingWithFilesReviewerNotifications_calls_NotificationDAO() {
        $reviewDueDateNotificationType = PPRTaskNotificationRegistry::REVIEW_PENDING_WITH_FILES_REVIEWER_NOTIFICATION;
        $reviewerId = $this->getRandomId();
        $reviewId = $this->getRandomId();
        $this->addGetNotificationMock($reviewDueDateNotificationType, $reviewId, $reviewerId);

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $result = $target->getReviewPendingWithFilesReviewerNotifications($reviewerId, $reviewId);
        $this->assertEquals($this->EXPECTED_NOTIFICATION_ARRAY, $result);
    }

    public function test_registerReviewSentAuthorNotification_inserts_the_expected_notification() {
        $reviewDueDateNotificationType = PPRTaskNotificationRegistry::REVIEW_SENT_AUTHOR_NOTIFICATION;
        $reviewerId = $this->getRandomId();
        $reviewId = $this->getRandomId();
        $this->addCreateNotificationMock($reviewDueDateNotificationType, $reviewId, $reviewerId);

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $target->registerReviewSentAuthorNotification($reviewerId, $reviewId);
    }

    public function test_getReviewSentAuthorNotifications_calls_NotificationDAO() {
        $reviewDueDateNotificationType = PPRTaskNotificationRegistry::REVIEW_SENT_AUTHOR_NOTIFICATION;
        $reviewerId = $this->getRandomId();
        $reviewId = $this->getRandomId();
        $this->addGetNotificationMock($reviewDueDateNotificationType, $reviewId, $reviewerId);

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $result = $target->getReviewSentAuthorNotifications($reviewerId, $reviewId);
        $this->assertEquals($this->EXPECTED_NOTIFICATION_ARRAY, $result);
    }

    public function test_registerSubmissionSurveyForAuthor_inserts_the_expected_notification() {
        $reviewDueDateNotificationType = PPRTaskNotificationRegistry::SUBMISSION_AUTHOR_SURVEY;
        $userId = $this->getRandomId();
        $this->addCreateNotificationMock($reviewDueDateNotificationType, $userId, $userId);

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $target->registerSubmissionSurveyForAuthor($userId);
    }

    public function test_getSubmissionSurveyForAuthor_calls_NotificationDAO() {
        $reviewDueDateNotificationType = PPRTaskNotificationRegistry::SUBMISSION_AUTHOR_SURVEY;
        $userId = $this->getRandomId();
        $this->addGetNotificationMock($reviewDueDateNotificationType, $userId, $userId);

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $result = $target->getSubmissionSurveyForAuthor($userId);
        $this->assertEquals($this->EXPECTED_NOTIFICATION_ARRAY, $result);
    }

    public function test_registerSubmissionClosedAuthorNotification_inserts_the_expected_notification() {
        $reviewDueDateNotificationType = PPRTaskNotificationRegistry::SUBMISSION_CLOSED_AUTHOR_NOTIFICATION;
        $userId = $this->getRandomId();
        $this->addCreateNotificationMock($reviewDueDateNotificationType, $userId, $userId);

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $target->registerSubmissionClosedAuthorNotification($userId);
    }

    public function test_getSubmissionClosedAuthorNotification_calls_NotificationDAO() {
        $reviewDueDateNotificationType = PPRTaskNotificationRegistry::SUBMISSION_CLOSED_AUTHOR_NOTIFICATION;
        $userId = $this->getRandomId();
        $this->addGetNotificationMock($reviewDueDateNotificationType, $userId, $userId);

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $result = $target->getSubmissionClosedAuthorNotification($userId);
        $this->assertEquals($this->EXPECTED_NOTIFICATION_ARRAY, $result);
    }

    public function test_updateDateRead_calls_NotificationDAO() {
        $notificationId = $this->getRandomId();
        $notificationDao = $this->createMock(NotificationDAO::class);
        DAORegistry::registerDAO('NotificationDAO', $notificationDao);
        $notificationDao->expects($this->once())->method('setDateRead')->with($notificationId, $this->anything());

        $target = new PPRTaskNotificationRegistry(self::CONTEXT_ID);

        $target->updateDateRead($notificationId);
    }

    private function addCreateNotificationMock($reviewDueDateNotificationType, $objectId, $userId) {
        $notificationDao = $this->createMock(NotificationDAO::class);
        $notificationDao->expects($this->once())->method('newDataObject')->willReturn(new Notification());
        $notificationDao->expects($this->once())
            ->method('insertObject')
            ->with($this->callback(function ($notification) use ($reviewDueDateNotificationType, $objectId, $userId) {
                return $this->assertNotification($notification,
                    $reviewDueDateNotificationType,
                    $objectId,
                    $userId);
            }));
        DAORegistry::registerDAO('NotificationDAO', $notificationDao);
        return $notificationDao;
    }

    private function addGetNotificationMock($reviewDueDateNotificationType, $objectId, $userId) {
        $notificationDao = $this->createMock(NotificationDAO::class);
        $notificationDao->expects($this->once())
            ->method('getByAssoc')
            ->with($reviewDueDateNotificationType, $objectId, $userId, self::PPR_NOTIFICATION, self::CONTEXT_ID)
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