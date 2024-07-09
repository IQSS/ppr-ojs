{** TRICK TO LOAD THE OJS TEMPLATE WITH THE SAME NAME *}
{** THIS WILL ALLOW TO ADD DATA BEFORE OR AFTER A TEMPLATE WITHOUT OVERRIDING ITS CONTENTS *}
{include file="submission/form/step2.tpl.load_ojs"}
{** ISSUE 063 *}
{assign var="modalId" value="uploadSubmissionFileMessage"|uniqid|escape}
{include file="ppr/modalMessage.tpl" modalId=$modalId
    modalHeader="submission.ppr.files.validation.header"|translate
    modalDescription="submission.ppr.files.validation.description"|translate
    modalButtonOk="submission.ppr.files.validation.button.ok"|translate}
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