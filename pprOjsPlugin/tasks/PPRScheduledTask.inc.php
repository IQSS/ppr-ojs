<?php

import('lib.pkp.classes.scheduledTask.ScheduledTask');
require_once(dirname(__FILE__) . '/PPRTaskNotificationRegistry.inc.php');
require_once(dirname(__FILE__) . '/PPRDueReviewData.inc.php');

/**
 * Base class for PPR tasks, with utility and common methods.
 */
abstract class PPRScheduledTask extends ScheduledTask {

    private $pprObjectFactory;

    function __construct($args, $pprObjectFactory = null) {
        $this->pprObjectFactory = $pprObjectFactory ?: new PPRObjectFactory();
        $this->userCache = [];
        $this->submissionCache = [];

        parent::__construct($args);
    }

    /**
     * Implemented by each task and executed for each context in the OJS installation.
     * Will be called from executeActions passing a context and an enabled PPR plugin.
     */
    abstract protected function executeForContext($context, $pprPlugin);

    /**
     * Loop through the available contexts and loading the PPR plugin for each one of them
     * When PPR plugin is enabled, will call the task executeForContext.
     */
    public function executeActions() {
        $contextDao = Application::getContextDAO();
        // PROCESS REVIEW REMINDERS FOR ALL OJS CONTEXTS
        $ojsEnabledContexts = $contextDao->getAll(true)->toArray();
        foreach ($ojsEnabledContexts as $context) {
            $pprPlugin = PluginRegistry::loadPlugin('generic', 'pprOjsPlugin', $context->getId());
            if (!$pprPlugin->getEnabled($context->getId())) {
                // PLUGIN NOT ENABLED FOR CURRENT CONTEXT
                $this->log($context, 'PPRPluginEnabled=false');
                continue;
            }

            $this->executeForContext($context, $pprPlugin);
        }

        //RETURN SUCCESS TO THE SCHEDULE TASKS MANAGER
        return true;
    }

    public function getPPRObjectFactory() {
        return $this->pprObjectFactory;
    }

    public function getUser($userId) {
        return $this->getPPRObjectFactory()->submissionUtil()->getUser($userId);
    }

    public function getSubmissionEditor($submissionId, $contextId) {
        $submissionEditors = $this->getPPRObjectFactory()->submissionUtil()->getSubmissionEditors($submissionId, $contextId);
        return empty($submissionEditors) ? null : reset($submissionEditors);
    }

    public function getAuthor($submissionId) {
        $submissionAuthors = $this->getPPRObjectFactory()->submissionUtil()->getSubmissionAuthors($submissionId);
        return empty($submissionAuthors) ? null : reset($submissionAuthors);
    }

    public function getSubmission($submissionId) {
        return $this->getPPRObjectFactory()->submissionUtil()->getSubmission($submissionId);
    }

    public function log($context, $message) {
        $pprTaskName = $this->getName();
        $contextPath = $context->getPath();
        error_log("PPR[{$pprTaskName}] context={$contextPath} {$message}");
    }
}


