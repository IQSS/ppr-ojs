<?php

import ('classes.submission.SubmissionFileDAO');

class PPRSubmissionFileDAO extends SubmissionFileDAO {

    /**
     * Add updated date to all SubmissionFile updates
     */
    public function updateObject($submissionFile)	{
        $request = Application::get()->getRequest();
        if (isset($request->_router->_component) && $request->_router->_component === 'modals.editorDecision.EditorDecisionHandler') {
            $submissionFile->setData('updatedAt', Core::getCurrentDate());
        }

        $submissionFile->setData('updatedAt', Core::getCurrentDate());


        return parent::updateObject($submissionFile);
    }
}