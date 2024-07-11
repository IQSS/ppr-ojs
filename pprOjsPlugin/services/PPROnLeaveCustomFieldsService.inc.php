<?php

/**
 * Service to manage the new user on leave custom fields required for PPR
 *
 * Issue 062
 */
class PPROnLeaveCustomFieldsService {
    const ON_LEAVE_FROM_FIELD = 'onLeaveFrom';
    const ON_LEAVE_TO_FIELD = 'onLeaveTo';

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->userOnLeaveEnabled()) {
            HookRegistry::register('LoadComponentHandler', array($this, 'addPPRUserSelectGridHandler'));
            HookRegistry::register('userdao::getAdditionalFieldNames', array($this, 'addOnLeaveFieldsToUserDatabaseSchema'));

            HookRegistry::register('identityform::display', array($this, 'initIdentityFormData'));
            HookRegistry::register('identityform::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('identityform::execute', array($this, 'executeIdentity'));

            HookRegistry::register('userdetailsform::initdata', array($this, 'initUserFormData'));
            HookRegistry::register('userdetailsform::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('userdetailsform::execute', array($this, 'executeUser'));
        }
    }

    function addPPRUserSelectGridHandler($hookName, $hookArgs) {
        $component =& $hookArgs[0];
        if ($component === 'grid.users.userSelect.UserSelectGridHandler') {
            // LOAD THE PPR AUTHOR HANDLER FROM THE PLUGIN REPO
            $component =str_replace('/', '.', $this->pprPlugin->getPluginPath()) . '.services.PPRUserSelectGridHandler';
            return true;
        }
        return false;
    }

    function addOnLeaveFieldsToUserDatabaseSchema($hookName, $args) {
        $fieldArray = &$args[1];
        $fieldArray[] = self::ON_LEAVE_FROM_FIELD;
        $fieldArray[] = self::ON_LEAVE_TO_FIELD;
    }

    function initUserFormData($hookName, $arguments) {
        $form = $arguments[0];
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
        // ONLY ADD ON LEAVE FIELDS WHEN EDITING A USER
        $templateMgr->assign(['pprUserEdit' => (bool)$form->user]);
        $dataObject = $form->user;
        $this->addFieldValuesToTemplate($dataObject);
    }

    function executeUser($hookName, $arguments) {
        $form = $arguments[0];
        $form->user->setData(self::ON_LEAVE_FROM_FIELD, $form->getData(self::ON_LEAVE_FROM_FIELD));
        $form->user->setData(self::ON_LEAVE_TO_FIELD, $form->getData(self::ON_LEAVE_TO_FIELD));
    }

    function initIdentityFormData($hookName, $arguments) {
        $form = $arguments[0];
        $dataObject = $form->getUser();
        $this->addFieldValuesToTemplate($dataObject);
    }

    function executeIdentity($hookName, $arguments) {
        $form = $arguments[0];
        $form->getUser()->setData(self::ON_LEAVE_FROM_FIELD, $form->getData(self::ON_LEAVE_FROM_FIELD));
        $form->getUser()->setData(self::ON_LEAVE_TO_FIELD, $form->getData(self::ON_LEAVE_TO_FIELD));
    }

    function addFieldValuesToTemplate($dataObject) {
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
        if ($dataObject) {
            $templateVars = array(
                self::ON_LEAVE_FROM_FIELD => $dataObject->getData(self::ON_LEAVE_FROM_FIELD),
                self::ON_LEAVE_TO_FIELD => $dataObject->getData(self::ON_LEAVE_TO_FIELD),
            );
            $templateMgr->assign($templateVars);
        }
    }

    /**
     * Add the new fields to the list of fields to read from the request.
     */
    function readUserVars($hookName, $arguments) {
        $fieldArray = &$arguments[1];
        $fieldArray[] = self::ON_LEAVE_FROM_FIELD;
        $fieldArray[] = self::ON_LEAVE_TO_FIELD;
    }

}