<?php

import('lib.pkp.classes.scheduledTask.ScheduledTask');
require_once(dirname(__FILE__) . '/PPRNotificationRegistry.inc.php');
require_once(dirname(__FILE__) . '/PPRDueReviewData.inc.php');

/**
 * Base class for PPR tasks, with utility and common methods.
 */
abstract class PPRScheduledTask extends ScheduledTask {

    private $userCache;
    private $submissionCache;

    function __construct($args) {
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
    }

    public function getUser($userId) {
        if(!isset($this->userCache[$userId])) {
            $userDao = DAORegistry::getDAO('UserDAO');
            $this->userCache[$userId] = $userDao->getById($userId);
        }

        return $this->userCache[$userId];
    }

    public function getSubmission($submissionId) {
        if(!isset($this->submissionCache[$submissionId])) {
            $submissionDao = DAORegistry::getDAO('SubmissionDAO');
            $this->submissionCache[$submissionId] = $submissionDao->getById($submissionId);
        }

        return $this->submissionCache[$submissionId];
    }

    public function log($context, $message) {
        $pprTaskName = $this->getName();
        $contextPath = $context->getPath();
        error_log("PPR[{$pprTaskName}] context={$contextPath} {$message}");
    }
}


