<?php

/**
 * Service to add the submission researchType custom field to the create submission > metadata form
 *
 * This services adds the custom field to the add reviewer form
 *
 * Issue 074, Issue 090
 */
class PPRSubmissionResearchTypeService {
    const RESEARCH_TYPE_FIELD = 'researchType';

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->submissionResearchTypeEnabled()) {
            HookRegistry::register('Schema::get::submission', array($this, 'addFieldsToSubmissionDatabaseSchema'));
            HookRegistry::register('submissionsubmitstep3form::initdata', array($this, 'initResearchTypeData'));
            HookRegistry::register('submissionsubmitstep3form::readuservars', array($this, 'readResearchTypeVars'));
            HookRegistry::register('submissionsubmitstep3form::execute', array($this, 'executeSubmissionResearchType'));

            HookRegistry::register('advancedsearchreviewerform::display', array($this, 'initReviewerFormData'));
            HookRegistry::register('createreviewerform::display', array($this, 'initReviewerFormData'));
            HookRegistry::register('enrollexistingreviewerform::display', array($this, 'initReviewerFormData'));
        }
    }

    function addFieldsToSubmissionDatabaseSchema($hookName, $params) {
        $schema = $params[0];
        $schema->properties->researchType = new stdClass();
        $schema->properties->researchType->type = 'string';
        $schema->properties->researchType->validation = ['nullable'];

        return false;
    }

    function initResearchTypeData($hookName, $params) {
        $submissionForm = $params[0];
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());

        $researchType =  $submissionForm->submission->getData(self::RESEARCH_TYPE_FIELD);
        $templateMgr->assign([self::RESEARCH_TYPE_FIELD => $researchType]);
        $templateMgr->assign(['researchTypes' => $this->pprPlugin->getPluginSettings()->getResearchTypes()]);

        return false;
    }

    function initReviewerFormData($hookName, $params) {
        $reviewerForm = $params[0];
        $researchType = $reviewerForm->getSubmission()->getData(self::RESEARCH_TYPE_FIELD);
        $reviewerForm->setData('submissionResearchType', $researchType);

        $researchTypeOptions = $this->pprPlugin->getPluginSettings()->getResearchTypeOptions();
        $preSelectedReviewerForm =  $researchTypeOptions[$researchType] ?? null;
        if ($preSelectedReviewerForm) {
            // OVERRIDE THE SELECTED REVIEWER FORM IN THE TEMPLATE
            $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
            $reviewForms = $templateMgr->getTemplateVars('reviewForms');
            foreach ($reviewForms as $formId => $formName) {
                if(0 === strcasecmp($preSelectedReviewerForm, $formName)) {
                    $reviewerForm->setData('reviewFormId', $formId);
                    break;
                }
            }
        }

        return false;
    }

    function readResearchTypeVars($hookName, $params) {
        $fieldArray = &$params[1];
        $fieldArray[] = self::RESEARCH_TYPE_FIELD;

        return false;
    }

    function executeSubmissionResearchType($hookName, $params) {
        $submissionForm = $params[0];
        $submissionForm->submission->setData(self::RESEARCH_TYPE_FIELD, $submissionForm->getData(self::RESEARCH_TYPE_FIELD));

        return false;
    }

}