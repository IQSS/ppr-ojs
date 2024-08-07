{**
 * templates/controllers/grid/user/reviewer/form/reviewerFormFooter.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * The non-searching part of the add reviewer form
 *
 *}
<div id="reviewerFormFooter" class="reviewerFormFooterContainer">
	<!--  message template choice -->
	{if $templates|@count == 1}
		{foreach from=$templates item=template key=templateKey}
			<input type="hidden" name="template" value="{$templateKey|escape}"/>
		{/foreach}
	{else}
		{fbvFormSection title="stageParticipants.notify.chooseMessage" for="template" size=$fbvStyles.size.medium}
			{fbvElement type="select" from=$templates translate=false id="template"}
		{/fbvFormSection}
	{/if}

	<!--  Message to reviewer textarea -->
	{fbvFormSection title="editor.review.personalMessageToReviewer" for="personalMessage"}
		{fbvElement type="textarea" name="personalMessage" id="personalMessage" value=$personalMessage variables=$emailVariables rich=true rows=25}
	{/fbvFormSection}

	<!-- skip email checkbox -->
	{fbvFormSection for="skipEmail" size=$fbvStyles.size.MEDIUM list=true}
		{fbvElement type="checkbox" id="skipEmail" name="skipEmail" label="editor.review.skipEmail"}
	{/fbvFormSection}

	{fbvFormSection title="editor.review.importantDates"}
		{fbvElement type="text" id="responseDueDate" name="responseDueDate" label="submission.task.responseDueDate" value=$responseDueDate inline=true size=$fbvStyles.size.MEDIUM class="datepicker"}
		{fbvElement type="text" id="reviewDueDate" name="reviewDueDate" label="editor.review.reviewDueDate" value=$reviewDueDate inline=true size=$fbvStyles.size.MEDIUM class="datepicker"}
	{/fbvFormSection}

	{include file="controllers/grid/users/reviewer/form/noFilesWarning.tpl"}

	{capture assign="extraContent"}
		<!-- Available review files -->
		{capture assign=limitReviewFilesGridUrl}{url router=$smarty.const.ROUTE_COMPONENT component="grid.files.review.LimitReviewFilesGridHandler" op="fetchGrid" submissionId=$submissionId stageId=$stageId reviewRoundId=$reviewRoundId escape=false}{/capture}
		{load_url_in_div id="limitReviewFilesGrid" url=$limitReviewFilesGridUrl}
	{/capture}
	<div id="filesAccordian" class="section">
		{include file="controllers/extrasOnDemand.tpl"
			id="filesAccordianController"
			widgetWrapper="#filesAccordian"
			moreDetailsText="editor.submissionReview.restrictFiles"
			lessDetailsText="editor.submissionReview.restrictFiles.hide"
			extraContent=$extraContent
		}
	</div>

	{if $pprPluginSettings->hideReviewMethodEnabled()}
		{** PPR - DEFAULT TO DOUBLE ANONYMOUS  - ISSUE 024 **}
		<input type="hidden" name="reviewMethod" value="{constant('SUBMISSION_REVIEW_METHOD_DOUBLEANONYMOUS')}" />
	{else}
		{fbvFormSection list=true title="editor.submissionReview.reviewType"}
		{foreach from=$reviewMethods key=methodId item=methodTranslationKey}
			{assign var=elementId value="reviewMethod"|concat:"-"|concat:$methodId}
			{if $reviewMethod == $methodId}
				{assign var=elementChecked value=true}
			{else}
				{assign var=elementChecked value=false}
			{/if}
			{fbvElement type="radio" name="reviewMethod" id=$elementId value=$methodId checked=$elementChecked label=$methodTranslationKey}
		{/foreach}
		{/fbvFormSection}
	{/if}

	{if count($reviewForms)>0}
		{** PPR - RESEARCH TYPE CUSTOM FIELD **}
		{fbvFormSection title="submission.research.type"}
			{$submissionResearchType}
		{/fbvFormSection}

		{** PPR - REMOVED DEFAULT OPTION **}
		{** ISSUE 066 **}
		{if !$pprPluginSettings->hideReviewFormDefaultEnabled()}
			{assign var=defaultLabel value="manager.reviewForms.noneChosen"|translate}
			{assign var=defaultValue value=0}
		{/if}
		{fbvFormSection title="submission.reviewForm"}
			{fbvElement type="select" name="reviewFormId" id="reviewFormId" defaultLabel=$defaultLabel defaultValue=$defaultValue translate=false from=$reviewForms selected=$reviewFormId}
		{/fbvFormSection}
	{/if}

	<!-- All of the hidden inputs -->
	<input type="hidden" name="selectionType" value="{$selectionType|escape}" />
	<input type="hidden" name="submissionId" value="{$submissionId|escape}" />
	<input type="hidden" name="stageId" value="{$stageId|escape}" />
	<input type="hidden" name="reviewRoundId" value="{$reviewRoundId|escape}" />
</div>
