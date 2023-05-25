
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
		{/fbvFormSection}
		{fbvFormSection}
			{fbvElement type="text" name="categoryOptions" label="plugins.generic.pprPlugin.settings.categoryOptions.label" id="categoryOptions" value=$categoryOptions}
		{/fbvFormSection}

		{fbvFormSection title="plugins.generic.pprPlugin.settings.section.reviews" list="true"}
			{fbvElement type="checkbox" name="hideReviewMethodEnabled" label="plugins.generic.pprPlugin.settings.reviewMethod.label" id="hideReviewMethodEnabled" checked=$hideReviewMethodEnabled}
			{fbvElement type="checkbox" name="hideReviewRecommendationEnabled" label="plugins.generic.pprPlugin.settings.reviewRecommendation.label" id="hideReviewRecommendationEnabled" checked=$hideReviewRecommendationEnabled}
		{/fbvFormSection}



		{fbvFormButtons}
		<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
	</div>
</form>
