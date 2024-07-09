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

{if $pprPluginSettings->submissionCommentsForReviewerEnabled()}
    <!-- SHOW COMMENTS FOR REVIEWER DATA -->
    <div class="pkp_notification">
        {include file="controllers/notification/inPlaceNotificationContent.tpl" notificationId="pprCommentsForReviewer"
        notificationStyleClass="notifyInfo" notificationContents=$commentsForReviewer
        notificationTitle="{translate key="submission.comments.reviewer"}"}
    </div>
{/if}

{if $pprPluginSettings->reviewUploadFileValidationEnabled()}
    {** ISSUE 067 *}
    {** REVIEW FILE VALIDATION MESSAGE *}
    {assign var="modalId" value="uploadReviewFileMessage"|uniqid|escape}
    {include file="ppr/modalMessage.tpl" modalId=$modalId
        modalHeader="review.ppr.files.validation.header"|translate
        modalDescription="review.ppr.files.validation.description"|translate
        modalButtonOk="review.ppr.files.validation.button.ok"|translate}

    <script type="text/javascript">
        $(function (){ldelim}
            {** JS FUNCTION TO CHECK FOR UPLOADED FILES WHEN MAKING A REVIEW *}
            $('#reviewStep3Form fieldset#reviewStep3 > div.formButtons button.submitFormButton').on('click', function(event) {ldelim}
                let filesUploaded = true;
                if ($('div#reviewAttachmentsGridContainer table tbody.empty:visible').length) {ldelim}
                    // EXPECTED MESSAGE WHEN NO FILES UPLOADED
                    filesUploaded = false;
                {rdelim}

                if (!filesUploaded) {ldelim}
                    {** WE NEED TO STOP THE OJS EVENT ON THE SUBMIT BUTTON TO FIRE WHEN NO FILES UPLOADED*}
                    event.stopImmediatePropagation();
                    event.preventDefault();
                    $('#{$modalId}').show();
                {rdelim}

            {rdelim});
        {rdelim});
    </script>
{/if}

{include file="core:reviewer/review/step3.tpl"}
