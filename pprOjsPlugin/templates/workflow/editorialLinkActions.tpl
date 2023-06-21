{** TRICK TO LOAD THE OJS TEMPLATE WITH THE SAME NAME *}
{** THIS WILL ALLOW TO ADD DATA BEFORE OR AFTER A TEMPLATE WITHOUT OVERRIDING ITS CONTENTS *}
{include file="workflow/editorialLinkActions.tpl.load_ojs"}

{** ADD THE PPR COMPLETE / ACTIVATE BUTTON AFTER THE EDITORIAL LINK ACTIONS *}
{if $pprAction}
    <div id="pprCompleteSubmission" class="pkp_workflow_decisions">
        {include file="linkAction/linkAction.tpl" action=$pprAction contextId=$contextId}
    </div>
{/if}
