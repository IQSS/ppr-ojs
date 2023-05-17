<?php

import('lib.pkp.classes.controllers.grid.DataObjectGridCellProvider');

/**
 * This is based on PKPAuthorGridCellProvider
 */
class PPRAuthorGridCellProvider extends DataObjectGridCellProvider {

    /** @var Publication The publication this author is related to */
    private $_publication;

    /**
     * Constructor
     *
     * @param Publication $publication
     */
    public function __construct($publication) {
        $this->_publication = $publication;
    }

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
        $element = $row->getData();
        $columnId = $column->getId();
        assert(is_a($element, 'DataObject') && !empty($columnId));
        assert($columnId == 'institution');
        return array('label' => $element->getLocalizedAffiliation());
    }
}


