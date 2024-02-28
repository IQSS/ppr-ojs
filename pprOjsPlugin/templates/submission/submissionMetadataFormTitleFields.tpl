{**
 * templates/submission/submissionMetadataFormTitleFields.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Submission's metadata form title fields. To be included in any form that wants to handle
 * submission metadata.
 *}
{if $formParams.submissionVersion && ($formParams.submissionVersion < $currentSubmissionVersion)}
  {assign var=readOnly value=1}
{else}
	{assign var=readOnly value=0}
{/if}
{fbvElement type="hidden" name="submissionVersion" id="submissionVersion" value=$formParams.submissionVersion}

{* PPR CUSTOM FIELD researchType AND REMOVE prefix *}
{if $pprPluginSettings->submissionResearchTypeEnabled()}
	{fbvFormSection title="submission.research.type"}
		{fbvElement type="select" name="researchType" id="researchType" label="submission.research.type.description" required=true  disabled=$readOnly defaultLabel="" defaultValue="" from=$researchTypes selected=$researchType translate=false}
	{/fbvFormSection}
{/if}
{* PPR REMOVE prefix *}
{if $pprPluginSettings->submissionHidePrefixEnabled()}
	{fbvFormSection for="title" title="common.title" required=true}
		{fbvElement type="text" multilingual=true name="title" id="title" value=$title readonly=$readOnly maxlength="255" required=true}
	{/fbvFormSection}
{else}
	<div class="pkp_helpers_clear">
		{fbvFormSection for="title" title="common.prefix" inline="true" size=$fbvStyles.size.SMALL}
			{fbvElement label="common.prefixAndTitle.tip" type="text" multilingual=true name="prefix" id="prefix" value=$prefix readonly=$readOnly maxlength="32"}
		{/fbvFormSection}
		{fbvFormSection for="title" title="common.title" inline="true" size=$fbvStyles.size.LARGE required=true}
			{fbvElement type="text" multilingual=true name="title" id="title" value=$title readonly=$readOnly maxlength="255" required=true}
		{/fbvFormSection}
	</div>
{/if}

{fbvFormSection title="common.subtitle" for="subtitle"}
	{fbvElement type="text" multilingual=true name="subtitle" id="subtitle" value=$subtitle readonly=$readOnly}
{/fbvFormSection}
{fbvFormSection title="common.abstract" for="abstract" required=$abstractsRequired}
	{if $wordCount}
		<p class="pkp_help">{translate key="submission.abstract.wordCount.description" wordCount=$wordCount}
	{/if}
	{fbvElement type="textarea" multilingual=true name="abstract" id="abstract" value=$abstract rich="extended" readonly=$readOnly wordCount=$wordCount}
{/fbvFormSection}

{* PPR CUSTOM FIELD commentsForReviewer *}
{if $pprPluginSettings->submissionCommentsForReviewerEnabled()}
	{fbvFormSection title="submission.comments.reviewer"}
		{fbvElement type="textarea" height=$fbvStyles.height.SHORT multilingual=true name="commentsForReviewer" id="commentsForReviewer" label="submission.comments.reviewer.description" readonly=$readOnly value=$commentsForReviewer|replace:'<br />':'' }
	{/fbvFormSection}
{/if}
{* PPR CUSTOM FIELD emailCoauthors *}
{if $pprPluginSettings->emailContributorsEnabled()}
	{fbvFormSection title="submission.email.contributors.title" list="true"}
		{capture assign="emailContributorsLabel"}{translate key="submission.email.contributors.label"}{/capture}
		{fbvElement type="checkbox" id="emailContributors" value=1 label=$emailContributorsLabel translate=false checked=$emailContributors}
	{/fbvFormSection}
{/if}