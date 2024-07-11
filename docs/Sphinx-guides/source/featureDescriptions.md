# Feature Descriptions

## Technical Notes
All template overrides are implemented in the Service class: `pprOjsPlugin/services/PPRWorkflowService.inc.php`

## Descriptions

**Issue Id:** Issue 07 <br>
**Area:** Submission <br>
**Title:** Hide the Homepage URL field <br>
**User Story:** We do not need users' personal Homepage URLs. Removing this field will simplify the interface, reduce clutter, and enhance the user experience. <br>
**Toggle:** N/A <br>
**Implementation:** This was implemented by PKP directly. This was implemented with CSS, there is a copy of the CSS code at the end of our CSS file for reference: `pprOjsPlugin/css/iqss.css`

**Issue Id:** Issue 08 <br>
**Area:** Submission <br>
**Title:** Hide the field ORIC iD <br>
**User Story:** We do not need users' ORIC iD. Removing this field will simplify the interface, reduce clutter, and enhance the user experience. <br>
**Toggle:** N/A <br>
**Implementation:** This was implemented by PKP directly. This was implemented with CSS, there is a copy of the CSS code at the end of our CSS file for reference: `pprOjsPlugin/css/iqss.css`

**Issue Id:** Issue 09 <br>
**Area:** Submission <br>
**Title:** Hide Preferred Public Name <br>
**User Story:** We do not need users' Preferred Public Name. Removing this field will simplify the interface, reduce clutter, and enhance the user experience. <br>
**Toggle:** hidePreferredPublicNameEnabled <br>
**Implementation:** Implemented with template override: `pprOjsPlugin/templates/common/userDetails.tpl`

**Issue Id:** Issue 010 <br>
**Area:** Submission <br>
**Title:** Remove the "Include this contributor in the browse list" Option <br>
**User Story:** As a user, searching for contributors on this platform is unnecessary, as its primary function is peer review rather than journal publication. Including the "Include this contributor in the browse list" option could confuse authors when submitting their papers. <br>
**Toggle:** N/A <br>
**Implementation:** This was implemented by PKP directly. This was implemented with CSS, there is a copy of the CSS code at the end of our CSS file for reference: `pprOjsPlugin/css/iqss.css`

**Issue Id:** Issue 013 <br>
**Area:** Workflow <br>
**Title:** Hide the Copyediting Tab <br>
**User Story:** As a user, I expect the system to offer peer-review functionality without the option to publish articles exclusively. The presence of a Copyediting tab could lead to confusion among users. <br>
**Toggle:** N/A - Custom IQSS CSS is always included to OJS. <br>
**Implementation:** Implemented with custom CSS, added selector to hide the Copyediting tab: `pprOjsPlugin/css/iqss.css`

**Issue Id:** Issue 019 <br>
**Area:** Registration <br>
**Title:** Hide Checkboxes on the Registration Page <br>
**User Story:** As an author registering on our platform, the registration page currently includes three checkboxes: A privacy statement, which is irrelevant as we don't have one; an option to receive notifications of new publications, even though we don't publish on our platform; registration is solely for peer review; and an option to be contacted for submission reviews, which we don't allow, as the author provides a list of potential reviewers. Considering these factors, including these checkboxes during registration is unnecessary and potentially confusing. Therefore, we should hide them to streamline registration and avoid user confusion. <br>
**Toggle:** N/A <br>
**Implementation:** This was implemented by PKP directly. This was implemented with CSS, there is a copy of the CSS code at the end of our CSS file for reference: `pprOjsPlugin/css/iqss.css`

**Issue Id:** Issue 020 <br>
**Area:** Registration <br>
**Title:** Registration Process with Eligibility Confirmation Checkbox <br>
**User Story:** As an author registering for an account, I want to ensure that I have read and understood the program requirements to determine my eligibility before creating an account. Our registration page needs a clear indication for authors to confirm their review of eligibility requirements. To enhance user experience and ensure compliance with our platform's guidelines, we propose adding a checkbox prompting authors to acknowledge their review of eligibility criteria before proceeding with registration. <br>
**Toggle:** userCustomFieldsEnabled <br>
**Implementation:** Implemented with custom CSS and the `userRegister` template override: `pprOjsPlugin/templates/frontend/pages/userRegister.tpl`

**Issue Id:** Issue 022 <br>
**Area:** Submission <br>
**Title:** Streamlining Submission Process with Checklist Integration <br>
**User Story:** As an author preparing to submit a research document, I want to double-check that I've included everything from the list on the "1. Start" tab in the submission process, ensuring the completeness and accuracy of my submission before finalizing it in the system.

To enhance the submission process and ensure completeness and accuracy, we propose integrating the checklist from the "1. Start" tab into the confirmation page on the "4. Confirmation" tab. This integration will allow authors to double-check that they've included everything from the initial checklist before finalizing their submission in the system. <br>
**Toggle:** submissionConfirmationChecklistEnabled <br>
**Implementation:** Implemented with template overrides: `pprOjsPlugin/templates/submission/form/step[1,4].tpl`

**Issue Id:** Issue 023 <br>
**Area:** Submission <br>
**Title:** Separate Fields for Institutional Position and Academic Department <br>
**User Story:** As a Managing editor, I need to understand who the author is to check for eligibility. I need to know about their institutional position and academic department. This will also help with reporting metrics.

To improve program metrics reporting, assigning associate editors, and checking eligibility, we propose implementing two separate fields: Institutional Position and Academic Department. These will be included across three key areas: the user account profile, the contributor profile, and the registration profile. <br>
**Toggle:** userCustomFieldsEnabled <br>
**Implementation:** This was implemented with the `PPRUserCustomFieldsService` service: `pprOjsPlugin/services/PPRUserCustomFieldsService.inc.php`

