<?php

import('lib.pkp.controllers.grid.users.userSelect.UserSelectGridHandler');
require_once(dirname(__FILE__) . '/PPRUserSelectGridCellProvider.inc.php');

/**
 *
 */
class PPRUserSelectGridHandler extends UserSelectGridHandler {

    function initialize($request, $args = null) {
        $cellProvider = new PPRUserSelectGridCellProvider();
        $this->addColumn(new GridColumn('select')); //NEEDED TO KEEP THE ORDER
        $this->addColumn(new GridColumn('name')); //NEEDED TO KEEP THE ORDER

        $this->addColumn(
            new GridColumn(
                'onLeave',
                null,
                null,
                null,
                $cellProvider,
                array('anyhtml' => true, 'alignment' => COLUMN_ALIGNMENT_RIGHT, 'width' => 12)
            )
        );

        parent::initialize($request, $args);
    }
}