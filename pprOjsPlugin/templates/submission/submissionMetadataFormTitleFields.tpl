<!-- TRICK TO LOAD THE OJS TEMPLATE WITH THE SAME NAME -->
<!-- THIS WILL ALLOW TO ADD DATA BEFORE OR AFTER A TEMPLATE WITHOUT OVERRIDING ITS CONTENTS -->
{include file="submission/submissionMetadataFormTitleFields.tpl.load_ojs"}

{fbvFormSection title="submission.comments.reviewer"}
    {fbvElement type="textarea" height=$fbvStyles.height.SHORT multilingual=true name="commentsForReviewer" id="commentsForReviewer" label="submission.comments.reviewer.description" disabled=$readOnly value=$commentsForReviewer|replace:'<br />':'' }
{/fbvFormSection}