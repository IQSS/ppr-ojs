<?php

import('tests.src.PPRTestCase');
import('services.reviewer.PPRReviewerGridCellProvider');

import('lib.pkp.controllers.grid.users.reviewer.ReviewerGridRow');

class PPRReviewerGridCellProviderTest extends PPRTestCase {

    const IS_CURRENT_USER_ASSIGNED_AUTHOR = false;

    public function test_getStatusText_should_return_declined_text_with_date_for_REVIEW_ASSIGNMENT_STATUS_DECLINED() {
        $row = $this->createRow('2024-01-01 12:10:10');
        $target = new PPRReviewerGridCellProvider(self::IS_CURRENT_USER_ASSIGNED_AUTHOR);
        $result = $target->_getStatusText(REVIEW_ASSIGNMENT_STATUS_DECLINED, $row);

        $this->assertNotNull($result);
        $this->assertStringContainsString('class="state declined"', $result);
        $this->assertStringContainsString('Request Declined', $result);
        $this->assertStringContainsString('Declined date: 2024-01-01', $result);
    }

    public function test_getStatusText_should_handle_null_confirmedDate_for_REVIEW_ASSIGNMENT_STATUS_DECLINED() {
        $row = $this->createRow(null);
        $target = new PPRReviewerGridCellProvider(self::IS_CURRENT_USER_ASSIGNED_AUTHOR);
        $result = $target->_getStatusText(REVIEW_ASSIGNMENT_STATUS_DECLINED, $row);

        $this->assertNotNull($result);
        $this->assertStringContainsString('class="state declined"', $result);
        $this->assertStringContainsString('Request Declined', $result);
        $this->assertStringContainsString('Declined date: N/A', $result);
    }

    public function test_getStatusText_should_return_expected_text_for_all_statuses() {
        $target = new PPRReviewerGridCellProvider(self::IS_CURRENT_USER_ASSIGNED_AUTHOR);
        $expectations = [
            REVIEW_ASSIGNMENT_STATUS_AWAITING_RESPONSE => ['class="state"', 'class="details"', 'Request Sent'],
            REVIEW_ASSIGNMENT_STATUS_ACCEPTED => ['class="state"', 'class="details"', 'Request Accepted'],
            REVIEW_ASSIGNMENT_STATUS_COMPLETE => ['class="state"', 'class="details"', 'Complete'],
            REVIEW_ASSIGNMENT_STATUS_REVIEW_OVERDUE => ['class="state overdue"', 'class="details"', 'Overdue'],
            REVIEW_ASSIGNMENT_STATUS_RESPONSE_OVERDUE => ['class="state overdue"', 'class="details"', 'Overdue'],
            REVIEW_ASSIGNMENT_STATUS_CANCELLED => ['class="state declined"', 'Request Cancelled'],
            REVIEW_ASSIGNMENT_STATUS_RECEIVED => ['class="state"', 'class="details"', 'Review Submitted'],
            REVIEW_ASSIGNMENT_STATUS_THANKED => ['class="state"', 'class="details"', 'Reviewer Thanked'],
        ];

        foreach ($expectations as $status => $expectedTextList) {
            $row = $this->createRow(null);
            $result = $target->_getStatusText($status, $row);
            $this->assertNotNull($result);
            foreach ($expectedTextList as $expectedText) {
                $this->assertStringContainsString($expectedText, $result);
            }
        }
    }

    public function test_getStatusText_should_return_empty_string_for_unknown_status() {
        $target = new PPRReviewerGridCellProvider(self::IS_CURRENT_USER_ASSIGNED_AUTHOR);
        $row = $this->createRow(null);
        $unknownStatus = 9999;
        $knownStatuses = [
            REVIEW_ASSIGNMENT_STATUS_AWAITING_RESPONSE,
            REVIEW_ASSIGNMENT_STATUS_ACCEPTED,
            REVIEW_ASSIGNMENT_STATUS_COMPLETE,
            REVIEW_ASSIGNMENT_STATUS_REVIEW_OVERDUE,
            REVIEW_ASSIGNMENT_STATUS_RESPONSE_OVERDUE,
            REVIEW_ASSIGNMENT_STATUS_DECLINED,
            REVIEW_ASSIGNMENT_STATUS_CANCELLED,
            REVIEW_ASSIGNMENT_STATUS_RECEIVED,
            REVIEW_ASSIGNMENT_STATUS_THANKED,
        ];
        $this->assertNotContains($unknownStatus, $knownStatuses);
        $result = $target->_getStatusText($unknownStatus, $row);
        $this->assertEquals('', $result);
    }

    private function createRow($dateConfirmed) {
        $all_dates = '2024-12-31 00:00:00';
        $row = $this->createMock(ReviewerGridRow::class);
        $reviewerId = $this->getRandomId();
        $reviewAssignment = $this->getTestUtil()->createReview($reviewerId);
        $reviewAssignment->method('getRecommendation')->willReturn('recommendation');
        $reviewAssignment->method('getLocalizedRecommendation')->willReturn('recommendation');
        $reviewAssignment->method('getDateConfirmed')->willReturn($dateConfirmed);
        $reviewAssignment->method('getDateResponseDue')->willReturn($all_dates);
        $reviewAssignment->method('getDateDue')->willReturn($all_dates);

        $row->expects($this->once())->method('getData')->willReturn($reviewAssignment);
        return $row;
    }
}