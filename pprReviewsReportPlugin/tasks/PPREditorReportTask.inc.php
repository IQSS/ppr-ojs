<?php

import('lib.pkp.classes.scheduledTask.ScheduledTask');
import('lib.pkp.classes.mail.MailTemplate');

require_once(dirname(__FILE__) . '/../reports/PPRSubmissionsReviewsReport.inc.php');

/**
 * Task to send the submissions/review report to a list of recipients configured in the plugin settings
 */
class PPREditorReportTask extends ScheduledTask {

    function getName() {
        return 'PPREditorReportTask';
    }

    public function executeActions() {
        $contextDao = Application::getContextDAO();
        // PROCESS REVIEW REMINDERS FOR ALL OJS CONTEXTS
        $ojsEnabledContexts = $contextDao->getAll(true)->toArray();
        foreach ($ojsEnabledContexts as $context) {
            $pprReportPlugin = PluginRegistry::getPlugin('reports', 'PPRReviewsReportPlugin');
            if (!$pprReportPlugin) {
                $this->log($context, 'PPRReviewsReportPlugin is null - loading new instance');
                $pprReportPlugin = PluginRegistry::loadPlugin('reports', 'pprReviewsReportPlugin', $context->getId());
            }

            if (!$pprReportPlugin->getEnabled($context->getId())) {
                // PLUGIN NOT ENABLED FOR CURRENT CONTEXT
                $this->log($context, 'PPRReportPluginEnabled=false');
                continue;
            }

            $pprReportPluginSettings = $pprReportPlugin->createPluginSettings($context->getId());
            $this->executeForContext($context, $pprReportPluginSettings);
        }

        //RETURN SUCCESS TO THE SCHEDULE TASKS MANAGER
        return true;
    }


    public function executeForContext($context, $pprReportPluginSettings) {
        if (!$pprReportPluginSettings->submissionsReviewsReportEnabled()) {
            // THIS IS REQUIRED HERE AS THE CONFIGURED SCHEDULED TASKS ARE LOADED BY THE acron PLUGIN WHEN IT IS RELOADED
            $this->log($context, 'submissionsReviewsEditorReportEnabled=false');
            return;
        }

        $recipients = $pprReportPluginSettings->submissionsReviewsReportRecipients();
        if (empty($recipients)) {
            $this->log($context, 'recipients=empty');
            return;
        }

        $this->log($context, "Start");
        import('lib.pkp.classes.file.PrivateFileManager');
        $fileMgr = new PrivateFileManager();
        $filename = uniqid('ppr_editor_report_');
        $reportFilename = implode(DIRECTORY_SEPARATOR, [realpath($fileMgr->getBasePath()), 'temp', "{$filename}.csv"]);
        $attachmentFilename = 'ppr_submissions_report_' . date('Y_m_d') . '.csv';

        $reportFactory = new PPRSubmissionsReviewsReport();
        $reportFactory->createReport($reportFilename, $context->getId());

        $email = new MailTemplate('PPR_SUBMISSIONS_REPORT_TASK', null, $context);
        $email->setReplyTo(null);
        foreach ($recipients as $recipientEmail) {
            $email->addRecipient($recipientEmail, '');
        }
        $email->setFrom($context->getData('contactEmail'), $context->getData('contactName'));
        $email->addAttachment($reportFilename, $attachmentFilename);
        $email->send();

        $deleteResult = $fileMgr->deleteByPath($reportFilename);
        $this->log($context, "Completed - tempFile=$reportFilename deleted=$deleteResult");
    }

    public function log($context, $message) {
        $pprTaskName = $this->getName();
        $contextPath = $context->getPath();
        error_log("PPR[{$pprTaskName}] context={$contextPath} {$message}");
    }
}


