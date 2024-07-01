# Feature Descriptions

## Technical Notes
All template overrides are implemented in the Service class: `pprOjsPlugin/services/PPRWorkflowService.inc.php`

## Descriptions

**Issue Id:** Issue 010 <br>
**Area:** Submission <br>
**Title:** Remove the "Include this contributor in the browse list" Option <br>
**User Story:** As a user, searching for contributors on this platform is unnecessary, as its primary function is peer review rather than journal publication. Including the "Include this contributor in the browse list" option could confuse authors when submitting their papers. <br>
**Toggle:** N/A
**Implementation:** This was implemented by PKP directly. This was implemented with CSS, there is a copy of the CSS code at the end of our CSS file for reference: `pprOjsPlugin/css/iqss.css`

**Issue Id:** Issue 013 <br>
**Area:** Workflow <br>
**Title:** Hide the Copyediting Tab <br>
**User Story:** As a user, I expect the system to offer peer-review functionality without the option to publish articles exclusively. The presence of a Copyediting tab could lead to confusion among users. <br>
**Toggle:** N/A - Custom IQSS CSS is always included to OJS.
**Implementation:** Implemented with CSS, added selector to hie the Copyediting tab: `pprOjsPlugin/css/iqss.css`

**Issue Id:** Issue 019 <br>
**Area:** Registration <br>
**Title:** Hide Checkboxes on the Registration Page <br>
**User Story:** As an author registering on our platform, the registration page currently includes three checkboxes: A privacy statement, which is irrelevant as we don't have one; an option to receive notifications of new publications, even though we don't publish on our platform; registration is solely for peer review; and an option to be contacted for submission reviews, which we don't allow, as the author provides a list of potential reviewers. Considering these factors, including these checkboxes during registration is unnecessary and potentially confusing. Therefore, we should hide them to streamline registration and avoid user confusion. <br>
**Toggle:** N/A
**Implementation:** This was implemented by PKP directly. This was implemented with CSS, there is a copy of the CSS code at the end of our CSS file for reference: `pprOjsPlugin/css/iqss.css`

**Issue Id:** Issue 020 <br>
**Area:** Registration <br>
**Title:** Registration Process with Eligibility Confirmation Checkbox <br>
**User Story:** As an author registering for an account, I want to ensure that I have read and understood the program requirements to determine my eligibility before creating an account. Our registration page needs a clear indication for authors to confirm their review of eligibility requirements. To enhance user experience and ensure compliance with our platform's guidelines, we propose adding a checkbox prompting authors to acknowledge their review of eligibility criteria before proceeding with registration. <br>
**Toggle:** userCustomFieldsEnabled
**Implementation:** Implemented with custom CSS and the userRegister template override: `pprOjsPlugin/templates/frontend/pages/userRegister.tpl`

**Issue Id:** Issue 022 <br>
**Area:** Submission <br>
**Title:** Streamlining Submission Process with Checklist Integration <br>
**User Story:** As an author preparing to submit a research document, I want to double-check that I've included everything from the list on the "1. Start" tab in the submission process, ensuring the completeness and accuracy of my submission before finalizing it in the system.

To enhance the submission process and ensure completeness and accuracy, we propose integrating the checklist from the "1. Start" tab into the confirmation page on the "4. Confirmation" tab. This integration will allow authors to double-check that they've included everything from the initial checklist before finalizing their submission in the system. <br>
**Toggle:** submissionConfirmationChecklistEnabled
**Implementation:** Implemented with template overrides: `pprOjsPlugin/templates/submission/form/step[1,4].tpl`

**Issue Id:** Issue 023 <br>
**Area:** Submission <br>
**Title:** Separate Fields for Institutional Position and Academic Department <br>
**User Story:** As a Managing editor, I need to understand who the author is to check for eligibility. I need to know about their institutional position and academic department. This will also help with reporting metrics.

To improve program metrics reporting, assigning associate editors, and checking eligibility, we propose implementing two separate fields: Institutional Position and Academic Department. These will be included across three key areas: the user account profile, the contributor profile, and the registration profile. <br>
**Toggle:** userCustomFieldsEnabled
**Implementation:** This was implemented with the PPRUserCustomFieldsService service: `pprOjsPlugin/services/PPRUserCustomFieldsService.inc.php`

**Issue Id:** Issue 024 <br>
**Area:** Reviewer Process <br>
**Title:** Single Option for Type of Review for Double-Blinded Review Process <br>
**User Story:** As an Associate Editor, I require only one option for the type of review in our system, as our program strictly adheres to a double-blinded review process. The presence of multiple options poses a risk of accidentally disclosing the author's identity to the reviewer, which goes against our process guidelines. To ensure the integrity of our double-blinded review process, I propose simplifying the "Type of Review" options to include only "Anonymous Reviewer/Anonymous Author," removing the possibilities for "Anonymous Reviewer/Disclosed Author" and "Open." By streamlining the options, we can eliminate confusion and maintain the confidentiality crucial to our review process. <br>
**Toggle:** hideReviewMethodEnabled
**Implementation:** Implemented with template override: `pprOjsPlugin/templates/controllers/grid/users/reviewer/form/reviewerFormFooter.tpl`

**Issue Id:** Issue 026 <br>
**Area:** Workflow <br>
**Title:** Hide Publications Tab <br>
**User Story:** As a user, I expect the system to offer peer-review functionality without the option to publish articles exclusively. The presence of a Publications tab could lead to confusion among users. <br>
**Toggle:** N/A
**Implementation:** Not sure what this issue is.

