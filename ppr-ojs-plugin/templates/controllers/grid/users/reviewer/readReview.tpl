{**
 * templates/controllers/grid/users/reviewer/readReview.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Screen to let user read a review.
 *
 *}
{capture assign="reviewerRecommendations"}
	<!-- DEFAULT REVISIONS REQUIRED -->
	<input type="hidden" name="recommendation" value="{constant('SUBMISSION_REVIEWER_RECOMMENDATION_PENDING_REVISIONS')}" />
{/capture}

<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#readReviewForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

{include file="core:controllers/grid/users/reviewer/readReview.tpl"}
