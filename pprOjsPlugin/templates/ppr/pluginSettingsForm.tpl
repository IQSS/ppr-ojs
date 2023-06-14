
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

		{fbvFormSection title="plugins.generic.pprPlugin.settings.section.workflow" list="true"}
			{fbvElement type="checkbox" name="displayWorkflowMessageEnabled" label="plugins.generic.pprPlugin.settings.workflowMessage.label" id="displayWorkflowMessageEnabled" checked=$displayWorkflowMessageEnabled}
			{fbvElement type="checkbox" name="displayContributorsEnabled" label="plugins.generic.pprPlugin.settings.contributors.label" id="displayContributorsEnabled" checked=$displayContributorsEnabled}
			{fbvElement type="checkbox" name="displaySuggestedReviewersEnabled" label="plugins.generic.pprPlugin.settings.reviewers.label" id="displaySuggestedReviewersEnabled" checked=$displaySuggestedReviewersEnabled}
		{/fbvFormSection}

		{fbvFormSection title="plugins.generic.pprPlugin.settings.section.users" list="true"}
			{fbvElement type="checkbox" name="hidePreferredPublicNameEnabled" label="plugins.generic.pprPlugin.settings.preferredPublicName.label" id="hidePreferredPublicNameEnabled" checked=$hidePreferredPublicNameEnabled}
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
			{fbvElement type="checkbox" name="hideReviewRecommendationEnabled" label="plugins.generic.pprPlugin.settings.reviewRecommendation.label" id="hideReviewRecommendationEnabled" checked=$hideReviewRecommendationEnabled}
		{/fbvFormSection}

		{fbvFormSection title="plugins.generic.pprPlugin.settings.section.submissions" list="true"}
			{fbvElement type="checkbox" name="submissionCustomFieldsEnabled" label="plugins.generic.pprPlugin.settings.submissionCustomFields.label" id="submissionCustomFieldsEnabled" checked=$submissionCustomFieldsEnabled}
		{/fbvFormSection}

		{fbvFormSection title="plugins.generic.pprPlugin.settings.section.tasks" list="true"}
			{fbvElement type="checkbox" name="reviewReminderEditorEnabled" label="plugins.generic.pprPlugin.settings.reviewReminderEditorEnabled.label" id="reviewReminderEditorEnabled" checked=$reviewReminderEditorEnabled}
			<div class="subsection">
				{fbvElement type="text" name="reviewReminderEditorDaysFromDueDate" label="plugins.generic.pprPlugin.settings.reviewReminderEditorDaysFromDueDate.label" id="reviewReminderEditorDaysFromDueDate" value=$reviewReminderEditorDaysFromDueDate}
			</div>
		<div class="subsection">
			{fbvElement type="checkbox" name="reviewReminderEditorReset" label="plugins.generic.pprPlugin.settings.reviewReminderEditorReset.label" id="reviewReminderEditorReset"}
		</div>
		{/fbvFormSection}

		{fbvFormButtons}
		<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
	</div>
</form>
