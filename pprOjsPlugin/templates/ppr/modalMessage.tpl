<script type="text/javascript">
    $(function (){ldelim}
        const overlayId = '#{$modalId}';
        {** CLICKING ANY BUTTONS WILL HIDE OVERLAY*}
        $('#{$modalId} .modalButtons button').click(function() {ldelim}
            $(overlayId).hide();
        {rdelim});

        {** CLOSE OVERLAY WHEN CLICK OUTSIDE MODAL *}
        $(overlayId).click(function(event) {ldelim}
            event.preventDefault();
            const $container = $('#{$modalId} .modalContainer');
            // if the target of the click isn't the container nor a descendant of the container
            if (!$container.is(event.target) && $container.has(event.target).length === 0) {ldelim}
                event.preventDefault();
                $(overlayId).hide();
            {rdelim}
        {rdelim});

    {rdelim});
</script>
<div id="{$modalId}" class="fullPageModalOverlay" style="display: none;">
    <div class="modalContainer">
        <div class="modalContent">
            <div class="modalHeader">{$modalHeader}</div>
            <div class="modalDescription">{$modalDescription}</div>
        </div>
        <div class="modalButtons">
            <button class="pkp_button modalButtonOk">{$modalButtonOk}</button>
            {if $cancelButton}
                <button class="pkp_button modalButtonCancel">{$modalButtonCancel}</button>
            {/if}
        </div>
    </div>
</div>