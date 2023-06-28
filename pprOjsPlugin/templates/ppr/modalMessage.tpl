{assign var="uuid" value=""|uniqid|escape}
<script type="text/javascript">
    $(function (){ldelim}
        const overlayId = '#fileValidationMessage{$uuid}';
        {** CLOSE OVERLAY BUTTON *}
        $('#fileValidationMessageClose').click(function() {ldelim}
            $(overlayId).hide();
        {rdelim});

        {** CLOSE OVERLAY WHEN CLICK OUTSIDE MODAL *}
        $(overlayId).click(function(event) {ldelim}
            const $container = $('.modalContainer');
            // if the target of the click isn't the container nor a descendant of the container
            if (!$container.is(event.target) && $container.has(event.target).length === 0) {ldelim}
                $(overlayId).hide();
            {rdelim}
        {rdelim});

        {** JS FUNCTION TO CHECK FOR REVIEW UPLOADED FILES WHEN MAKING A SUBMISSION *}
        $('#submitStep2Form button[type=submit]').on('click', function(event) {ldelim}
            let filesUploaded = true;
            if ($('#submission-files-container div.listPanel__empty').length) {ldelim}
                // EXPECTED MESSAGE WHEN NO FILES UPLOADED
                filesUploaded = false;
            {rdelim}

            if (!filesUploaded) {ldelim}
                event.preventDefault();
                $(overlayId).show();
            {rdelim}

        {rdelim});

    {rdelim});
</script>
<div id="fileValidationMessage{$uuid}" class="fullPageModalOverlay" style="display: none;">
    <div class="modalContainer">
        <div class="modalContent">
            <div class="modalHeader">{$modalHeader}</div>
            <div class="modalDescription">{$modalDescription}</div>
        </div>
        <div class="modalButtons">
            <button id="fileValidationMessageClose" class="pkp_button">{$modalButton}</button>
        </div>
    </div>
</div>