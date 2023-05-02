<div id="{$id}" class="pkp_notification">
    {capture assign=messageContent}
		Please return to the "Review" tab to view the peer pre-review of your submission.
    {/capture}
    {include file="controllers/notification/inPlaceNotificationContent.tpl" notificationId="invalidTabMessage"
    notificationStyleClass="notifyInfo" notificationContents=$messageContent
    notificationTitle="Review Completed"}
</div>
