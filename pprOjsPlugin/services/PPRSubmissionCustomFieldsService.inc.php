<?php

class PPRSubmissionCustomFieldsService {
    const SUBMISSION_FORM_FIELD = 'commentsForReviewer';

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->submissionCustomFieldsEnabled()) {
            HookRegistry::register('Schema::get::publication', array($this, 'addFieldsToPublicationDatabaseSchema'));
            HookRegistry::register('submissionsubmitstep3form::initdata', array($this, 'initSubmissionData'));
            HookRegistry::register('submissionsubmitstep3form::readuservars', array($this, 'readUserVars'));
            HookRegistry::register('submissionsubmitstep3form::execute', array($this, 'executeSubmission'));


            HookRegistry::register('reviewerreviewstep3form::initdata', array($this, 'initReviewData'));
        }
    }

    function addFieldsToPublicationDatabaseSchema($hookName, $params) {
        $schema = $params[0];
        $schema->properties->commentsForReviewer = new stdClass();
        $schema->properties->commentsForReviewer->type = 'string';
        $schema->properties->commentsForReviewer->multilingual = true;
        $schema->properties->commentsForReviewer->validation = ['nullable'];

        return false;
    }

    function initSubmissionData($hookName, $params) {
        $form = $params[0];
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());

        $publication = $form->submission->getCurrentPublication();
        $commentsForReviewer = $publication->getData(self::SUBMISSION_FORM_FIELD);
        $templateVars = [self::SUBMISSION_FORM_FIELD => $commentsForReviewer];
        $templateMgr->assign($templateVars);

        return false;
    }

    function initReviewData($hookName, $params) {
        $form = $params[0];
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());

        $publication = $form->getReviewerSubmission()->getCurrentPublication();
        $commentsForReviewer = nl2br($publication->getLocalizedData(self::SUBMISSION_FORM_FIELD));
        $templateVars = [self::SUBMISSION_FORM_FIELD => $commentsForReviewer];
        $templateMgr->assign($templateVars);

        return false;
    }

    function readUserVars($hookName, $params) {
        $fieldArray = &$params[1];
        $fieldArray[] = self::SUBMISSION_FORM_FIELD;

        return false;
    }

    function executeSubmission($hookName, $params) {
        $form = $params[0];
        $publication = $form->submission->getCurrentPublication();
        $pubId = $publication->getData('id');
        $commentsForReviewer = $form->getData(self::SUBMISSION_FORM_FIELD);
        $publicationDao = DAORegistry::getDAO('PublicationDAO');

        // STORE INTO THE publication_settings TABLE. ALL LOCALE VALUES
        foreach ($commentsForReviewer as $locale => $value) {
            $valuesArray = [
                'publication_id' => $pubId,
                'locale' => $locale,
                'setting_name' => self::SUBMISSION_FORM_FIELD,
                'setting_value'  => $value
            ];
            $columns = ['publication_id', 'locale', 'setting_name'];
            $publicationDao->replace('publication_settings', $valuesArray, $columns);
        }

        return false;
    }

}