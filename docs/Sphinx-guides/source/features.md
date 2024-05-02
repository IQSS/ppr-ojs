# Features

As part of the effort to keep track of the scope of this plugin, we have created this page to try to identify all the changes done and categorize them on **Customizations** (*Things that work differently from the standard PKP workflow*) and **New Features**. (*Things that were added as functionality to the system*).

Additionally, we use other plugins and customizations made by other contributors and PKP and those will also be included in this document and flagged as such.


(custom_ppr_plugin)=
## Customizations IQSS Peer Pre-Review Program Plugin

### Core settings

- Add override for file upload component buttons text

### Submissions / Review workflow

- Add a return to the "Review Tab" message in case someone lands on the hidden Copyediting/Production page
- Add the Author/Coauthor information on the Review Tab
- Add the suggested Reviewers Panel on the Review Tab
- Hide the "Require New Review Round" option from the "Send to Author" form
- Hide the option to BCC a reviewer from the "Send to Author" form
- A popup appears when Associate Editors forget to attach a review file for Authors
- Shows the Author's last name in the submission breadcrumbs at the top of the "Review Tab"

### Users / Contributors

- Hide the option for the Author to add a "Preferred Public Name"
- Hide the option to add a "Bio" statement for all user
- Add the ability to show when an Associate Editor goes on leave under the "Assign Participant" page
- Add institutional position, department, and required fields to the registration, user, author, and reviewer forms

### Reviews

- Hides the option to change the type of review and defaults to "Anonymous Reviewer/Anonymous Author"
- Hides the option to choose "None/Free Form Review" when selecting paper instructions when adding a reviewer
- Hides the option for the Reviewer to choose the type of feedback defaults to "Reviewer Completed the Review"
- A popup appears when the Reviewer forgets to attach a review file
- Add reviewer to review attachments component on Sent to Author workflow
- Override the reviewer component: Updates status, keep reviewers in submission and custom email template

### Submissions
- Adds the ability for Author's to provide specific comments for the Review during their submission
- Adds the ability for the Author to choose their research document type (e.g. Paper, Pre-Analysis, etc)
- Hides the ability for Author's to add a prefix to their title during submission
- Adds the ability to close a submission once the review has been completed and sends it to the Archive tab
- Adds the Author's submission checklist to their confirmation page before submitting their document
- Adds the validation pop-up for Author's if they forget to upload their document

### Surveys

- Author submission completed
- Author dashboard
- Reviewers

### Emails

- Add author and editor first and full names to email templates
- Disables the creation of the reviewer registration email
- Send review accepted confirmation email to reviewer
- Send review submitted confirmation email to reviewer
- Add review reminder action email template override based on review being accepted
- Adds the Managing Editor as BCC to "Thank Reviewer" email for payment initiation
- Send submission approved confirmation email to author
- Removes the Coauthors from the submission confirmation email the Author receives
- Add a checkbox to allow authors to choose if contributors should be notified

### Scheduled Tasks

- Send an email to the Associate Editors if they have an Overdue Response/Review
- Automated Reviewer due date reminder for an upcoming review
- Add review sent notification to authors

## Customizations IQSS Peer Pre-Review Reviews Report

### Scheduled Tasks

- Automated submissions/review report

(custom_email_templates)=
## Custom email templates

|Name|Description|
|----|-----------|
|``PPR_REVIEW_DUE_DATE_EDITOR``|Reviewer's Response/Review Overdue|
|``PPR_REVIEW_DUE_DATE_REVIEWER``|Upcoming Review Due|
|``PPR_REVIEW_REQUEST_DUE_DATE_REVIEWER``|Scientific Review Request Reminder|
|``PPR_REQUESTED_REVIEWER_UNASSIGN``|Scientific Review Request Cancellation|
|``PPR_CONFIRMED_REVIEWER_UNASSIGN``|Scientific Review Cancellation|
|``PPR_SUBMISSIONS_REPORT_TASK``|Weekly Report|
|``PPR_SUBMISSION_APPROVED``|Submission|
|``PPR_REVIEW_ACCEPTED``|Scientific Review Request Confirmation|
|``PPR_REVIEW_SUBMITTED``|Confirmation of a Review Upload|
|``PPR_REVIEW_DUE_DATE_WITH_FILES_REVIEWER``|Upcoming Review Due With Documents|
|``PPR_REVIEW_PENDING_WITH_FILES_REVIEWER``|Uploaded Review Without Submission|
|``PPR_REVIEW_SENT_AUTHOR``|Survey To Be Sent to Authors|