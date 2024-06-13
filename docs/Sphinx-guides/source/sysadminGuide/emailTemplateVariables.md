# Email Template Variables
This is the list of backend variables available to the email templates to render dynamic data based in the workflow the email relates to.

These variables can be used in the templates in the form ``{$variable_name}``

## Backend classes
Email variables added by ``FirstNameEmailService``:
 * authorName, authorFullName, authorFirstName, contributorsNames
 * reviewerName, reviewerFullName, reviewerFirstName, firstNameOnly
 * editorName, editorFullName, editorFirstName

Email variables added by ``SubmissionMailTemplate``:
 * submissionTitle, submissionId, submissionAbstract
 * authorString, principalContactSignature
 * contextName, contextUrl
 * senderEmail, senderName
 * siteTitle

Email Variables added by ``MailTemplate``:
 * principalContactSignature
 * contextName, contextUrl
 * senderEmail, senderName
 * siteTitle

## Descriptions

| Email Template  | Variables                                                                                                                                                                                                           |
|-----------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| ``NOTIFICATION`` | ``MailTemplate``, notificationContents, url, siteTitle                                                                                                                                                              |
| ``EDITOR_DECISION_INITIAL_DECLINE`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, authorName, submissionUrl                                                                                                                                    |
| ``EDITOR_DECISION_DECLINE`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, authorName, submissionUrl                                                                                                                                    |
| ``EDITOR_DECISION_REVISIONS`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, authorName, submissionUrl                                                                                                                                    |
| ``SUBMISSION_ACK`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, authorName, authorUsername, editorialContactSignature, submissionUrl                                                                                         |
| ``PPR_SUBMISSION_APPROVED`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, submissionUrl                                                                                                                                                |
| ``EDITOR_ASSIGN`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``                                                                                                                                                               |
| ``REVIEW_REQUEST_ONECLICK`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, contextUrl, editorialContactSignature, signatureFullName, passwordResetUrl, messageToReviewer, abstractTermIfEnabled                                         |
| ``REVIEW_CONFIRM`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, reviewDueDate                                                                                                                                                |
| ``REVIEW_DECLINE`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, reviewDueDate                                                                                                                                                |
| ``PPR_REVIEW_ACCEPTED`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, reviewerUserName, reviewDueDate, passwordResetUrl, submissionReviewUrl                                                                                       |
| ``PPR_REVIEW_DUE_DATE_REVIEWER`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, reviewerUserName, reviewDueDate, responseDueDate, editorialContactSignature, passwordResetUrl, submissionReviewUrl, messageToReviewer, abstractTermIfEnabled |
| ``PPR_REVIEW_PENDING_WITH_FILES_REVIEWER`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, reviewerUserName, reviewDueDate, responseDueDate, editorialContactSignature, passwordResetUrl, submissionReviewUrl, messageToReviewer, abstractTermIfEnabled |
| ``PPR_REVIEW_DUE_DATE_WITH_FILES_REVIEWER`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, reviewerUserName, reviewDueDate, responseDueDate, editorialContactSignature, passwordResetUrl, submissionReviewUrl, messageToReviewer, abstractTermIfEnabled |
| ``PPR_REVIEW_DUE_DATE_EDITOR`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, reviewDueDate, editorialContactSignature, submissionReviewUrl                                                                                                |
| ``PPR_REVIEW_REQUEST_DUE_DATE_REVIEWER`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, reviewDueDate, passwordResetUrl, submissionReviewUrl, editorialContactSignature                                                                              |
| ``REVIEW_REMIND_ONECLICK`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, reviewDueDate, passwordResetUrl, submissionReviewUrl, editorialContactSignature                                                                              |
| ``REVIEW_REMIND`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, reviewDueDate, passwordResetUrl, submissionReviewUrl, editorialContactSignature                                                                              |
| ``PPR_REQUESTED_REVIEWER_UNASSIGN`` | reviewerName, reviewerFirstName, editorName, editorFirstName, authorName, authorFirstName                                                                                                                           |
| ``PPR_CONFIRMED_REVIEWER_UNASSIGN`` | reviewerName, reviewerFirstName, editorName, editorFirstName, authorName, authorFirstName                                                                                                                           |
| ``PPR_REVIEW_SUBMITTED`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, reviewerUserName, reviewDueDate                                                                                                                              |
| ``REVIEW_ACK`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, contextUrl, editorialContactSignature, signatureFullName                                                                                                     |
| ``PPR_REVIEW_SENT_AUTHOR`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, editorialContactSignature, submissionUrl                                                                                                                     |
| ``PPR_SUBMISSION_CLOSED_AUTHOR`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, editorialContactSignature, submissionUrl                                                                                                                     |
| ``REVIEW_CANCEL`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, signatureFullName                                                                                                                                            |
| ``PPR_SUBMISSIONS_REPORT_TASK`` | No variables are added                                                                                                                                                                                              |

