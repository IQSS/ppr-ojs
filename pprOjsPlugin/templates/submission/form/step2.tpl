{** TRICK TO LOAD THE OJS TEMPLATE WITH THE SAME NAME *}
{** THIS WILL ALLOW TO ADD DATA BEFORE OR AFTER A TEMPLATE WITHOUT OVERRIDING ITS CONTENTS *}
{include file="submission/form/step2.tpl.load_ojs"}

{assign var="modalId" value="uploadSubmissionFileMessage"|uniqid|escape}
{capture assign="modalHeader"}{translate key="submission.ppr.files.validation.header"}{/capture}
{capture assign="modalDescription"}{translate key="submission.ppr.files.validation.description"}{/capture}
{capture assign="modalButton"}{translate key="submission.ppr.files.validation.button"}{/capture}
{include file="ppr/modalMessage.tpl" modalId=$modalId modalHeader=$modalHeader modalDescription=$modalDescription modalButton=$modalButton}
<script type="text/javascript">
    $(function (){ldelim}
        {** JS FUNCTION TO CHECK FOR UPLOADED FILES WHEN MAKING A SUBMISSION *}
        $('#submitStep2Form button[type=submit]').on('click', function(event) {ldelim}
            const modalId = '#{$modalId}'
            let filesUploaded = true;
            if ($('#submission-files-container div.listPanel__empty').length) {ldelim}
                // EXPECTED MESSAGE WHEN NO FILES UPLOADED
                filesUploaded = false;
            {rdelim}

            if (!filesUploaded) {ldelim}
                event.preventDefault();
                $(modalId).show();
            {rdelim}
    
        {rdelim});
    {rdelim});
</script>