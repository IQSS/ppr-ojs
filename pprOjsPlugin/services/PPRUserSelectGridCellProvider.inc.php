<?php

use function PHP81_BC\strftime;

import('lib.pkp.classes.controllers.grid.DataObjectGridCellProvider');

/**
 * This is based on UserSelectGridCellProvider
 */
class PPRUserSelectGridCellProvider extends DataObjectGridCellProvider {

    const EMPTY_LABEL = ['label' => ''];

    //
    // Template methods from GridCellProvider
    //
    /**
     * Extracts variables for a given column from a data element
     * so that they may be assigned to template before rendering.
     * @param $row GridRow
     * @param $column GridColumn
     * @return array
     */
    function getTemplateVarsFromRowColumn($row, $column) {
        $user = $row->getData();
        $columnId = $column->getId();

        assert(is_a($user, 'DataObject') && !empty($columnId));

        $onLeaveFrom = $user->getData('onLeaveFrom');
        $onLeaveTo = $user->getData('onLeaveTo');

        if (!$onLeaveFrom || !$onLeaveTo) {
            return self::EMPTY_LABEL;
        }

        $onLeaveFromDate = strtotime($onLeaveFrom);
        $onLeaveToDate = strtotime($onLeaveTo);
        $nowDate = time();
        if ($onLeaveFromDate <= $nowDate && $nowDate <= $onLeaveToDate) {
            $onLeaveBadge = '<span class="pkpBadge pprBadge--onLeave">' . __('user.onLeave.badge.label', ['onLeaveTo' => $onLeaveTo]) . '</span> ';
            return array('label' => $onLeaveBadge);
        }

        return self::EMPTY_LABEL;
    }
}


