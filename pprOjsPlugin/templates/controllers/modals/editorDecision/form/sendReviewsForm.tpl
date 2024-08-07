{** TRICK TO LOAD THE OJS TEMPLATE WITH THE SAME NAME *}
{** THIS WILL ALLOW TO ADD DATA BEFORE OR AFTER A TEMPLATE WITHOUT OVERRIDING ITS CONTENTS *}
{include file="controllers/modals/editorDecision/form/sendReviewsForm.tpl.load_ojs"}

{if $pprPluginSettings->hideReviewRoundSelectionEnabled() && $decision === constant('SUBMISSION_EDITOR_DECISION_PENDING_REVISIONS')}
	<script type="text/javascript">
		{** HIDE REQUIRE REVISIONS ROUND SECTION *}
		{** ISSUE 085 *}
		$('form#sendReviews [id="decisionRevisions"]').closest('div').hide();
	</script>
{/if}

{if $pprPluginSettings->hideSendToReviewersEnabled() && in_array($decision, [constant('SUBMISSION_EDITOR_DECISION_PENDING_REVISIONS'), constant('SUBMISSION_EDITOR_DECISION_DECLINE')])}
	<script type="text/javascript">
		{** HIDE SEND TO REVIEWERS SECTION *}
		{** ISSUE 072 *}
		$('form#sendReviews [id="bccReviewers[]"]').closest('div').hide();
	</script>
{/if}

{** ISSUE 061 *}
{if $pprPluginSettings->submissionRequestRevisionsFileValidationEnabled() && $decision === constant('SUBMISSION_EDITOR_DECISION_PENDING_REVISIONS')}
	{** ONLY ADD FILE VALIDATION FOR REQUEST REVISIONS ACTION *}
	{assign var="modalId" value="requestRevisionsFileMessage"|uniqid|escape}
	{include file="ppr/modalMessage.tpl" cancelButton=true modalId=$modalId
		modalHeader="revisions.ppr.review.files.validation.header"|translate
		modalDescription="revisions.ppr.review.files.validation.description"|translate
		modalButtonOk="revisions.ppr.review.files.validation.button.ok"|translate
		modalButtonCancel="revisions.ppr.review.files.validation.button.cancel"|translate}
	<script type="text/javascript">
		$(function (){ldelim}
			{** FILE VALIDATION LOGIC *}
			$('#{$modalId} .modalButtonOk').click(function() {ldelim}
				$('#sendReviews button[type=submit]').trigger('click', [true]);
				{rdelim});

			{** JS FUNCTION TO CHECK FOR REVIEW FILES BEiNG SELECTED WHEN REQUESTING REVISIONS *}
			$('#sendReviews button[type=submit]').on('click', (event, continueWithSubmission) => {ldelim}
				if (continueWithSubmission) {
					return;
				}

				const modalId = '#{$modalId}'

				let attachmentsAvailable = false;
				let attachmentSelected = false;
				$('input[type=checkbox][name="selectedAttachments[]"]').each(function(index, element) {ldelim}
					attachmentsAvailable = true;
					if (element.checked) {
						attachmentSelected = true;
					}
					{rdelim});

				if (attachmentsAvailable && !attachmentSelected) {ldelim}
					event.preventDefault();
					$(modalId).appendTo('body');
					$(modalId).show();
					{rdelim}

				{rdelim});
			{rdelim});
	</script>
{/if}