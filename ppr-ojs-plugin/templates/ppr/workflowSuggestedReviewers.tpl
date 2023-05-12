<div id="pprSuggestedReviewers">
    <div class="pkp_notification">
        {include file="controllers/notification/inPlaceNotificationContent.tpl" notificationId="pprReviewersToInclude"
        notificationStyleClass="notifyInfo" notificationContents=$submission->getCurrentPublication()->getLocalizedData('recommendedReviewers')
        notificationTitle="{translate key="editor.submission.recommendedReviewers.title"}"}
    </div>
    <div class="pkp_notification">
        {include file="controllers/notification/inPlaceNotificationContent.tpl" notificationId="pprReviewersToExclude"
        notificationStyleClass="notifyInfo" notificationContents=$submission->getCurrentPublication()->getLocalizedData('excludedReviewers')
        notificationTitle="{translate key="editor.submission.excludedReviewers.title"}"}
    </div>
</div>