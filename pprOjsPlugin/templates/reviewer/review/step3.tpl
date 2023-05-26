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
    {if $pprPluginSettings->hideReviewRecommendationEnabled()}
        <!-- DEFAULT REVISIONS REQUIRED -->
        <input type="hidden" name="recommendation" value="{constant('SUBMISSION_REVIEWER_RECOMMENDATION_PENDING_REVISIONS')}" />
    {else}
        {include file="reviewer/review/reviewerRecommendations.tpl"}
    {/if}
{/capture}

{if $pprPluginSettings->submissionCustomFieldsEnabled()}
    <!-- SHOW COMMENTS FOR REVIEWER DATA -->
    <div class="pkp_notification">
        {include file="controllers/notification/inPlaceNotificationContent.tpl" notificationId="pprCommentsForReviewer"
        notificationStyleClass="notifyInfo" notificationContents=$commentsForReviewer
        notificationTitle="{translate key="submission.comments.reviewer"}"}
    </div>
{/if}

{include file="core:reviewer/review/step3.tpl"}
