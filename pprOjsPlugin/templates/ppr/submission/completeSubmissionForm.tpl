
<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#pprSubmissionActionForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>


<form class="pkp_form" id="pprSubmissionActionForm" method="post" action="{url component="pprPlugin.services.CompleteSubmissionHandler" op=$pprActionType submissionId=$submissionId}">
	<div id="pprCompleteSubmission">
		<p>
			{translate key="submission.{$pprActionType}.form.description" }
		</p>

		{fbvFormButtons}
	</div>
</form>