**Issue Id:** Issue 024 <br>
**Area:** Reviewer Process <br>
**Title:** Single Option for Type of Review for Double-Blinded Review Process <br>
**User Story:** As an Associate Editor, I require only one option for the type of review in our system, as our program strictly adheres to a double-blinded review process. The presence of multiple options poses a risk of accidentally disclosing the author's identity to the reviewer, which goes against our process guidelines. To ensure the integrity of our double-blinded review process, I propose simplifying the "Type of Review" options to include only "Anonymous Reviewer/Anonymous Author," removing the possibilities for "Anonymous Reviewer/Disclosed Author" and "Open." By streamlining the options, we can eliminate confusion and maintain the confidentiality crucial to our review process. <br>
**Toggle:** hideReviewMethodEnabled <br>
**Implementation:** Implemented with template override: `pprOjsPlugin/templates/controllers/grid/users/reviewer/form/reviewerFormFooter.tpl`

**Issue Id:** Issue 026 <br>
**Area:** Workflow <br>
**Title:** Hide Publications Tab <br>
**User Story:** As a user, I expect the system to offer peer-review functionality without the option to publish articles exclusively. The presence of a Publications tab could lead to confusion among users. <br>
**Toggle:** N/A <br>
**Implementation:** Not sure what this issue is.

**Issue Id:** Issue 033 <br>
**Area:** Submission <br>
**Title:** Hide the "Accept and Skip Review" Button <br>
**User Story:** As a managing editor, I ensure all submissions undergo peer review before acceptance. Removing the "Accept and Skip Review" option mitigates the risk of accidentally bypassing this crucial step. <br>
**Toggle:** N/A - Custom IQSS CSS is always included to OJS. <br>
**Implementation:** Implemented with custom CSS: `pprOjsPlugin/css/iqss.css`

**Issue Id:** Issue 036 <br>
**Area:** Review Process <br>
**Title:** Managing/Associate Editors Approval Process with Author's Reviewer List Visibility <br>
**User Story:** As a managing editor, I require visibility of the Author's "recommended reviewers" list to ensure all necessary information is included in the submission before proceeding to the "Send for Review" stage. <br>
**Toggle:** displaySuggestedReviewersEnabled <br>
**Implementation:** Implemented with the `PPRWorkflowService` service: `pprOjsPlugin/services/PPRWorkflowService.inc.php`

**Issue Id:** Issue 037 <br>
**Area:** Submission <br>
**Title:** Dedicated Field for Authors to Direct Reviewer Attention <br>
**User Story:** As an Author, I need the capability to direct the Reviewer's attention to specific areas of my paper, facilitating a focused review process. This information should be visible to the Associate Editor responsible for assigning reviewers and any reviewers evaluating my paper.

To address this need, we propose implementing a dedicated field titled "If you'd like to direct the reviewer's attention to any particular area of the paper, please describe it here." This field will seamlessly integrate into the Reviewer's workflow under the "3. Download & Review" tab. Additionally, it will be prominently displayed on the "Activity --> Review" tab for Associate/Managing Editors and Authors, ensuring clear communication and enhancing the review process for all stakeholders. <br>
**Toggle:** submissionCommentsForReviewerEnabled <br>
**Implementation:** This is implemented using the `PPRSubmissionCommentsForReviewerService` to add the new custom field, a template override needed to display the new form field, and the `PPRWorkflowService` to add the field to the workflow page.
 - PPRSubmissionCommentsForReviewerService service: `pprOjsPlugin/services/submission/PPRSubmissionCommentsForReviewerService.inc.php`
 - PPRWorkflowService service: `pprOjsPlugin/services/PPRWorkflowService.inc.php`

**Issue Id:** Issue 045 <br>
**Area:** Reports <br>
**Title:** Customized IQSS Report for Program Metrics Tracking <br>
**User Story:** As a managing editor who runs the program, I need a report to show the process of different areas in the system to keep track of how the program is doing, which can be exported to Smartsheet for a dashboard. This feature entails the development of a customized report to track various metrics within the system, including the number of reviews completed, distinct authors, coauthors, published authors, published papers, submissions, review length, total papers (unique titles), reviewers' review time, and reviewers' response time. <br>
**Toggle:** N/A <br>
**Implementation:** This is implemented with the `pprReviewsReportPlugin` code. This report is available throw the OJS UI.

**Issue Id:** Issue 050 <br>
**Area:** Review Process <br>
**Title:** Author's Institution Visibility on the Review Tab <br>
**User Story:** As a Managing Editor or Associate Editor, I need access to the author institution details on the review tab. This visibility will enable me to confirm that authors and assigned reviewers represent separate institutions. <br>
**Toggle:** displayContributorsEnabled <br>
**Implementation:** The OJS author.affiliation field is being re-purpose as institution. These changes display the institution field in the Contributors component.
 - PPRWorkflowService service: `pprOjsPlugin/services/PPRWorkflowService.inc.php`
 - PPRAuthorGridHandler service: `pprOjsPlugin/services/PPRAuthorGridHandler.inc.php`
 - PPRAuthorGridCellProvider service: `pprOjsPlugin/services/PPRAuthorGridCellProvider.inc.php`

**Issue Id:** Issue 052 <br>
**Area:** Review Process <br>
**Title:** Author Coauthor Visibility on Review Tab <br>
**User Story:** As a Managing Editor or Associate Editor, I need access to the coauthor institution details on the review tab. This visibility will enable me to confirm that authors and assigned reviewers represent separate institutions. <br>
**Toggle:** displayContributorsEnabled <br>
**Implementation:** This issue is to add the contributors component to the workflow pages, submissions and reviews.
 - PPRWorkflowService service: `pprOjsPlugin/services/PPRWorkflowService.inc.php`

**Issue Id:** Issue 055 <br>
**Area:** Publication Tab <br>
**Title:** Hide Fields on the "Submission Details" Tab <br>
**User Story:** As a user, it can be confusing to see certain fields since we operate differently from a typical journal; we solely conduct peer reviews. Therefore, on the "Submission Details (Old Name Publication)" tab, we need to hide the following sections: Galleys, Permissions & Disclosures, and Issue. <br>
**Toggle:** N/A - Custom IQSS CSS is always included to OJS. <br>
**Implementation:** Implemented with CSS: `pprOjsPlugin/css/iqss.css`

