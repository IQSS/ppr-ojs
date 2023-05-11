<?php

import('lib.pkp.controllers.grid.users.author.PKPAuthorGridCellProvider');

/**
 * Handler to add/update components in the OJS workflow page
 * Workflow page controls the submission and review workflow
 */
class PPRWorkflowHandler {

    private $_pprPlugin;

    public function __construct($plugin) {
        $this->_pprPlugin = $plugin;
    }


    function register() {
        HookRegistry::register('authorgridhandler::initfeatures', array($this, 'updateContributorsGrid'));
        HookRegistry::register('Template::Workflow', array($this, 'updateWorkflowTemplate'));
    }

    /**
     * Updates to the AuthorGridHandler to add the institution data to the contributors component.
     * @param $hookName
     * @param $hookArgs
     * @return false
     */
    public function updateContributorsGrid($hookName, $hookArgs) {
        $authorGridHandler = $hookArgs[0];
        $this->_pprPlugin->import('controllers.PPRAuthorGridCellProvider');
        $cellProvider = new PPRAuthorGridCellProvider($authorGridHandler->getPublication());
        $authorGridHandler->addColumn(new GridColumn('name')); //NEEDED TO KEEP THE ORDER AND MAKE INSTITUTION THE SECOND COLUMN
        $authorGridHandler->addColumn(
            new GridColumn(
                'institution',
                'user.affiliation',
                null,
                null,
                $cellProvider,
                array('width' => 40, 'alignment' => COLUMN_ALIGNMENT_LEFT)
            )
        );

        return false;
    }

    /**
     * Use the Template::Workflow hook to add components to the workflow template
     *  - contributors panel
     *
     * @param $hookName
     * @param $hookArgs
     * @return false
     */
    public function updateWorkflowTemplate($hookName, $hookArgs) {
        $smarty =& $hookArgs[1];
        $output =& $hookArgs[2];

        // ADD THE CONTRIBUTORS COMPONENT TO THE WORKFLOW TEMPLATE
        $output .= $smarty->fetch($this->_pprPlugin->getTemplateResource('ppr/workflowContributors.tpl'));

        return false;
    }
}