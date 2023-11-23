<?php

import('lib.pkp.classes.controllers.grid.DataObjectGridCellProvider');

/**
 * This is based on PKPAuthorGridCellProvider
 */
class PPRAuthorGridCellProvider extends DataObjectGridCellProvider {

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
        $author = $row->getData();
        $columnId = $column->getId();

        assert(is_a($author, 'DataObject') && !empty($columnId));
        switch ($columnId) {
            case 'institution':
                return array('label' => $author->getLocalizedAffiliation());
            case 'category':
                return array('label' => $author->getData('category'));
            case 'department':
                return array('label' =>$author->getData('department'));
        }
    }
}


