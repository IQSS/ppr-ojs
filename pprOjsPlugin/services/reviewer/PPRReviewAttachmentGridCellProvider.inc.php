<?php

import('lib.pkp.classes.controllers.grid.DataObjectGridCellProvider');

/**
 * Provider to add review file owner and sent flag to review files attachment component
 */
class PPRReviewAttachmentGridCellProvider extends DataObjectGridCellProvider {

    /**
     * Extracts variables for a given column from a data element
     * so that they may be assigned to template before rendering.
     * @param $row GridRow
     * @param $column GridColumn
     * @return array
     */
    function getTemplateVarsFromRowColumn($row, $column) {
        $submissionFile = $row->getData()['submissionFile'];
        $columnId = $column->getId();
        assert(is_a($submissionFile, 'DataObject') && !empty($columnId));

        //DEFAULT TO UNKNOWN IN CASE OTHER FILE TYPES APPEAR IN THIS HANDLER
        $label = '<span class="ppr-span-block ppr-attachments-new">' . __('review.ppr.files.status.unknown') . '</span>';
        if($submissionFile->getFileStage() == SUBMISSION_FILE_REVIEW_FILE) {
            // SUBMISSION FILE SHOULD NOT DISPLAY THE new/sent FLAG
            $label = '<span class="ppr-span-block">' . __('review.ppr.files.type.submission') . '</span>';
        } elseif($submissionFile->getFileStage() == SUBMISSION_FILE_REVIEW_ATTACHMENT) {
            //REVIEW FILE
            $label = '<span class="ppr-span-block ppr-attachments-new">' . __('review.ppr.files.status.new') . '</span>';
            if($submissionFile->getViewable()) {
                $label = '<span class="ppr-span-block ppr-attachments-sent">' . __('review.ppr.files.status.sent') . '</span>';
            }
        }

        $reviewerId = $submissionFile->getUploaderUserId();
        $userDao = DAORegistry::getDAO('UserDAO');
        $reviewer = $userDao->getById($reviewerId);
        $reviewerLabel = '';
        if ($reviewer) {
            $reviewerLabel = '<span class="ppr-span-block ppr-attachments-reviewer">' . $reviewer->getLocalizedGivenName() . '</span>';
        }

        return array('label' => $label . $reviewerLabel);
    }
}


