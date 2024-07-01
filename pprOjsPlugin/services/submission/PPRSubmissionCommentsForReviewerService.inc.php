<?php

/**
 * Service to add the commentsForReviewer custom field to the create submission > metadata form
 *
 * This services adds the custom field to the create review form for the reviewer
 *
 * Issue 037
 */
class PPRSubmissionCommentsForReviewerService {
    const COMMENTS_FOR_REVIEWER_FIELD = 'commentsForReviewer';

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->submissionCommentsForReviewerEnabled()) {
            HookRegistry::register('Schema::get::publication', array($this, 'addFieldsToPublicationDatabaseSchema'));
            HookRegistry::register('submissionsubmitstep3form::initdata', array($this, 'initSubmissionFormData'));
            HookRegistry::register('submissionsubmitstep3form::readuservars', array($this, 'readCommentsForReviewerVars'));
            HookRegistry::register('submissionsubmitstep3form::execute', array($this, 'executeSubmissionCommentsForReviewer'));


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

    function initSubmissionFormData($hookName, $params) {
        $form = $params[0];
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());

        $publication = $form->submission->getCurrentPublication();
        $commentsForReviewer = $publication->getData(self::COMMENTS_FOR_REVIEWER_FIELD);
        $templateMgr->assign([self::COMMENTS_FOR_REVIEWER_FIELD => $commentsForReviewer]);

        return false;
    }

    function initReviewData($hookName, $params) {
        $form = $params[0];
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());

        $publication = $form->getReviewerSubmission()->getCurrentPublication();
        $commentsForReviewer = nl2br($publication->getLocalizedData(self::COMMENTS_FOR_REVIEWER_FIELD));
        $templateVars = [self::COMMENTS_FOR_REVIEWER_FIELD => $commentsForReviewer];
        $templateMgr->assign($templateVars);

        return false;
    }

    function readCommentsForReviewerVars($hookName, $params) {
        $fieldArray = &$params[1];
        $fieldArray[] = self::COMMENTS_FOR_REVIEWER_FIELD;

        return false;
    }

    function executeSubmissionCommentsForReviewer($hookName, $params) {
        $form = $params[0];
        $publication = $form->submission->getCurrentPublication();
        $pubId = $publication->getData('id');
        $commentsForReviewer = $form->getData(self::COMMENTS_FOR_REVIEWER_FIELD);
        $publicationDao = DAORegistry::getDAO('PublicationDAO');

        // STORE INTO THE publication_settings TABLE. ALL LOCALE VALUES
        foreach ($commentsForReviewer as $locale => $value) {
            $valuesArray = [
                'publication_id' => $pubId,
                'locale' => $locale,
                'setting_name' => self::COMMENTS_FOR_REVIEWER_FIELD,
                'setting_value'  => $value
            ];
            $columns = ['publication_id', 'locale', 'setting_name'];
            $publicationDao->replace('publication_settings', $valuesArray, $columns);
        }

        return false;
    }

}