**Issue Id:** Issue 056 <br>
**Area:** Profile <br>
**Title:** Hide the "Reviewing Interests" field on the User Profile <br>
**User Story:** As a user, I don't want to be asked about my reviewing interests because the authors and associate editors decide who to contact for reviews. Our platform differs from a regular journal, so this feature is unnecessary. <br>
**Toggle:** userCustomFieldsEnabled (bundled with other changes) <br>
**Implementation:** Implemented with template override: `pprOjsPlugin/templates/controllers/grid/users/reviewer/form/createReviewerForm.tpl`

"Reviewing Interests" is in other views, but only implemented in the create reviewer form.

**Issue Id:** Issue 057 <br>
**Area:** Review Process <br>
**Title:** Automated Past Due Notifications for Associate Editors <br>
**User Story:** As an Associate Editor, I must be notified promptly when a reviewer fails to submit their review or respond to a review request on time. Since I may only sometimes be actively using the system, receiving automated email notifications about overdue reviews or responses is essential to stay informed and take appropriate action. These notifications enable me to follow up with reviewers, mitigate delays, and ensure the timely completion of the review process. <br>
**Toggle:** reviewReminderEditorTaskEnabled <br>
**Implementation:** This is implemented using the `PPRReviewDueDateEditorNotification` scheduled task: `pprOjsPlugin/tasks/PPRReviewDueDateEditorNotification.inc.php`

The schedule for the task is defined: `pprOjsPlugin/scheduledTasks.xml`

**Issue Id:** Issue 058 <br>
**Area:** Review Process <br>
**Title:** Automated Reviewer Due Notification <br>
**User Story:** As a reviewer, I appreciate receiving timely reminders about upcoming review deadlines. These reminders help me stay organized and ensure that I can allocate sufficient time to complete my reviews before they are due. This feature introduces automated notifications to remind reviewers about upcoming review due dates. Reviewers will receive these notifications, the timing of which can be updated in days via the plugin. <br>
**Toggle:** reviewReminderReviewerTaskEnabled <br>
**Implementation:** This is implemented using the `PPRReviewReminder` scheduled task: `pprOjsPlugin/tasks/PPRReviewReminder.inc.php`

The schedule for the task is defined: `pprOjsPlugin/scheduledTasks.xml`

**Issue Id:** Issue 060 <br>
**Area:** Review Process <br>
**Title:** Closed Review Stage <br>
**User Story:** As a user, I must distinguish between completed and ongoing reviews to streamline organization and enhance workflow efficiency. Regardless of completion status, all reviews are housed within the "My Queue" section, which confuses identifying finalized tasks. Implementing a "Closed Submission" button is necessary to address this issue. Upon activation by the Managing Editor within their workflow, this feature will seamlessly relocate completed reviews to the dedicated "Archives" section. <br>
**Toggle:** submissionCloseEnabled <br>
**Implementation:** This is implemented using the `PPRSubmissionActionsService` service: `pprOjsPlugin/services/submission/PPRSubmissionActionsService.inc.php`

**Issue Id:** Issue 061 <br>
**Area:** Review Process <br>
**Title:** Adding a Confirmation Popup for Missing Attachments in Reviewer's Process <br>
**User Story:** As an Associate Editor, I want to receive a reminder to ensure I have added the necessary review attachment before sending the paper to the Author. This feature adds a popup box when an Associate Editor clicks the "Request Revision" button to send a paper to the Author without a review attachment. If an attachment hasn't been selected under the "Select review files to share with the author(s)" section, the popup will display a message asking if the user would like to proceed without an attachment. The popup will offer two options: "Yes" to send the email without an attachment or "Add Attachment" to exit the popup and select a file. This enhancement ensures that Associate Editors are reminded to include the necessary attachments before sending the review to the Author, preventing missed reviews in emails or dashboards. <br>
**Toggle:** submissionRequestRevisionsFileValidationEnabled <br>
**Implementation:** Implemented with custom Javascript and the `sendReviewsForm` template override: `pprOjsPlugin/templates/controllers/modals/editorDecision/form/sendReviewsForm.tpl`

**Issue Id:** Issue 062 <br>
**Area:** Notifications <br>
**Title:** Vacation Status Label Under "Edit Profile" <br>
**User Story:** As a Manager Editor, it's essential for me to be aware of Associate Editors' availability to avoid assigning reviews when they are on vacation. With the introduction of the vacation status indicator, I can quickly identify when an AE is unavailable for assignments. This feature can be enables on the "Edit Profile" page. <br>
**Toggle:** userOnLeaveEnabled <br>
**Implementation:** Implemented with the `PPROnLeaveCustomFieldsService` service:  `pprOjsPlugin/services/PPROnLeaveCustomFieldsService.inc.php`

**Issue Id:** Issue 063 <br>
**Area:** Submission <br>
**Title:** Author's Popup Reminder to Attach Research Document <br>
**User Story:** As an author, I want to receive a reminder to attach my research document when submitting my paper. If I proceed without attaching a document, I'd like to be prompted with a popup reminder. This enhancement ensures that attaching a research document is mandatory and doesn't allow the author to advance in the submission process without uploading the required file. <br>
**Toggle:** submissionUploadFileValidationEnabled <br>
**Implementation:** Implemented with custom Javascript and the `step2` template override: `pprOjsPlugin/templates/submission/form/step2.tpl`

**Issue Id:** Issue 064 <br>
**Area:** List of Contributors <br>
**Title:** Contributor's List shows the Author's Name first <br>
**User Story:** As a Managing Editor or Associate Editor, I need clarity on who submitted a paper within the contributor's list. With the implementation of this feature, the author's name will always appear at the top of the contributor's list, regardless of the submission order. <br>
**Toggle:** displayContributorsEnabled <br>
**Implementation:** This issue is to add the contributors component to the workflow pages, submissions and reviews.
- PPRWorkflowService service: `pprOjsPlugin/services/PPRWorkflowService.inc.php`

