{**
 * templates/submission/form/step4.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Step 4 of author submission.
 *}
<script type="text/javascript">
    $(function() {ldelim}
        // Attach the JS form handler.
        $('#submitStep4Form').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
        {rdelim});
</script>

{**
  PPR REQUIREMENT TO ADD SUBMISSION CHECKLIST TO THE CONFIRMATION PAGE - ISSUE 022
*}
{* Submission checklist *}
{if $currentContext->getLocalizedData('submissionChecklist')}
    {fbvFormSection list="true" label="submission.ppr.confirmation.intro" id="ppr_confirmation_submissionChecklist"}
        {foreach name=checklist from=$currentContext->getLocalizedData('submissionChecklist') key=checklistId item=checklistItem}
            <li>
                <label>
                    {$checklistItem.content|nl2br}
                </label>
            </li>
        {/foreach}
    {/fbvFormSection}
{/if}

<form class="pkp_form" id="submitStep4Form" method="post" action="{url op="saveStep" path=$submitStep}">
    {csrf}
    <input type="hidden" name="submissionId" value="{$submissionId|escape}" />
    {include file="controllers/notification/inPlaceNotification.tpl" notificationId="submitStep4FormNotification"}

    <p>{translate key="submission.ppr.confirmation.message"}</p>

    {fbvFormButtons id="step4Buttons" submitText="submission.submit.finishSubmission" confirmSubmit="submission.confirmSubmit"}
</form>