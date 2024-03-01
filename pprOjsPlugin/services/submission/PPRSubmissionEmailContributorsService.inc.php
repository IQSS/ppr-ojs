<?php

/**
 * Service to add the submission emailContributors custom field to the create submission > metadata form
 *
 * This services adds the research type custom field to the add reviewer form
 */
class PPRSubmissionEmailContributorsService {
    const EMAIL_CONTRIBUTORS_FIELD = 'emailContributors';

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->emailContributorsEnabled()) {
            HookRegistry::register('Schema::get::submission', array($this, 'addFieldsToSubmissionDatabaseSchema'));
            HookRegistry::register('submissionsubmitstep3form::initdata', array($this, 'initSubmissionFormData'));
            HookRegistry::register('submissionsubmitstep3form::readuservars', array($this, 'readSubmissionFormVars'));
            HookRegistry::register('submissionsubmitstep3form::execute', array($this, 'executeSubmissionSubmissionForm'));
        }
    }

    function addFieldsToSubmissionDatabaseSchema($hookName, $params) {
        $schema = $params[0];
        $schema->properties->emailContributors = new stdClass();
        $schema->properties->emailContributors->type = 'boolean';
        $schema->properties->emailContributors->validation = ['nullable'];

        return false;
    }

    function initSubmissionFormData($hookName, $params) {
        $submissionForm = $params[0];
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());

        $emailContributors =  $submissionForm->submission->getData(self::EMAIL_CONTRIBUTORS_FIELD);
        $templateMgr->assign([self::EMAIL_CONTRIBUTORS_FIELD => $emailContributors]);

        return false;
    }

    function readSubmissionFormVars($hookName, $params) {
        $fieldArray = &$params[1];
        $fieldArray[] = self::EMAIL_CONTRIBUTORS_FIELD;

        return false;
    }

    function executeSubmissionSubmissionForm($hookName, $params) {
        $submissionForm = $params[0];
        $submissionForm->submission->setData(self::EMAIL_CONTRIBUTORS_FIELD, $submissionForm->getData(self::EMAIL_CONTRIBUTORS_FIELD));

        return false;
    }

}