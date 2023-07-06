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
                array('anyhtml' => true, 'alignment' => COLUMN_ALIGNMENT_RIGHT, 'width' => 5)
            )
        );

        parent::initialize($request, $args);
    }

    function addColumn_bak($column) {
        //OVERRIDE THE NAME COLUMN
        if (in_array($column->getId(), ['name'])) {
            $cellProvider = new PPRUserSelectGridCellProvider();
            $column->setCellProvider($cellProvider);
            $column->addFlag('anyhtml', true);
        }

        parent::addColumn($column);
    }
}