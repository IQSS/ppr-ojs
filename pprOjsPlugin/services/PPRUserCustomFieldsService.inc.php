<?php

/**
 * Service to manage the new user/author custom fields required for PPR
 */
class PPRUserCustomFieldsService {
    const CATEGORY_DROPDOWN = 'categories';
    const CATEGORY_FIELD = 'category';
    const DEPARTMENT_FIELD = 'department';

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

            HookRegistry::register('authorform::initdata', array($this, 'initAuthorFormData'));
            HookRegistry::register('authorform::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('authorform::execute', array($this, 'executeAuthor'));

            HookRegistry::register('userdao::getAdditionalFieldNames', array($this, 'addFieldsToUserDatabaseSchema'));

            HookRegistry::register('registrationform::display', array($this, 'initRegistrationFormData'));
            HookRegistry::register('registrationform::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('registrationform::execute', array($this, 'executeUser'));

            HookRegistry::register('userdetailsform::initdata', array($this, 'initUserFormData'));
            HookRegistry::register('userdetailsform::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('userdetailsform::execute', array($this, 'executeUser'));

            HookRegistry::register('contactform::display', array($this, 'initContactFormData'));
            HookRegistry::register('contactform::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('contactform::execute', array($this, 'executeContact'));

            HookRegistry::register('createreviewerform::display', array($this, 'initReviewersFormData'));
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

    function initAuthorFormData($hookName, $arguments) {
        $form = $arguments[0];
        $this->addFieldValuesToTemplate($form->getAuthor());
    }

    function executeAuthor($hookName, $arguments) {
        $form = $arguments[0];
        $this->addFieldValuesToModel($form, $form->getAuthor());
    }

    function addFieldsToUserDatabaseSchema($hookName, $args) {
        $fieldArray = &$args[1];
        $fieldArray[] = self::CATEGORY_FIELD;
        $fieldArray[] = self::DEPARTMENT_FIELD;
    }

    function initRegistrationFormData($hookName, $arguments) {
        //WE ONLY NEED TO ADD THE CATEGORIES DROPDOWN DATA
        $this->addFieldValuesToTemplate(null);
    }

    function initUserFormData($hookName, $arguments) {
        $form = $arguments[0];
        // THIS FORM IS SHARED FOR USERS AND AUTHORS
        $dataObject = $form->user ?? $form->author;
        $this->addFieldValuesToTemplate($dataObject);
    }

    function executeUser($hookName, $arguments) {
        $form = $arguments[0];
        $this->addFieldValuesToModel($form, $form->user);
    }

    function initContactFormData($hookName, $arguments) {
        $form = $arguments[0];
        $dataObject = $form->getUser();
        $this->addFieldValuesToTemplate($dataObject);
    }

    function executeContact($hookName, $arguments) {
        $form = $arguments[0];
        $this->addFieldValuesToModel($form, $form->getUser());
    }

    function initReviewersFormData($hookName, $arguments) {
        //WE ONLY NEED TO ADD THE CATEGORIES DROPDOWN DATA
        $this->addFieldValuesToTemplate(null);
    }

    function executeReviewer($hookName, $arguments) {
        $userDao = DAORegistry::getDAO('UserDAO');

        $form = $arguments[0];
        $userId = $form->getData('reviewerId');
        $user = $userDao->getById($userId);
        $this->addFieldValuesToModel($form, $user);
        $userDao->updateObject($user);
    }

    function addFieldValuesToTemplate($dataObject) {
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
        //ADD CATEGORIES TO POPULATE DROPDOWN OPTIONS IN FORM
        $templateMgr->assign([self::CATEGORY_DROPDOWN => $this->pprPlugin->getPluginSettings()->getCategoryOptions()]);
        if ($dataObject) {
            $templateVars = array(
                self::CATEGORY_FIELD => $dataObject->getData(self::CATEGORY_FIELD),
                self::DEPARTMENT_FIELD => $dataObject->getData(self::DEPARTMENT_FIELD),
            );
            $templateMgr->assign($templateVars);
        }
    }

    /**
     * Copy the new field values from the form to the user/author data object.
     */
    function addFieldValuesToModel($fromObject, $toModelObject) {
        $toModelObject->setData(self::CATEGORY_FIELD, $fromObject->getData(self::CATEGORY_FIELD));
        $toModelObject->setData(self::DEPARTMENT_FIELD, $fromObject->getData(self::DEPARTMENT_FIELD));
    }

    /**
     * Add the new fields to the list of fields to read from the request.
     */
    function readUserVars($hookName, $arguments) {
        $fieldArray = &$arguments[1];
        $fieldArray[] = self::CATEGORY_FIELD;
        $fieldArray[] = self::DEPARTMENT_FIELD;
    }

}