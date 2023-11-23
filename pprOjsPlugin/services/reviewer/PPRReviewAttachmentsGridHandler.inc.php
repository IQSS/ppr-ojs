<?php

import('lib.pkp.controllers.grid.files.attachment.EditorSelectableReviewAttachmentsGridHandler');
require_once(dirname(__FILE__) . '/PPRReviewAttachmentGridCellProvider.inc.php');

/**
 * Overrides the Review Attachments component to add the reviewer column.
 */
class PPRReviewAttachmentsGridHandler extends EditorSelectableReviewAttachmentsGridHandler {

    function initialize($request, $args = null) {
        $cellProvider = new PPRReviewAttachmentGridCellProvider();
        $this->addColumn(new GridColumn('select')); //NEEDED TO KEEP THE ORDER
        $this->addColumn(new GridColumn('name')); //NEEDED TO KEEP THE ORDER
        $this->addColumn(
            new GridColumn(
                'reviewer',
                'review.ppr.files.column.reviewer',
                null,
                null,
                $cellProvider,
                array('width' => 40, 'alignment' => COLUMN_ALIGNMENT_LEFT, 'anyhtml' => true)
            )
        );

        parent::initialize($request, $args);
    }

    /**
     * This is the only way that I have found to modify the columns in the EditorSelectableReviewAttachmentsGridHandler without reimplementing the handler.
     */
    function addColumn($column) {
        // EXCLUDE THE COLUMNS FROM EditorSelectableReviewAttachmentsGridHandler THAT WE DO NOT WANT
        if (in_array($column->getId(), ['type'])) {
            return;
        }

        parent::addColumn($column);
    }

    /**
     * All review files from the list should not be selected.
     */
    function isDataElementSelected($gridDataElement) {
        return false;
    }
}