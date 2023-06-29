{** TRICK TO LOAD THE OJS TEMPLATE WITH THE SAME NAME *}
{** THIS WILL ALLOW TO ADD DATA BEFORE OR AFTER A TEMPLATE WITHOUT OVERRIDING ITS CONTENTS *}
{include file="controllers/modals/editorDecision/form/sendReviewsForm.tpl.load_ojs"}

<script type="text/javascript">
	$(function (){ldelim}
		{** JS FUNCTION TO CHECK FOR REVIEW FILES BEiNG SELECTED WHEN REQUESTING REVISIONS *}
		$('#sendReviews button[type=submit]').on('click', (event) => {ldelim}
			let attachmentsAvailable = false;
			let attachmentSelected = false;
			$('input[type=checkbox][name="selectedAttachments[]"]').each(function(index, element) {
				attachmentsAvailable = true;
				if (element.checked) {
					attachmentSelected = true;
				}
			});

			if (attachmentsAvailable && !attachmentSelected) {
				const continueWithSubmission = confirm("{translate key="revisions.ppr.review.files.validation.message" }");
				if (!continueWithSubmission) {
					event.preventDefault();
				}
			}

		{rdelim});
	{rdelim});
</script>