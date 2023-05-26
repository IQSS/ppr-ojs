<div id="pprCommentsForReviewer">
    <!-- SHOW COMMENTS FOR REVIEWER DATA -->
    <div class="pkp_notification">
        {include file="controllers/notification/inPlaceNotificationContent.tpl" notificationId="pprCommentsForReviewer"
        notificationStyleClass="notifyInfo" notificationContents=nl2br($submission->getCurrentPublication()->getLocalizedData('commentsForReviewer'))
        notificationTitle="{translate key="submission.comments.reviewer"}"}
    </div>
</div>