**Issue Id:** Issue 065 <br>
**Area:** Review Process <br>
**Title:** Associate Editor's Reminder Email <br>
**User Story:** As the Managing Editor, I would like the email notification to state the Associate Editors' name. Please add this variable in the backend. <br>
**Toggle:** firstNameEmailEnabled <br>
**Implementation:** All the author/reviewer/editor names are now managed by a single service, the PPRFirstNameEmailService: `pprOjsPlugin/services/email/PPRFirstNameEmailService.inc.php`

**Issue Id:** Issue 066 <br>
**Area:** Review Process <br>
**Title:** Hide the "None/Free Form Review" Option <br>
**User Story:** As an Associate Editor, when assigning a reviewer and opting for 'Create New Reviewer' within the 'Review Form' section, the current default option is 'None/Free Form Review,' which may leave the Reviewer without specific guidelines. We want to remove the 'None/Free Form Review' option to enhance clarity and consistency in the review process. Instead, 'Paper' will be the default, followed by 'Pre-Analysis' as the second option. This adjustment ensures that Reviewers receive clear instructions by default, improving the efficiency and quality of their evaluations. <br>
**Toggle:** hideReviewFormDefaultEnabled <br>
**Implementation:** Implemented with template override: `pprOjsPlugin/templates/controllers/grid/users/reviewer/form/createReviewerForm.tpl`

**Issue Id:** Issue 067 <br>
**Area:** Review Process <br>
**Title:** Mandatory for a Reviewer to Upload a File <br>
**User Story:** As a Reviewer completing a review, I want to receive a notification if I forget to attach a review document in the "Reviewer Files" section under step "3. Download & Review." There's no alert system to remind me to upload a file, nor do I receive any notification upon submitting the review without an attachment. Implementing a popup notification would ensure that I am prompted to upload a file before finalizing my review, thus enhancing the review process and preventing oversight. <br>
**Toggle:** reviewUploadFileValidationEnabled <br>
**Implementation:** Implemented with custom Javascript and the `step3` template override: `pprOjsPlugin/templates/reviewer/review/step3.tpl`

**Issue Id:** Issue 068 <br>
**Area:** Review Process <br>
**Title:** Eliminated the Registration Email <br>
**User Story:** As an Associate Editor, I want to streamline adding a Reviewer by ensuring that the system does not automatically send a registration email containing a username and password when adding a Reviewer through the Add Reviewer functionality in a submission. This will prevent confusion for the Reviewer, who might mistake the initial registration email as spam. Instead, only send the Scientific Request email, clearly stating the purpose of the request, thus improving the user experience. <br>
**Toggle:** reviewerRegistrationEmailDisabled <br>
**Implementation:** This is implemented using the `PPRDisableEmailService` service: `pprOjsPlugin/services/email/PPRDisableEmailService.inc.php`

**Issue Id:** Issue 070 <br>
**Area:** Review Process <br>
**Title:** Add BCC Managing Editor to the "Thank Reviewer" Email <br>
**User Story:** As a managing editor, I need to receive a notification that alerts me when the "Thank Reviewer" email is sent out. This will enhance the efficiency of the reviewers' payment process. This notification will prompt me to initiate the payment process without the need for manual checking. <br>
**Toggle:** reviewAddEditorToBccEnabled <br>
**Implementation:** This is implemented using the `PPRReviewAddEditorEmailService` service: `pprOjsPlugin/services/email/PPRReviewAddEditorEmailService.inc.php`

**Issue Id:** Issue 071 <br>
**Area:** Review Process <br>
**Title:** Reviewer Missed Review Reminder <br>
**User Story:** As a Reviewer who has yet to accept a review and whose response is overdue, I find receiving a reminder email for a missed Review Due Date problematic. It assumes I have already accepted the review, causing confusion and frustration. Instead, I prefer to receive a gentle reminder prompting me to accept or decline the review request and only receive reminders if the review is overdue. This would ensure a smoother and clearer communication process, improving the overall experience for both Reviewers and Editors. <br>
**Toggle:** reviewReminderReviewerTaskEnabled <br>
**Implementation:** This is implemented using the `PPRReviewReminder` scheduled task: `pprOjsPlugin/tasks/PPRReviewReminder.inc.php`

**Issue Id:** Issue 072 <br>
**Area:** Review Process <br>
**Title:** Remove the BCC Option to Reviewer <br>
**User Story:** As an Associate Editor, I should not be able to BCC a Reviewer on the same email sent to an Author, as this would violate confidentiality. When clicking the 'Send Review to Author' button, there is an option to 'Send to Reviewers' by selecting a checkbox to include the Reviewer, which would BCC them. However, since we maintain double-blinded reviews where the Author and Reviewer identities are kept confidential, this feature should be disabled to ensure compliance with our protocol. <br>
**Toggle:** hideSendToReviewersEnabled <br>
**Implementation:** Implemented with custom Javascript and the `sendReviewsForm` template override: `pprOjsPlugin/templates/controllers/modals/editorDecision/form/sendReviewsForm.tpl`

**Issue Id:** Issue 074 <br>
**Area:** Submission <br>
**Title:** Capture the type of research document <br>
**User Story:** As an associate editor, I must know the type of research document the author has uploaded to select the appropriate reviewer instructions to send to reviewers. I suggest displaying the research document type on the 'Select Reviewer' form above the 'Select Reviewer Instructions' dropdown to fulfill this requirement. I propose making the research document type editable within the plugin interface. This allows for seamless adjustment if the document type changes or requires reclassification. The document could be a Paper, Pre-Analysis Plan, Grant Proposal, Book Proposal, or categorized as Other. Secondly, I recommend integrating these document types into IQSS Reporting to enhance reporting capabilities. This can be achieved by repurposing the existing "Document Type" field and renaming it as "Research Document Type." <br>
**Toggle:** submissionResearchTypeEnabled <br>
**Implementation:** This is implemented using the `PPRSubmissionResearchTypeService` service: `pprOjsPlugin/services/submission/PPRSubmissionResearchTypeService.inc.php`

