<?php

class PPRUserCustomFieldsService {
    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->userCustomFieldsEnabled()) {
            $this->pprPlugin->import('services.PPRAuthorDAO');
            $pprAuthorDAO = new PPRAuthorDAO();
            DAORegistry::registerDAO('AuthorDAO', $pprAuthorDAO);

            HookRegistry::register('Schema::get::author', array($this, 'addFieldsToAuthorDatabaseSchema'));

            HookRegistry::register('authorform::initdata', array($this, 'initAuthorData'));
            HookRegistry::register('authorform::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('authorform::execute', array($this, 'executeAuthor'));

            HookRegistry::register('userdao::getAdditionalFieldNames', array($this, 'addFieldsToUserDatabaseSchema'));

            HookRegistry::register('registrationform::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('registrationform::execute', array($this, 'executeUser'));


            HookRegistry::register('userdetailsform::initdata', array($this, 'initUserData'));
            HookRegistry::register('userdetailsform::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('userdetailsform::execute', array($this, 'executeUser'));

            HookRegistry::register('createreviewerform::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('createreviewerform::execute', array($this, 'executeReviewer'));
        }
    }

    /**
     * This is needed to store the new fields in the database.
     * Only the fields in the schema are stored in the database
     */
    function addFieldsToAuthorDatabaseSchema($hookName, $args) {
        $schema = $args[0];
        $schema->properties->category = new stdClass();
        $schema->properties->category->type = 'string';
        $schema->properties->category->validation = ['nullable'];

        $schema->properties->department = new stdClass();
        $schema->properties->department->type = 'string';
        $schema->properties->department->validation = ['nullable'];
    }

    function initAuthorData($hookName, $arguments) {
        $form = $arguments[0];
        $this->addFieldValuesToTemplate($form->getAuthor());
    }

    function executeAuthor($hookName, $arguments) {
        $form = $arguments[0];
        $this->addFieldValuesToModel($form, $form->getAuthor());
    }

    function addFieldsToUserDatabaseSchema($hookName, $args) {
        $fieldArray = &$args[1];
        $fieldArray[] = 'category';
        $fieldArray[] = 'department';
    }

    function initUserData($hookName, $arguments) {
        $form = $arguments[0];
        $this->addFieldValuesToTemplate($form->user);
    }

    function executeUser($hookName, $arguments) {
        $form = $arguments[0];
        $this->addFieldValuesToModel($form, $form->user);
    }

    function addFieldValuesToTemplate($dataObject) {
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
        if ($dataObject) {
            $templateVars = array(
                'category' => $dataObject->getData('category'),
                'department' => $dataObject->getData('department'),
            );
            $templateMgr->assign($templateVars);
        }
    }

    /**
     * Copy he new field values from the form to the user/author data object.
     */
    function addFieldValuesToModel($fromObject, $toModelObject) {
        $toModelObject->setData('category', $fromObject->getData('category'));
        $toModelObject->setData('department', $fromObject->getData('department'));
    }

    /**
     * Add the new fields to the list of fields to read from the request.
     */
    function readUserVars($hookName, $arguments) {
        $fieldArray = &$arguments[1];
        $fieldArray[] = 'category';
        $fieldArray[] = 'department';
    }

}