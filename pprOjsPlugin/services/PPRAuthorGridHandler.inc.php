<?php

import('lib.pkp.controllers.grid.users.author.AuthorGridHandler');
require_once(dirname(__FILE__) . '/PPRAuthorGridCellProvider.inc.php');

/**
 * Custom AuthorGridHandler to control the columns that are displayed in the contributors component
 */
class PPRAuthorGridHandler extends AuthorGridHandler {

    function initialize($request, $args = null) {
        $cellProvider = new PPRAuthorGridCellProvider();
        $this->addColumn(new GridColumn('name')); //NEEDED TO KEEP THE ORDER AND MAKE INSTITUTION THE SECOND COLUMN
        $this->addColumn(
            new GridColumn(
                'institution',
                'user.affiliation',
                null,
                null,
                $cellProvider,
                array('width' => 30, 'alignment' => COLUMN_ALIGNMENT_LEFT)
            )
        );
        $this->addColumn(
            new GridColumn(
                'category',
                'user.category',
                null,
                null,
                $cellProvider,
                array('width' => 15, 'alignment' => COLUMN_ALIGNMENT_LEFT)
            )
        );
        $this->addColumn(
            new GridColumn(
                'department',
                'user.department',
                null,
                null,
                $cellProvider,
                array('width' => 15, 'alignment' => COLUMN_ALIGNMENT_LEFT)
            )
        );

        parent::initialize($request, $args);
    }

    /**
     * This is the only way that I have found to modify the columns in the AuthorGridHandler without reimplementing the handler.
     */
    function addColumn($column) {
        // EXCLUDE THE COLUMNS FROM AuthorGridHandler THAT WE DO NOT WANT
        if (in_array($column->getId(), ['role', 'includeInBrowse'])) {
            return;
        }

        parent::addColumn($column);
    }

}