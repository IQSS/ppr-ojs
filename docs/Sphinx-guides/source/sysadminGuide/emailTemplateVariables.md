# Email Template Variables
This is the list of backend variables available to the email templates to render dynamic data based in the workflow the email relates to.

These variables can be used in the templates in the form ``{$variable_name}``

## Backend classes
Email variables added by ``FirstNameEmailService``:

authorName, authorFullName, authorFirstName, contributorsNames,
reviewerName, reviewerFullName, reviewerFirstName, firstNameOnly,
editorName, editorFullName, editorFirstName

Email variables added by ``SubmissionMailTemplate``:

submissionTitle, submissionId, submissionAbstract, authorString, principalContactSignature, contextName, contextUrl, senderEmail, senderName, siteTitle

Email Variables added by ``MailTemplate``:

principalContactSignature, contextName, contextUrl, senderEmail, senderName, siteTitle

## Descriptions

| Email Template  | Variables                                                                        |
|-----------------|----------------------------------------------------------------------------------|
| ``NOTIFICATION`` |                                                                                  |
| ``EDITOR_DECISION_INITIAL_DECLINE`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, authorName, submissionUrl |
| ``EDITOR_DECISION_DECLINE`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, authorName, submissionUrl |
| ``EDITOR_DECISION_REVISIONS`` | ``FirstNameEmailService``, ``SubmissionMailTemplate``, authorName, submissionUrl |
| ``SUBMISSION_ACK`` |                                                                                  |
| ``PPR_SUBMISSION_APPROVED`` |                                                                                  |
| ``EDITOR_ASSIGN`` |                                                                                  |
| ``REVIEW_REQUEST_ONECLICK`` |                                                                                  |
| ``REVIEW_CONFIRM`` |                                                                                  |
| ``REVIEW_DECLINE`` |                                                                                  |
| ``PPR_REVIEW_ACCEPTED`` |                                                                                  |
| ``PPR_REVIEW_DUE_DATE_REVIEWER`` |                                                                                  |
| ``PPR_REVIEW_DUE_DATE_EDITOR`` |                                                                                  |
| ``PPR_REVIEW_REQUEST_DUE_DATE_REVIEWER`` |                                                                                  |
| ``REVIEW_REMIND_ONECLICK`` |                                                                                  |
| ``PPR_REQUESTED_REVIEWER_UNASSIGN`` |                                                                                  |
| ``PPR_CONFIRMED_REVIEWER_UNASSIGN`` |                                                                                  |
| ``PPR_REVIEW_SUBMITTED`` |                                                                                  |
| ``REVIEW_ACK`` |                                                                                  |
| ``PPR_SUBMISSIONS_REPORT_TASK`` |                                                                                  |
| ``PPR_REVIEW_SENT_AUTHOR`` |                                                                                  |
| ``PPR_REVIEW_PENDING_WITH_FILES_REVIEWER`` |                                                                                  |
| ``PPR_REVIEW_DUE_DATE_WITH_FILES_REVIEWER`` |                                                                                  |
| ``PPR_SUBMISSION_CLOSED_AUTHOR`` |                                                                                  |
| ``REVIEW_CANCEL`` |                                                                                  |

