{** TRICK TO LOAD THE OJS TEMPLATE WITH THE SAME NAME *}
{** THIS WILL ALLOW TO ADD DATA BEFORE OR AFTER A TEMPLATE WITHOUT OVERRIDING ITS CONTENTS *}
{include file="submission/form/complete.tpl.load_ojs"}

{if $showPPRAuthorSurvey}
	<div class="ppr-survey">
		{$pprPluginSettings->authorSubmissionSurveyHtml()}
	</div>
{/if}
