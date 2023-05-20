<?php

import('lib.pkp.classes.plugins.GenericPlugin');

/**
 * Registration Updates
 * ppr-ojs/environment/data/ojs/src/lib/pkp/templates/frontend/pages/userRegister.tpl
 * ppr-ojs/environment/data/ojs/src/lib/pkp/templates/frontend/components/registrationForm.tpl
 * RegistrationForm
 * UserDAO
 *
 *
 * Admin > Users > Add User
 * ppr-ojs/environment/data/ojs/src/lib/pkp/templates/controllers/grid/settings/user/form/userDetailsForm.tpl
 * ppr-ojs/environment/data/ojs/src/lib/pkp/templates/common/userDetails.tpl
 * UserDetailsForm
 *
 * Submission - Add contributor
 * controllers/grid/users/author/form/authorForm.tpl
 * AuthorGridHandler
 * controllers.grid.users.author.form.AuthorForm
 *
 * Review - Add reviewer
 * ppr-ojs/environment/data/ojs/src/lib/pkp/templates/controllers/grid/users/reviewer/form/createReviewerForm.tpl
 * ReviewerGridHandler
 * CreateReviewerForm
 *
 *
 * DISPLAY:
 * StageParticipantGridHandler
 * StageParticipantGridCellProvider
 *
 * AuthorGridHandler
 * PKPAuthorGridCellProvider
 *
 */

class PeerPreReviewProgramPlugin extends GenericPlugin {

