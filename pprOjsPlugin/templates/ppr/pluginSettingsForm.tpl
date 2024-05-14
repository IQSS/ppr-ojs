
<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#pprPluginSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>


<form class="pkp_form" id="pprPluginSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	<div id="pprProfileSettings">

		<p id="description">
			{translate key="plugins.generic.pprPlugin.settings.description" }
		</p>

		{csrf}
		{include file="controllers/notification/inPlaceNotification.tpl" notificationId="pprPluginSettingsFormNotification"}

		<h2>{translate key="plugins.generic.pprPlugin.settings.section.featureFlags"}</h2>

		{fbvFormSection title="plugins.generic.pprPlugin.settings.section.core" list="true"}
			{fbvElement type="checkbox" name="fileUploadTextOverrideEnabled" label="plugins.generic.pprPlugin.settings.fileUploadTextOverrideEnabled.label" id="fileUploadTextOverrideEnabled" checked=$fileUploadTextOverrideEnabled}
			<div class="subsection">
				{fbvElement type="text" name="accessKeyLifeTime" label="plugins.generic.pprPlugin.settings.accessKeyLifeTime.label" id="accessKeyLifeTime" value=$accessKeyLifeTime}
			</div>
		{/fbvFormSection}

		{fbvFormSection title="plugins.generic.pprPlugin.settings.section.workflow" list="true"}
			{fbvElement type="checkbox" name="displayWorkflowMessageEnabled" label="plugins.generic.pprPlugin.settings.workflowMessage.label" id="displayWorkflowMessageEnabled" checked=$displayWorkflowMessageEnabled}
			{fbvElement type="checkbox" name="displayContributorsEnabled" label="plugins.generic.pprPlugin.settings.contributors.label" id="displayContributorsEnabled" checked=$displayContributorsEnabled}
			{fbvElement type="checkbox" name="displaySuggestedReviewersEnabled" label="plugins.generic.pprPlugin.settings.reviewers.label" id="displaySuggestedReviewersEnabled" checked=$displaySuggestedReviewersEnabled}
		    {fbvElement type="checkbox" name="hideReviewRoundSelectionEnabled" label="plugins.generic.pprPlugin.settings.hideReviewRoundSelectionEnabled.label" id="hideReviewRoundSelectionEnabled" checked=$hideReviewRoundSelectionEnabled}
		    {fbvElement type="checkbox" name="hideSendToReviewersEnabled" label="plugins.generic.pprPlugin.settings.hideSendToReviewersEnabled.label" id="hideSendToReviewersEnabled" checked=$hideSendToReviewersEnabled}
			{fbvElement type="checkbox" name="submissionRequestRevisionsFileValidationEnabled" label="plugins.generic.pprPlugin.settings.submissionRequestRevisionsFileValidationEnabled.label" id="submissionRequestRevisionsFileValidationEnabled" checked=$submissionRequestRevisionsFileValidationEnabled}
			{fbvElement type="checkbox" name="publicationOverrideEnabled" label="plugins.generic.pprPlugin.settings.publicationOverrideEnabled.label" id="publicationOverrideEnabled" checked=$publicationOverrideEnabled}
		{/fbvFormSection}

		{fbvFormSection title="plugins.generic.pprPlugin.settings.section.users" list="true"}
			{fbvElement type="checkbox" name="hidePreferredPublicNameEnabled" label="plugins.generic.pprPlugin.settings.preferredPublicName.label" id="hidePreferredPublicNameEnabled" checked=$hidePreferredPublicNameEnabled}
			{fbvElement type="checkbox" name="hideUserBioEnabled" label="plugins.generic.pprPlugin.settings.hideUserBioEnabled.label" id="hideUserBioEnabled" checked=$hideUserBioEnabled}
			{fbvElement type="checkbox" name="userOnLeaveEnabled" label="plugins.generic.pprPlugin.settings.userOnLeaveEnabled.label" id="userOnLeaveEnabled" checked=$userOnLeaveEnabled}
			{fbvElement type="checkbox" name="userCustomFieldsEnabled" label="plugins.generic.pprPlugin.settings.userCustomFields.label" id="userCustomFieldsEnabled" checked=$userCustomFieldsEnabled}
			<div class="subsection">
				{fbvElement type="text" name="categoryOptions" label="plugins.generic.pprPlugin.settings.categoryOptions.label" id="categoryOptions" value=$categoryOptions}
			</div>
			<div class="subsection">
				{fbvElement type="text" name="institutionOptions" label="plugins.generic.pprPlugin.settings.institutionOptions.label" id="institutionOptions" value=$institutionOptions}
			</div>
		{/fbvFormSection}


		{fbvFormSection title="plugins.generic.pprPlugin.settings.section.reviews" list="true"}
			{fbvElement type="checkbox" name="hideReviewMethodEnabled" label="plugins.generic.pprPlugin.settings.reviewMethod.label" id="hideReviewMethodEnabled" checked=$hideReviewMethodEnabled}
			{fbvElement type="checkbox" name="hideReviewFormDefaultEnabled" label="plugins.generic.pprPlugin.settings.hideReviewFormDefault.label" id="hideReviewFormDefaultEnabled" checked=$hideReviewFormDefaultEnabled}
			{fbvElement type="checkbox" name="hideReviewRecommendationEnabled" label="plugins.generic.pprPlugin.settings.reviewRecommendation.label" id="hideReviewRecommendationEnabled" checked=$hideReviewRecommendationEnabled}
			{fbvElement type="checkbox" name="reviewUploadFileValidationEnabled" label="plugins.generic.pprPlugin.settings.reviewUploadFileValidationEnabled.label" id="reviewUploadFileValidationEnabled" checked=$reviewUploadFileValidationEnabled}
			{fbvElement type="checkbox" name="reviewAttachmentsOverrideEnabled" label="plugins.generic.pprPlugin.settings.reviewAttachmentsOverrideEnabled.label" id="reviewAttachmentsOverrideEnabled" checked=$reviewAttachmentsOverrideEnabled}
		    {fbvElement type="checkbox" name="reviewerGridServiceEnabled" label="plugins.generic.pprPlugin.settings.reviewerGridServiceEnabled.label" id="reviewerGridServiceEnabled" checked=$reviewerGridServiceEnabled}
		{/fbvFormSection}

		{fbvFormSection title="plugins.generic.pprPlugin.settings.section.submissions" list="true"}
			{fbvElement type="checkbox" name="submissionCommentsForReviewerEnabled" label="plugins.generic.pprPlugin.settings.submissionCommentsForReviewerEnabled.label" id="submissionCommentsForReviewerEnabled" checked=$submissionCommentsForReviewerEnabled}
			{fbvElement type="checkbox" name="submissionResearchTypeEnabled" label="plugins.generic.pprPlugin.settings.submissionResearchTypeEnabled.label" id="submissionResearchTypeEnabled" checked=$submissionResearchTypeEnabled}
			<div class="subsection">
				{fbvElement type="text" name="researchTypeOptions" label="plugins.generic.pprPlugin.settings.researchTypeOptions.label" id="researchTypeOptions" value=$researchTypeOptions}
			</div>
			{fbvElement type="checkbox" name="submissionHidePrefixEnabled" label="plugins.generic.pprPlugin.settings.submissionHidePrefixEnabled.label" id="submissionHidePrefixEnabled" checked=$submissionHidePrefixEnabled}
			{fbvElement type="checkbox" name="submissionCloseEnabled" label="plugins.generic.pprPlugin.settings.submissionCloseEnabled.label" id="submissionCloseEnabled" checked=$submissionCloseEnabled}
			{fbvElement type="checkbox" name="submissionConfirmationChecklistEnabled" label="plugins.generic.pprPlugin.settings.submissionConfirmationChecklistEnabled.label" id="submissionConfirmationChecklistEnabled" checked=$submissionConfirmationChecklistEnabled}
			{fbvElement type="checkbox" name="submissionUploadFileValidationEnabled" label="plugins.generic.pprPlugin.settings.submissionUploadFileValidationEnabled.label" id="submissionUploadFileValidationEnabled" checked=$submissionUploadFileValidationEnabled}
		{/fbvFormSection}

		{fbvFormSection title="plugins.generic.pprPlugin.settings.section.surveys" list="true"}
			<div class="subsection">
				{fbvElement type="textarea" name="authorSubmissionSurveyHtml" label="plugins.generic.pprPlugin.settings.authorSubmissionSurveyHtml.label" id="authorSubmissionSurveyHtml" value=$authorSubmissionSurveyHtml height=$fbvStyles.height.SHORT}
			</div>
			<div class="subsection">
				{fbvElement type="textarea" name="authorDashboardSurveyHtml" label="plugins.generic.pprPlugin.settings.authorDashboardSurveyHtml.label" id="authorDashboardSurveyHtml" value=$authorDashboardSurveyHtml height=$fbvStyles.height.SHORT}
			</div>
			<div class="subsection">
				{fbvElement type="textarea" name="reviewerSurveyHtml" label="plugins.generic.pprPlugin.settings.reviewerSurveyHtml.label" id="reviewerSurveyHtml" value=$reviewerSurveyHtml height=$fbvStyles.height.SHORT}
			</div>
		{/fbvFormSection}

		{fbvFormSection title="plugins.generic.pprPlugin.settings.section.emails" list="true"}
			{fbvElement type="checkbox" name="firstNameEmailEnabled" label="plugins.generic.pprPlugin.settings.firstNameEmailEnabled.label" id="firstNameEmailEnabled" checked=$firstNameEmailEnabled}
			{fbvElement type="checkbox" name="reviewerRegistrationEmailDisabled" label="plugins.generic.pprPlugin.settings.reviewerRegistrationEmailDisabled.label" id="reviewerRegistrationEmailDisabled" checked=$reviewerRegistrationEmailDisabled}
			{fbvElement type="checkbox" name="reviewAcceptedEmailEnabled" label="plugins.generic.pprPlugin.settings.reviewAcceptedEmailEnabled.label" id="reviewAcceptedEmailEnabled" checked=$reviewAcceptedEmailEnabled}
			{fbvElement type="checkbox" name="reviewSubmittedEmailEnabled" label="plugins.generic.pprPlugin.settings.reviewSubmittedEmailEnabled.label" id="reviewSubmittedEmailEnabled" checked=$reviewSubmittedEmailEnabled}
			{fbvElement type="checkbox" name="reviewReminderEmailOverrideEnabled" label="plugins.generic.pprPlugin.settings.reviewReminderEmailOverrideEnabled.label" id="reviewReminderEmailOverrideEnabled" checked=$reviewReminderEmailOverrideEnabled}
			{fbvElement type="checkbox" name="reviewAddEditorToBccEnabled" label="plugins.generic.pprPlugin.settings.reviewAddEditorToBccEnabled.label" id="reviewAddEditorToBccEnabled" checked=$reviewAddEditorToBccEnabled}
			{fbvElement type="checkbox" name="submissionApprovedEmailEnabled" label="plugins.generic.pprPlugin.settings.submissionApprovedEmailEnabled.label" id="submissionApprovedEmailEnabled" checked=$submissionApprovedEmailEnabled}
			{fbvElement type="checkbox" name="submissionConfirmationContributorsEmailDisabled" label="plugins.generic.pprPlugin.settings.submissionConfirmationContributorsEmailDisabled.label" id="submissionConfirmationContributorsEmailDisabled" checked=$submissionConfirmationContributorsEmailDisabled}
			{fbvElement type="checkbox" name="emailContributorsEnabled" label="plugins.generic.pprPlugin.settings.emailContributorsEnabled.label" id="emailContributorsEnabled" checked=$emailContributorsEnabled}
		{/fbvFormSection}

		{fbvFormSection title="plugins.generic.pprPlugin.settings.section.tasks" list="true"}
			<div class="subsection">
				{fbvElement type="checkbox" name="reviewReminderEditorTaskEnabled" label="plugins.generic.pprPlugin.settings.reviewReminderEditorTaskEnabled.label" id="reviewReminderEditorTaskEnabled" checked=$reviewReminderEditorTaskEnabled}
				{fbvElement type="text" name="reviewReminderEditorDaysFromDueDate" label="plugins.generic.pprPlugin.settings.reviewReminderEditorDaysFromDueDate.label" id="reviewReminderEditorDaysFromDueDate" value=$reviewReminderEditorDaysFromDueDate}
			</div>

			<div class="subsection">
				{fbvElement type="checkbox" name="reviewReminderReviewerTaskEnabled" label="plugins.generic.pprPlugin.settings.reviewReminderReviewerTaskEnabled.label" id="reviewReminderReviewerTaskEnabled" checked=$reviewReminderReviewerTaskEnabled}
				{fbvElement type="text" name="reviewReminderReviewerDaysFromDueDate" label="plugins.generic.pprPlugin.settings.reviewReminderReviewerDaysFromDueDate.label" id="reviewReminderReviewerDaysFromDueDate" value=$reviewReminderReviewerDaysFromDueDate}
			</div>

		    <div class="subsection">
				{fbvElement type="checkbox" name="reviewSentAuthorTaskEnabled" label="plugins.generic.pprPlugin.settings.reviewSentAuthorTaskEnabled.label" id="reviewSentAuthorTaskEnabled" checked=$reviewSentAuthorTaskEnabled}
				{fbvElement type="text" name="reviewSentAuthorWaitingDays" label="plugins.generic.pprPlugin.settings.reviewSentAuthorWaitingDays.label" id="reviewSentAuthorWaitingDays" value=$reviewSentAuthorWaitingDays}
				{fbvElement type="text" name="reviewSentAuthorEnabledDate" label="plugins.generic.pprPlugin.settings.reviewSentAuthorEnabledDate.label" id="reviewSentAuthorEnabledDate" value=$reviewSentAuthorEnabledDate class="datepicker"}
		    </div>

			<div class="subsection">
				{fbvElement type="checkbox" name="submissionClosedAuthorTaskEnabled" label="plugins.generic.pprPlugin.settings.submissionClosedAuthorTaskEnabled.label" id="submissionClosedAuthorTaskEnabled" checked=$submissionClosedAuthorTaskEnabled}
				{fbvElement type="text" name="submissionClosedAuthorWaitingDays" label="plugins.generic.pprPlugin.settings.submissionClosedAuthorWaitingDays.label" id="submissionClosedAuthorWaitingDays" value=$submissionClosedAuthorWaitingDays}
			</div>

			<div class="subsection">
				{fbvElement type="checkbox" name="scheduledTasksReset" label="plugins.generic.pprPlugin.settings.scheduledTasksReset.label" id="scheduledTasksReset"}
			</div>
		{/fbvFormSection}

		{fbvFormButtons}
		<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
	</div>
</form>