**Issue Id:** Issue 076 <br>
**Area:** Emails <br>
**Title:** Response for Review Reminder for Overdue Reviewers <br>
**User Story:** As an Associate/Managing Editor, I need the system to enable me to send both Response and Review reminders to overdue reviewers. It's important to ensure that the reminder is based on whether the reviewer has accepted the review. We require the capability to send either a Response or Review reminder if the reviewer exceeds the deadline. Clicking the "Send Reminder" button next to the reviewer's name only sends the Review reminder, even if the reviewer hasn't yet accepted the review. This enhancement will ensure that reviewers are reminded promptly based on their status, improving the efficiency of the review process. <br>
**Toggle:** reviewReminderEmailOverrideEnabled <br>
**Implementation:** This is implemented using the `PPRReviewReminderEmailService` service: `pprOjsPlugin/services/email/PPRReviewReminderEmailService.inc.php`

**Issue Id:** Issue 078 <br>
**Area:** Submission <br>
**Title:** Disable 'Submission Acknowledgement' Email to the Coauthor <br>
**User Story:** As a coauthor who lacks an account in our system and may not be eligible for submission, we don't want them to receive confirmation emails when the author submits a paper. We request that the email 'SUBMISSION_ACK_NOT_USER' be discontinued for coauthors without system accounts to prevent unnecessary notifications. Restricting this notification to the author ensures clarity in the submission process and prevents unnecessary emails for coauthors not directly involved in the submission workflow. <br>
**Toggle:** submissionConfirmationContributorsEmailDisabled <br>
**Implementation:** This is implemented using the `PPRDisableEmailService` service: `pprOjsPlugin/services/email/PPRDisableEmailService.inc.php`

**Issue Id:** Issue 079 <br>
**Area:** Emails <br>
**Title:** Ready for Review Email Only Sent to Author <br>
**User Story:** As an author utilizing the system, I expect to receive the review email exclusively. This prevents confusion for coauthors who do not have access to the platform. The email key 'EDITOR_DECISION_REVISIONS' should be exclusively sent to the submitting author. This implementation aims to improve user experience by preventing coauthors, not registered users, from receiving emails. <br>
**Toggle:** emailContributorsEnabled <br>
**Implementation:** This is implemented using the `PPREmailContributorsService` service: `pprOjsPlugin/services/email/PPREmailContributorsService.inc.php`

**Issue Id:** Issue 081 <br>
**Area:** Emails <br>
**Title:** Coauthor Email Exclusion List <br>
**User Story:** As a coauthor, I often receive emails that are not relevant to me because I am not currently a registered user in the system. To address this issue and improve user experience, I propose implementing a feature that excludes non-registered coauthors from receiving emails. This modification aims to improve user experience by preventing coauthors who are not registered users from receiving emails, thereby streamlining communication channels. This adjustment ensures that emails are directed only to the author unless specified otherwise, enhancing the efficiency and relevance of communication within the system. <br>
**Toggle:** emailContributorsEnabled <br>
**Implementation:** This has been bundled and implemented using the `PPREmailContributorsService` service: `pprOjsPlugin/services/email/PPREmailContributorsService.inc.php`

**Issue Id:** Issue 083 <br>
**Area:** Emails <br>
**Title:** Authors Choice to CC Coauthors on Submission Emails <br>
**User Story:** As an Author, I want the ability to choose when to add my coauthors to particular emails in the system. This way, I can efficiently manage communication and ensure that relevant collaborators are informed throughout the submission process. <br>
**Toggle:** emailContributorsEnabled <br>
**Implementation:** The implementation adds a new custom field using the `PPRSubmissionEmailContributorsService`, and executes the logic in the `PPREmailContributorsService`.
- PPRSubmissionEmailContributorsService service: `pprOjsPlugin/services/submission/PPRSubmissionEmailContributorsService.inc.php`
- PPREmailContributorsService service: `pprOjsPlugin/services/email/PPREmailContributorsService.inc.php`

**Issue Id:** Issue 084 <br>
**Area:** Profile <br>
**Title:** Hide the "Bio Statement" field on the User Profile <br>
**User Story:** As a peer pre-review process, we don't need to capture anyone's bio statement. Our system doesn't require readers to search for articles based on an author's bio. <br>
**Toggle:** hideUserBioEnabled <br>
**Implementation:** Implemented with template override: `pprOjsPlugin/templates/user/publicProfileForm.tpl`

**Issue Id:** Issue 085 <br>
**Area:** Review Process <br>
**Title:** Hide Decision Dropdown within the Associate Editors Workflow <br>
**User Story:** As an Associate Editor, I require a streamlined workflow that eliminates unnecessary decision-making and ensures consistency in the review process. Within my workflow, the option "revisions will undergo a new round of peer reviews" should be hidden, and the default should be "Revisions will not be subject to a new round of peer reviews." <br>
**Toggle:** hideReviewRoundSelectionEnabled <br>
**Implementation:** Implemented with custom Javascript and the `sendReviewsForm` template override: `pprOjsPlugin/templates/controllers/modals/editorDecision/form/sendReviewsForm.tpl`

**Issue Id:** Issue 089 <br>
**Area:** Reports <br>
**Title:** Customized IQSS Report for Program Metrics Tracking <br>
**User Story:** As a managing editor, I need to efficiently track where my reviews are in the system. To streamline this process, I propose implementing an automated weekly pull of the IQSS Peer Pre-Review Report every Monday. This report will provide comprehensive insights into the status of reviews within the system. Additionally, the email functionality will allow notifications to be sent to multiple email addresses, enhancing communication with relevant stakeholders. <br>
**Toggle:** submissionsReviewsReportEnabled <br>
**Implementation:** This is implemented using the `PPREditorReportTask` scheduled task: `pprReviewsReportPlugin/tasks/PPREditorReportTask.inc.php`