    /**
     * @copydoc Plugin::register()
     */
    function register($category, $path, $mainContextId = null) {
        $success = parent::register($category, $path, $mainContextId);
        if ($success && $this->getEnabled()) {
            $this->import('ProgramUserDAO');
            $pprUserDAO = new PPRUserDAO();
            DAORegistry::registerDAO('UserDAO', $pprUserDAO);

            HookRegistry::register('TemplateResource::getFilename', array($this, '_overridePluginTemplates'));

            HookRegistry::register('Schema::get::author', array($this, 'addToAuthorSchema'));
            HookRegistry::register('authorform::initdata', array($this, 'initDataAuthor'));
            HookRegistry::register('authorform::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('authorform::execute', array($this, 'executeAuthor'));

            HookRegistry::register('registrationform::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('registrationform::execute', array($this, 'execute'));


            HookRegistry::register('userdetailsform::initdata', array($this, 'initData'));
            HookRegistry::register('userdetailsform::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('userdetailsform::execute', array($this, 'execute'));

            HookRegistry::register('createreviewerform::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('createreviewerform::execute', array($this, 'executeReviewer'));

            //DISPLAY
            $this->import('controllers.PPRWorkflowHandler');
            $workflowHandler = new PPRWorkflowHandler($this);
            $workflowHandler->register();
            //HookRegistry::register('authorgridhandler::initfeatures', array($this, 'initFeatures'));
            //HookRegistry::register('Template::Workflow', array($this, 'addTemplate'));

            //HookRegistry::register('userdao::getAdditionalFieldNames', array($this, 'getAdditionalFieldNames'));
            $this->setupCustomCss();
        }

        return $success;
    }

    /**
     * @copydoc LazyLoadPlugin::setEnabled()
     */
	function setEnabled($enabled) {
		parent::setEnabled($enabled);
		if (!$enabled) {
			// CLEAR THE TEMPLATE CACHE TO RELOAD DEFAULT TEMPLATES
			// THIS IS AN ISSUE WITH TEMPLATE OVERRIDE IN PLUGINS
			$templateMgr = TemplateManager::getManager(Application::get()->getRequest());
			$templateMgr->clearTemplateCache();
			$templateMgr->clearCssCache();

			$cacheMgr = CacheManager::getManager();
			$cacheMgr->flush();
		}
	}

    public function addTemplate($hookName, $args) {
        $something = $args[0];
        $smarty =& $args[1];
        $output =& $args[2];

        // add the new fields block to the template form
        $output .= $smarty->fetch($this->getTemplateResource('contributors.tpl'));
        return false;

    }

    public function initFeatures($hookName, $args) {
        import('lib.pkp.controllers.grid.users.author.PKPAuthorGridCellProvider');
        $handler = $args[0];
        $cellProvider = new PKPAuthorGridCellProvider($handler->getPublication());
        $handler->addColumn(
            new GridColumn(
                'name',
                'author.users.contributor.name',
                null,
                null,
                $cellProvider,
                array('width' => 20, 'alignment' => COLUMN_ALIGNMENT_LEFT)
            )
        );
        $handler->addColumn(
            new GridColumn(
                'affiliation',
                'form.institution.label',
                null,
                null,
                $cellProvider,
                array('width' => 40, 'alignment' => COLUMN_ALIGNMENT_LEFT)
            )
        );
    }

    public function addToAuthorSchema($hookName, $args) {
        $schema = $args[0];
        $schema->properties->institution = new stdClass();
        $schema->properties->institution->type = 'string';
        $schema->properties->institution->multilingual = true;
        $schema->properties->institution->validation = ['nullable'];
    }

    function initDataAuthor($hookName, $arguments) {
        $form = $arguments[0];
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
        if ($form->getAuthor()) {
            $templateVars = array('institution' => $form->getAuthor()->getData('institution'));
            $templateMgr->assign($templateVars);
        }
    }

    function executeAuthor($hookName, $arguments) {
        $request = Application::get()->getRequest();
        $site = $request->getSite();
        $sitePrimaryLocale = $site->getPrimaryLocale();
        $currentLocale = AppLocale::getLocale();

        $form = $arguments[0];
        $form->getAuthor()->setData('institution', $form->getData('institution'), null);
        //$form->user->setData('institution', $form->getData('institution', null), $currentLocale);
        //if ($sitePrimaryLocale != $currentLocale) {
        //    $form->user->setData('institution', $form->getData('institution', null), $sitePrimaryLocale);
        //}
    }

    function initData($hookName, $arguments) {
        $form = $arguments[0];
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
        if ($form->user) {
            $templateVars = array('institution' => $form->user->getData('institution'));
            $templateMgr->assign($templateVars);
        }
    }

    function readUserVars($hookName, $arguments) {
        $form = $arguments[0];
        $fieldArray = &$arguments[1];
        $fieldArray[] = 'institution';
    }

    function execute($hookName, $arguments) {
        $request = Application::get()->getRequest();
        $site = $request->getSite();
        $sitePrimaryLocale = $site->getPrimaryLocale();
        $currentLocale = AppLocale::getLocale();

        $form = $arguments[0];
        $form->user->setData('institution', $form->getData('institution'), null);
        //$form->user->setData('institution', $form->getData('institution', null), $currentLocale);
        //if ($sitePrimaryLocale != $currentLocale) {
        //    $form->user->setData('institution', $form->getData('institution', null), $sitePrimaryLocale);
        //}
    }

    function executeReviewer($hookName, $arguments) {
        $userDao = DAORegistry::getDAO('UserDAO');

        $form = $arguments[0];
        $userId = $form->getData('reviewerId');
        $user = $userDao->getById($userId);
        $user->setData('institution', $form->getData('institution'), null);
        $userDao->updateObject($user);

        //$form->user->setData('institution', $form->getData('institution', null), $currentLocale);
        //if ($sitePrimaryLocale != $currentLocale) {
        //    $form->user->setData('institution', $form->getData('institution', null), $sitePrimaryLocale);
        //}
    }

    function getAdditionalFieldNames($hookName, $arguments) {
        $fieldArray = &$arguments[1];
        $fieldArray[] = 'institution';
    }

    /**
     * Load custom CSS file into all backend pages.
     *
     * @return void
     */
	function setupCustomCss() {
		$request = Application::get()->getRequest();
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->addStyleSheet(
			'iqssCustomCss',
			$request->getBaseUrl() . '/' . $this->getPluginPath() . '/css/iqss.css',
			['contexts' => 'backend']
		);
	}

	/**
	 * @copydoc Plugin::getDisplayName
	 */
	function getDisplayName() {
		return "IQSS Peer Pre-Review Program Plugin";
	}

	/**
	 * @copydoc Plugin::getDescription
	 */
	function getDescription() {
		return "Customizations to the OJS registration and submission workflow for use in the Peer Pre-Review Program at Harvard’s IQSS";
	}
}

