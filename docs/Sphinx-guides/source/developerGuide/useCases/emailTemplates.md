# PPR-OJS Email templates

During the workflows of the application, there are several email templates utilized, they are described on this page.
For all email templates [see](custom_email_templates)

## Submission Templates

- The **Author** completes their submission, and the following emails are sent automatically
    - ``SUBMISSION_ACK`` (Author)
    - ``NOTIFICATION`` (Managing Editor)
- Once the **Managing Editor** receives the ``NOTIFICATION`` email, they log into OJS and review the submission.
    - ``EDITOR_DECISION_INITIAL_DECLINE`` (Author) is sent to the author to notify them that their submission is not eligible for PPR
    - ``PPR_SUBMISSION_APPROVED`` (Author) is sent to the author to notify them that their submission is eligible for PPR

## Review Process

- Once the **Associate Editor** receives an assignment email (``EDITOR_ASSIGN``), they should log into the system to assign a Reviewer.
- These emails are sent during the Reviewer process

### Request Review Emails
- ``REVIEW_REQUEST_ONECLICK`` is sent to the Reviewer to request a review
    - ``REVIEW_CONFIRM``: The Reviewer accepted the review
    - ``REVIEW_DECLINE``: The Reviewer declined the review
- ``PPR_REVIEW_REQUEST_DUE_DATE_REVIEWER`` is sent to the Reviewer when they haven’t responded to the request for review. The Associate Editors clicks the "Send Reminder" button next to a Reviewer's name
-``PPR_REQUESTED_REVIEWER_UNASSIGN`` is sent to the Reviewer when they haven’t responded, and it’s time to move on to another Reviewer. The Associate Editor clicks the "Unassign Reviewer" under the Reviewer's name

### Accepted Review Emails
- ``PPR_REVIEW_DUE_DATE_REVIEWER`` is a reminder sent to the Reviewer two days before their review is due
- ``REVIEW_REMIND_ONECLICK`` is sent to the Reviewer when they have missed their review due date. The Associate Editors clicks the "Send Reminder" button next to a Reviewer's name
- ``PPR_CONFIRMED_REVIEWER_UNASSIGN`` is sent to the Reviewer when they missed their review due date and it’s time to move on to another Reviewer. The Associate Editor clicks the "Cancel Reviewer” under the Reviewer's name
- ``PPR_REVIEW_SUBMITTED`` is sent to the reviewer once they upload and click the "Submit Review" blue button
- ``REVIEW_ACK`` is sent to the Reviewer once the review is completed letting them know someone will get in touch for payment. The Associate Editor clicks the "Thank Reviewer" button next to the Reviewers name

### This email goes out once a review is late.
- ``PPR_REVIEW_DUE_DATE_WITH_FILES_REVIEWER`` is sent to the reviewer once they upload the review to the system, but don’t click “submit” to finalize the review before it is due
- ``PPR_REVIEW_PENDING_WITH_FILES_REVIEWER`` is sent to the reviewer once they upload the review to the system, but don’t click “submit” to finalize the review after it is due
- ``PPR_REVIEW_DUE_DATE_EDITOR`` is sent when a review or response is overdue to let the Associate Editor know they need to login and check on their review

### This email goes out once a review has been completed and the Associate Editor sends it back to the Author.
- ``EDITOR_DECISION_REVISION`` is sent to the Author with their review attached. The Associate Editor clicks the "Send Review to Author" button under the submission

## Survey Process

PPR values the users’ experience using OJS. There are two types of survey: one is to see how users (both authors and reviewers) like OJS submission system, the other is to see how the authors benefit from the reviewers.

- The system will automatically send a survey to the author asking them how convenient they use OJS to make a submission. Noted that only a first-time author will receive the survey. With that said, if the author makes multiple submissions in the future, they won’t receive the survey. Anymore.
- The system will automatically send a survey to the reviewer asking them how convenient they use OJS to make a review.
- ``PPR_REVIEW_SENT_AUTHOR`` is sent to the authors a week after they receive the review to see how they benefit from the reviewer.