{**
 * templates/reviewer/review/step3.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Show the step 3 review page
 *}

{capture assign="additionalFormFields"}
    <!-- DEFAULT REVISIONS REQUIRED -->
    <input type="hidden" name="recommendation" value="{constant('SUBMISSION_REVIEWER_RECOMMENDATION_PENDING_REVISIONS')}" />
{/capture}

{include file="core:reviewer/review/step3.tpl"}
