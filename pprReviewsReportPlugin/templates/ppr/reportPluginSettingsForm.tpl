
<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#pprReportPluginSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<style>
form#pprReportPluginSettingsForm div.subsection {
	padding-top: 1em;
}
</style>


<form class="pkp_form" id="pprReportPluginSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="reports" plugin=$pluginName verb="settings" save=true}">
	<div id="pprProfileSettings">

		<p id="description">
			{translate key="plugins.reports.pprReviewsPlugin.settings.description" }
		</p>

		{csrf}
		{include file="controllers/notification/inPlaceNotification.tpl" notificationId="pprReportPluginSettingsFormNotification"}

		<h2>{translate key="plugins.reports.pprReviewsPlugin.settings.section.featureFlags"}</h2>

		{fbvFormSection title="plugins.reports.pprReviewsPlugin.settings.section.tasks" list="true"}
			{fbvElement type="checkbox" name="submissionsReviewsReportEnabled" label="plugins.reports.pprReviewsPlugin.settings.submissionsReviewsReportEnabled.label" id="submissionsReviewsReportEnabled" checked=$submissionsReviewsReportEnabled}
			<div class="subsection">
				{fbvElement type="text" name="submissionsReviewsReportRecipients" label="plugins.reports.pprReviewsPlugin.settings.submissionsReviewsReportRecipients.label" id="submissionsReviewsReportRecipients" value=$submissionsReviewsReportRecipients}
			</div>
			<div class="subsection">
				{fbvElement type="checkbox" name="scheduledTasksReset" label="plugins.reports.pprReviewsPlugin.settings.scheduledTasksReset.label" id="scheduledTasksReset"}
			</div>
		{/fbvFormSection}

		{fbvFormButtons}
		<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
	</div>
</form>
