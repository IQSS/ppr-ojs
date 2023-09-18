{** TRICK TO LOAD THE OJS TEMPLATE WITH THE SAME NAME *}
{** THIS WILL ALLOW TO ADD DATA BEFORE OR AFTER A TEMPLATE WITHOUT OVERRIDING ITS CONTENTS *}
{include file="reviewer/review/reviewCompleted.tpl.load_ojs"}

{if $pprPluginSettings->reviewerSurveyHtml()}
    <div class="ppr-survey">
        {$pprPluginSettings->reviewerSurveyHtml()}
    </div>
{/if}