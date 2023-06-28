<script type="text/javascript">
    $(function (){ldelim}
        const overlayId = '#{$modalId}';
        {** CLOSE OVERLAY BUTTON *}
        $('#modalCloseButton').click(function() {ldelim}
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

    {rdelim});
</script>
<div id="{$modalId}" class="fullPageModalOverlay" style="display: none;">
    <div class="modalContainer">
        <div class="modalContent">
            <div class="modalHeader">{$modalHeader}</div>
            <div class="modalDescription">{$modalDescription}</div>
        </div>
        <div class="modalButtons">
            <button id="modalCloseButton" class="pkp_button">{$modalButton}</button>
        </div>
    </div>
</div>