The schedule for the task is defined: `pprReviewsReportPlugin/scheduledTasks.xml`

**Issue Id:** Issue 090 <br>
**Area:** Submission <br>
**Title:** Review Template Pre-selected Based on Research Document Type <br>
**User Story:** As an Associate Editor, I would like the review template guidelines for papers or pre-analysis plans to be automatically preselected when the Author chooses this document type for their submission. This automation would streamline the review process by ensuring that the appropriate directions appear for the reviewers without the Associate Editor manually selecting the template, ultimately saving time. <br>
**Toggle:** submissionResearchTypeEnabled <br>
**Implementation:** This is implemented using the `PPRSubmissionResearchTypeService` service: `pprOjsPlugin/services/submission/PPRSubmissionResearchTypeService.inc.php`

**Issue Id:** Issue 092 <br>
**Area:** Review Process <br>
**Title:** "Unassigning Reviewer" and "Cancel Reviewer" <br>
**User Story:** When an Associate Editor needs to cancel a reviewer, they encounter two distinct stages, each requiring different email wording. Suppose a response from the reviewer is overdue, and they have yet to accept the review. In that case, the Associate Editor must choose 'Unassigning Reviewer' and dispatch a specific email with tailored wording. Conversely, suppose the reviewer has already accepted the review, and the review itself is overdue. In that case, the Associate Editor should opt for 'Cancel Reviewer' and trigger a different email template designed for that scenario. Presently, the system relies on a single template titled REVIEW_CANCEL for both functions, which doesn't adequately address the nuanced communication needs. Therefore, it's imperative to introduce an additional template to ensure precise and context-specific messaging for each cancellation stage. <br>
**Toggle:** reviewerGridServiceEnabled <br>
**Implementation:** This is bundled with the implementation for the `PPRReviewerGridService` service: `pprOjsPlugin/services/reviewer/PPRReviewerGridService.inc.php`
- PPRReviewerGridHandler handler: `pprOjsPlugin/services/reviewer/PPRReviewerGridHandler.inc.php`
- PPRUnassignReviewerForm form: `pprOjsPlugin/services/reviewer/PPRUnassignReviewerForm.inc.php`

**Issue Id:** Issue 093 <br>
**Area:** Emails <br>
**Title:** Disable Sending of Decline Email to Coauthors <br>
**User Story:** As a managing/associate editor, I need the ability to prevent the sending of decline emails 'EDITOR_DECISION_INITIAL_DECLINE' and 'EDITOR_DECISION_DECLINE' to coauthors listed as contributors when using the "Decline" button in the system. We need to turn off the sending of the email templates 'EDITOR_DECISION_INITIAL_DECLINE' and 'EDITOR_DECISION_DECLINE' to coauthors who are listed as contributors when the "Decline" button is clicked in the system. These emails are sent to coauthors, causing unnecessary communication and confusion. Disabling this feature will ensure that only the submitting author receives the decline notification. <br>
**Toggle:** emailContributorsEnabled <br>
**Implementation:** This has been bundled and implemented using the `PPREmailContributorsService` service: `pprOjsPlugin/services/email/PPREmailContributorsService.inc.php`

**Issue Id:** Issue 095 <br>
**Area:** Submission <br>
**Title:** Remove the option "Publish" and "Create New Version" Button <br>
**User Story:** As managing/associate editors, we must remove the 'Publish' and 'Create New Version' buttons. As our workflow does not involve publishing content, these options are unnecessary and pose a risk of accidental actions. By eliminating these buttons, we ensure that only appropriate actions are available to users, enhancing the integrity of our system and preventing unintended outcomes.

Remove the option "Publish" and "Create New Version" Buttons from the Submission Details Page. See image. <br>
**Toggle:** N/A <br>
**Implementation:** Implemented with custom CSS: `pprOjsPlugin/css/iqss.css`

**Issue Id:** Issue 096 <br>
**Area:** Profile <br>
**Title:** Hide Privacy Statements <br>
**User Story:** As per our legal team's guidelines, we are not permitted to include a privacy statement within our system. <br>
**Toggle:** N/A <br>
**Implementation:** Implemented by updating the OJS locale for the privacy statement with a space " " to make it disappear.

**Issue Id:** Issue 098 <br>
**Area:** Survey <br>
**Title:** Survey for Authors Post Submission on System <br>
**User Story:** As managing editors, I seek to gather feedback from Reviewers on their process to enhance our review workflow. We've created a survey within the review form, but its placement could be more optimal, and we need a systematic way to capture and export the data for analysis. To address this, we propose integrating a survey directly into the Reviewers' "4. Completion" screen, ensuring consistent visibility and accessibility. We aim to streamline the process by eliminating the need for Reviewers to select a specific form, ensuring that the survey always appears upon completion of their review. Additionally, we request that the survey results be automatically emailed to PeerPreReview@iq.harvard.edu each time a form is submitted, preferably in Excel format, facilitating efficient data collection and analysis. <br>
**Toggle:** reviewerSurveyHtml - We use the actual survey HTML to know if enabled <br>
**Implementation:** Implemented with template override: `pprOjsPlugin/templates/reviewer/review/reviewCompleted.tpl`

**Issue Id:** Issue 108 <br>
**Area:** Emails <br>
**Title:** Author Notification: Submission Approved <br>
**User Story:** As a Managing Editor, I need the system to automatically send an email notification to the author when I click the "Send to Review" button, informing them that their submission has been approved and is now being assigned to a reviewer. This notification ensures that authors are kept informed about the progress of their submissions and fosters transparency in the review process. <br>
**Toggle:** submissionApprovedEmailEnabled <br>
**Implementation:** This is implemented using the `PPRSubmissionActionsService` service: `pprOjsPlugin/services/submission/PPRSubmissionActionsService.inc.php`