**Issue Id:** Issue 033 <br>
**Area:** Submission <br>
**Title:** Hide the "Accept and Skip Review" Button <br>
**User Story:** As a managing editor, I ensure all submissions undergo peer review before acceptance. Removing the "Accept and Skip Review" option mitigates the risk of accidentally bypassing this crucial step. <br>
**Toggle:** N/A - Custom IQSS CSS is always included to OJS.
**Implementation:** Implemented with CSS, added selector to hie the Copyediting tab: `pprOjsPlugin/css/iqss.css`

**Issue Id:** Issue 036 <br>
**Area:** Review Process <br>
**Title:** Managing/Associate Editors Approval Process with Author's Reviewer List Visibility <br>
**User Story:** As a managing editor, I require visibility of the Author's "recommended reviewers" list to ensure all necessary information is included in the submission before proceeding to the "Send for Review" stage. <br>
**Toggle:** displaySuggestedReviewersEnabled
**Implementation:** Implemented with the PPRWorkflowService service: `pprOjsPlugin/services/PPRWorkflowService.inc.php`

**Issue Id:** Issue 037 <br>
**Area:** Submission <br>
**Title:** Dedicated Field for Authors to Direct Reviewer Attention <br>
**User Story:** As an Author, I need the capability to direct the Reviewer's attention to specific areas of my paper, facilitating a focused review process. This information should be visible to the Associate Editor responsible for assigning reviewers and any reviewers evaluating my paper.

To address this need, we propose implementing a dedicated field titled "If you'd like to direct the reviewer's attention to any particular area of the paper, please describe it here." This field will seamlessly integrate into the Reviewer's workflow under the "3. Download & Review" tab. Additionally, it will be prominently displayed on the "Activity --> Review" tab for Associate/Managing Editors and Authors, ensuring clear communication and enhancing the review process for all stakeholders. <br>
**Toggle:** submissionCommentsForReviewerEnabled
**Implementation:** This is implemented using the PPRSubmissionCommentsForReviewerService to add the new custom field, a template override needed to display the new form field, and the PPRWorkflowService to add the field to the workflow page.
 - PPRSubmissionCommentsForReviewerService service: `pprOjsPlugin/services/submission/PPRSubmissionCommentsForReviewerService.inc.php`
 - PPRWorkflowService service: `pprOjsPlugin/services/PPRWorkflowService.inc.php`

**Issue Id:** Issue 045 <br>
**Area:** Reports <br>
**Title:** Customized IQSS Report for Program Metrics Tracking <br>
**User Story:** As a managing editor who runs the program, I need a report to show the process of different areas in the system to keep track of how the program is doing, which can be exported to Smartsheet for a dashboard. This feature entails the development of a customized report to track various metrics within the system, including the number of reviews completed, distinct authors, coauthors, published authors, published papers, submissions, review length, total papers (unique titles), reviewers' review time, and reviewers' response time. <br>
**Toggle:** N/A
**Implementation:** This is implemented with the `pprReviewsReportPlugin` code. This report is available throw the OJS UI.

**Issue Id:** Issue 050 <br>
**Area:** Review Process <br>
**Title:** Author's Institution Visibility on the Review Tab <br>
**User Story:** As a Managing Editor or Associate Editor, I need access to the author institution details on the review tab. This visibility will enable me to confirm that authors and assigned reviewers represent separate institutions. <br>
**Toggle:** displayContributorsEnabled
**Implementation:** The OJS author.affiliation field is being re-purpose as institution. This changes displays institution in the Contributors component.
 - PPRWorkflowService service: `pprOjsPlugin/services/PPRWorkflowService.inc.php`
 - PPRAuthorGridHandler service: `pprOjsPlugin/services/PPRAuthorGridHandler.inc.php`
 - PPRAuthorGridCellProvider service: `pprOjsPlugin/services/PPRAuthorGridCellProvider.inc.php`

**Issue Id:** Issue 052 <br>
**Area:** Review Process <br>
**Title:** Author Coauthor Visibility on Review Tab <br>
**User Story:** As a Managing Editor or Associate Editor, I need access to the coauthor institution details on the review tab. This visibility will enable me to confirm that authors and assigned reviewers represent separate institutions. <br>
**Toggle:** displayContributorsEnabled
**Implementation:** This issue is to add the contributors component to the workflow pages, submissions and reviews.
 - PPRWorkflowService service: `pprOjsPlugin/services/PPRWorkflowService.inc.php`

**Issue Id:** Issue 055 <br>
**Area:** Publication Tab <br>
**Title:** Hide Fields on the "Submission Details" Tab <br>
**User Story:** As a user, it can be confusing to see certain fields since we operate differently from a typical journal; we solely conduct peer reviews. Therefore, on the "Submission Details (Old Name Publication)" tab, we need to hide the following sections: Galleys, Permissions & Disclosures, and Issue. <br>
**Toggle:** N/A - Custom IQSS CSS is always included to OJS.
**Implementation:** Implemented with CSS, added selector to hie the Copyediting tab: `pprOjsPlugin/css/iqss.css`

**Issue Id:** Issue 056 <br>
**Area:** Profile <br>
**Title:** Hide the "Reviewing Interests" field on the User Profile <br>
**User Story:** As a user, I don't want to be asked about my reviewing interests because the authors and associate editors decide who to contact for reviews. Our platform differs from a regular journal, so this feature is unnecessary. <br>
**Toggle:** N/A
**Implementation:** It is not implemented.
