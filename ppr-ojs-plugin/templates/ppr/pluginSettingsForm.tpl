
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

		{fbvFormSection title="plugins.generic.pprPlugin.settings.featureFlags.title" list="true"}
			{fbvElement type="checkbox" name="displayContributorsEnabled" label="plugins.generic.pprPlugin.settings.contributors.label" id="displayContributorsEnabled" checked=$displayContributorsEnabled}
			{fbvElement type="checkbox" name="displaySuggestedReviewersEnabled" label="plugins.generic.pprPlugin.settings.reviewers.label" id="displaySuggestedReviewersEnabled" checked=$displaySuggestedReviewersEnabled}
		{/fbvFormSection}



		{fbvFormButtons}
		<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
	</div>
</form>