**Issue Id:** Issue 109 <br>
**Area:** Review Process <br>
**Title:** Popup Notification for Missed Review Attachment <br>
**User Story:** As an Associate Editor, I want to ensure that both reviews are sent to the Author after receiving them. If a second review is returned and I need to check the box for it to be sent, the Author only receives the old review without the new attachment. To prevent this oversight, I propose adding a column indicating whether the review was sent or displaying the reviewer's name, providing a visual cue to ensure all reviews are sent to the Author. <br>
**Toggle:** reviewAttachmentsOverrideEnabled <br>
**Implementation:** Implemented using the `PPRReviewAttachmentsService` service: `pprOjsPlugin/services/reviewer/PPRReviewAttachmentsService.inc.php`
- PPRReviewAttachmentsGridHandler handler: `pprOjsPlugin/services/reviewer/PPRReviewAttachmentsGridHandler.inc.php`
- PPRReviewAttachmentGridCellProvider form: `pprOjsPlugin/services/reviewer/PPRReviewAttachmentGridCellProvider.inc.php`

**Issue Id:** Issue 111 <br>
**Area:** Registration <br>
**Title:** Registration Page for Authors Only Wording <br>
**User Story:** As a user, clarity about the registration process is crucial. Since the registration page is exclusively for authors, adding wording like "author profile" is essential to avoid confusion. This addition will indicate that the registration process is tailored specifically for authors, helping users understand the page's purpose. <br>
**Toggle:** userCustomFieldsEnabled <br>
**Implementation:** Implemented with custom CSS and the `userRegister` template override: `pprOjsPlugin/templates/frontend/pages/userRegister.tpl`

**Issue Id:** Issue 112 <br>
**Area:** Email <br>
**Title:** Reviewer Accepted Confirmation Email <br>
**User Story:** As a reviewer, I want to receive a confirmation email after accepting a review request. The email should contain a link allowing me to reset my username/password if necessary, enabling me to log in directly as needed. <br>
**Toggle:** reviewAcceptedEmailEnabled <br>
**Implementation:** Implemented using the `PPRReviewAcceptedService` service: `pprOjsPlugin/services/reviewer/PPRReviewAcceptedService.inc.php`

**Issue Id:** Issue 114 <br>
**Area:** Email <br>
**Title:** Survey for Authors Post Submission on Review <br>
**User Story:** As the Managing Editor, I want to implement a survey that gathers authors' feedback one week after receiving a review for their submission. This survey should focus on the author's experience with the review they received, separate from the system survey. Each time a review is sent to an author, they should receive an email notification containing the survey link. <br>
**Toggle:** reviewSentAuthorTaskEnabled <br>
**Implementation:** This is implemented using the `PPRReviewSentAuthorNotification` scheduled task: `pprOjsPlugin/tasks/PPRReviewSentAuthorNotification.inc.php`

The schedule for the task is defined: `pprOjsPlugin/scheduledTasks.xml`

**Issue Id:** Issue 115 <br>
**Area:** Submission <br>
**Title:** Remove "Other Selection" Under the Author Submission Workflow <br>
**User Story:** As an author, it can be confusing to see two 'Other' options when uploading my research document and being presented with a list of 'Primary academic disciplines' to choose from. We propose removing the "Other Selection" option under the Author Submission Workflow to enhance clarity and reduce confusion for Authors during the submission process. <br>
**Toggle:** N/A - Custom IQSS CSS is always included to OJS. <br>
**Implementation:** Implemented with custom CSS: `pprOjsPlugin/css/iqss.css`

**Issue Id:** Issue 118 <br>
**Area:** Reviewer Process <br>
**Title:** Review Submitted Confirmation Email (Reviewer) <br>
**User Story:** As a reviewer, I require confirmation of my review submission after clicking the 'Submit Review' button. I need to be assured that my review has been successfully submitted. Therefore, I want an email notification that automatically sends me a confirmation email immediately after I click the 'Submit Review' button. <br>
**Toggle:** reviewSubmittedEmailEnabled <br>
**Implementation:** Implemented using the `PPRReviewSubmittedService` service: `pprOjsPlugin/services/reviewer/PPRReviewSubmittedService.inc.php`

**Issue Id:** Issue 119 <br>
**Area:** Reviewer Process <br>
**Title:** Continue Button Renaming to Complete <br>
**User Story:** When uploading a file, users, especially reviewers, find it confusing when the button text says "Continue" without clear guidance on the subsequent steps, particularly if they need to take further action, such as clicking a button. This lack of clarity often leads to oversight, delaying the completion of the review process. <br>
**Toggle:** fileUploadTextOverrideEnabled <br>
**Implementation:** Implemented with template override: `pprOjsPlugin/templates/controllers/wizard/fileUpload/fileUploadWizard.tpl`

**Issue Id:** Issue 120 <br>
**Area:** Reviewer Process <br>
**Title:** Reviewer Reminder for Open Review with File Attached <br>
**User Story:** As a Reviewer, I'd appreciate a reminder when I have uploaded a file, but I have not clicked the blue "Submit Review" button. This reminder should be triggered when pending reviews are detected, and at least one file has been uploaded.

The logic for this email reminder needs to be added to the existing custom review reminder notification for reviewers as they are both related.

The logic will be updated as follows:
- Check all pending reviews that has been accepted by reviewer.
- First check if review is due. If due send notification and end.
- If not due yet, check for review files. If at least one file, send notification and end.

**Toggle:** reviewReminderReviewerTaskEnabled <br>
**Implementation:** This is implemented using the `PPRReviewReminder` scheduled task: `pprOjsPlugin/tasks/PPRReviewReminder.inc.php`

The schedule for the task is defined: `pprOjsPlugin/scheduledTasks.xml`

**Issue Id:** Issue 125 <br>
**Area:** Survey <br>
**Title:** Survey for Authors About OJS System <br>
**User Story:** As an author, to avoid being inconvenienced with multiple system surveys, we propose sending the survey to authors only once after the submission takes place. By implementing this approach, we aim to gather valuable feedback from authors while minimizing disruption to their workflow and optimizing the effectiveness of the survey process. <br>
**Toggle:** authorSubmissionSurveyHtml - We use the actual survey HTML to know if enabled <br>
**Implementation:** Implemented using the `PPRAuthorSubmissionSurveyService` service: `pprOjsPlugin/services/submission/PPRAuthorSubmissionSurveyService.inc.php`

**Issue Id:** Issue 127 <br>
**Area:** Email <br>
**Title:** Utilize First Name Only in Email Communications <br>
**User Story:** As the Managing Editor, I want to update all active email templates in our current workflow to use only the recipient's first name. This adjustment aims to create a more informal and user-friendly communication style.

This was implemented in batches using issues: 129, 130, 131, 132 <br>
**Toggle:** firstNameEmailEnabled <br>
**Implementation:** All the author/reviewer/editor names are now managed by a single service, the PPRFirstNameEmailService: `pprOjsPlugin/services/email/PPRFirstNameEmailService.inc.php`

**Issue Id:** Issue 135 <br>
**Area:** Reports <br>
**Title:** Customized IQSS Report for Program Metrics Tracking <br>
**User Story:** As a Managing Editor who receives only one or two monthly submissions but frequently receives reports, setting the report default to quarterly would be more efficient. This adjustment ensures that reports are generated less often, aligning better with the volume of submissions. <br>
**Toggle:** submissionsReviewsReportEnabled <br>
**Implementation:** This is implemented using the `PPREditorReportTask` scheduled task: `pprReviewsReportPlugin/tasks/PPREditorReportTask.inc.php`

The schedule for the task is defined: `pprReviewsReportPlugin/scheduledTasks.xml`

**Issue Id:** Issue 136 <br>
**Area:** Reviewer List <br>
**Title:** Track Unassigned Reviewers <br>
**User Story:** As an Associate Editor, I require unassigned reviewers to be consistently visible within the Reviewer list, designated with an 'Unassigned' status. This visibility is crucial for preventing the accidental sending of duplicate review requests. <br>
**Toggle:** reviewerGridServiceEnabled <br>
**Implementation:** This is bundled with the implementation for the `PPRReviewerGridService` service: `pprOjsPlugin/services/reviewer/PPRReviewerGridService.inc.php`
- PPRReviewerGridHandler handler: `pprOjsPlugin/services/reviewer/PPRReviewerGridHandler.inc.php`
- PPRUnassignReviewerForm form: `pprOjsPlugin/services/reviewer/PPRUnassignReviewerForm.inc.php`

**Issue Id:** Issue 137 <br>
**Area:** Reviewer List <br>
**Title:** Track Declined Reviewers <br>
**User Story:** "As an Associate Editor, I require the ability to track the date when a reviewer declines review requests to manage submission statuses effectively. To facilitate this, I propose incorporating this information into our Reviewer list. <br>
**Toggle:** reviewerGridServiceEnabled <br>
**Implementation:** This is bundled with the implementation for the `PPRReviewerGridService` service: `pprOjsPlugin/services/reviewer/PPRReviewerGridService.inc.php`
- PPRReviewerGridHandler handler: `pprOjsPlugin/services/reviewer/PPRReviewerGridHandler.inc.php`
- PPRReviewerGridCellProvider form: `pprOjsPlugin/services/reviewer/PPRReviewerGridCellProvider.inc.php`
  
**Issue Id:** Issue 141 <br>
**Area:** Email <br>
**Title:** Post-One-Year Survey: Tracking Publication Status <br>
**User Story:** As a program manager overseeing paper submissions, I need to implement a post-one-year survey to track the publication status of authors' papers. The survey must be designed to gather information specifically on whether papers have been published or are still in the publication process one year after submission closure. It should collect data on the publication status of each paper submitted to the program, including whether the paper has been published, is under review, or is still in the publication process.

The survey should be sent to authors one year after the closure of paper submissions. It should be user-friendly and easy for authors to complete. The results of the survey must be recorded and stored in a centralized database for analysis. This data will be crucial for reporting to donors and updating metrics on the program webpage to demonstrate the program's success and impact.

The survey should not be directly tied to individual reviews but should provide an overview of the progress of each author's paper. Authors' participation in the survey should be encouraged, emphasizing the importance of their input in monitoring submission progress and program success. The program team should be able to effectively monitor the progress of paper submissions using the collected data.
<br>
**Toggle:** submissionClosedAuthorTaskEnabled <br>
**Implementation:** This is implemented using the `PPRSubmissionClosedAuthorNotification` scheduled task: `pprOjsPlugin/tasks/PPRSubmissionClosedAuthorNotification.inc.php`

The schedule for the task is defined: `pprOjsPlugin/scheduledTasks.xml`

**Issue Id:** Issue 142 <br>
**Area:** Reports <br>
**Title:** Add annual survey notification to Report <br>
**User Story:** As a Managing Editor, I want to see when a survey has been sent to the author. To facilitate this, I suggest adding a new column to the report titled "Survey Sent Date." In this column, the date of the annual survey email will be recorded. If the email has not been sent, the column will remain empty. This addition will provide valuable insight into the survey communication process, aiding in tracking and management. <br>
**Toggle:** N/A <br>
**Implementation:** This is implemented with the `pprReviewsReportPlugin` code. This report is available throw the OJS UI and sent via email.

**Issue Id:** Issue 145 <br>
**Area:** Submission <br>
**Title:** Remove Checkboxes Show Just Submission Requirements <br>
**User Story:** As an author, I find it confusing to check off boxes during the submission process only to realize later that not all requirements are necessary. To simplify the process and improve clarity, I propose removing the checkbox requirement altogether and instead displaying the complete list of submission components upfront. <br>
**Toggle:** submissionConfirmationChecklistEnabled <br>
**Implementation:** Implemented with template overrides: `pprOjsPlugin/templates/submission/form/step[1,4].tpl`