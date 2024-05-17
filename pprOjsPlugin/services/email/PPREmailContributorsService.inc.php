<?php

/**
 * Service to manage the emails sent to contributors from different actions in OJS.
 */
class PPREmailContributorsService {

    const OJS_SEND_TO_CONTRIBUTORS_TEMPLATES =
        [
            'SUBMISSION_ACK',
            'EDITOR_DECISION_REVISIONS',
            'EDITOR_DECISION_RESUBMIT',
            'EDITOR_DECISION_INITIAL_DECLINE',
            'EDITOR_DECISION_DECLINE',
            'PPR_REVIEW_SENT_AUTHOR',
            'PPR_SUBMISSION_APPROVED'
        ];

    private $pprPlugin;
    private $pprObjectFactory;

    function __construct($plugin, $pprObjectFactory = null) {
        $this->pprPlugin = $plugin;
        $this->pprObjectFactory = $pprObjectFactory ?: new PPRObjectFactory();
        $this->pprPlugin->import('util.PPRMissingUser');
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->emailContributorsEnabled()) {
            HookRegistry::register('sendreviewsform::display', array($this, 'sendReviewsFormDisplay'));
            HookRegistry::register('Mail::send', array($this, 'addContributorsToEmailRecipients'));
        }
    }

    /**
     * Adds author, contributors, editor, and reviewer names to the email body displayed in the SendReviewsForm.
     */
    function sendReviewsFormDisplay($hookName, $arguments) {
        $sendReviewForm = $arguments[0];
        $submission = $sendReviewForm->getSubmission();
        $author = $this->getSubmissionAuthor($submission->getId());
        if (isset($author)) {
            $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
            $emailContributors =  $submission->getData('emailContributors');
            $authorNames = $emailContributors ? $submission->getAuthorString() : $author->getFullName();
            $sendReviewForm->setData('authorName', htmlspecialchars($authorNames));

            $personalMessage = $sendReviewForm->getData('personalMessage');
            $personalMessage = $this->pprObjectFactory->firstNamesManagementService()->replaceFirstNames($personalMessage, $submission);
            $sendReviewForm->setData('personalMessage', $personalMessage);

            $revisionsEmail = $templateMgr->getTemplateVars('revisionsEmail');
            $revisionsEmail = $this->pprObjectFactory->firstNamesManagementService()->replaceFirstNames($revisionsEmail, $submission);
            $sendReviewForm->setData('revisionsEmail', $revisionsEmail);

            $resubmitEmail = $templateMgr->getTemplateVars('resubmitEmail');
            $resubmitEmail = $this->pprObjectFactory->firstNamesManagementService()->replaceFirstNames($resubmitEmail, $submission);
            $sendReviewForm->setData('resubmitEmail', $resubmitEmail);
        }

        return false;
    }

    /**
     * Updates the email recipients to set just the submission author or the author and contributors
     * depending on the PPR submission custom field emailContributors
     */
    function addContributorsToEmailRecipients($hookName, $arguments) {
        $emailTemplate = $arguments[0];
        if ($emailTemplate instanceof SubmissionMailTemplate && in_array($emailTemplate->emailKey,self::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES)) {
            $submission = $emailTemplate->submission;
            $emailContributors =  $submission->getData('emailContributors');
            $author = $this->getSubmissionAuthor($submission->getId());
            if (!$author) {
                return;
            }

            $recipients = array(['name' => $author->getFullName(), 'email' => $author->getEmail()]);
            if ($emailContributors) {
                foreach ($submission->getAuthors() as $contributor) {
                    if(0 === strcasecmp($author->getEmail(), $contributor->getEmail())) {
                        // IF AUTHOR IS ALREADY INCLUDED, IGNORE
                        continue;
                    }

                    $recipients[] = ['name' => $contributor->getFullName(), 'email' => $contributor->getEmail()];
                }
            }

            $emailTemplate->setRecipients($recipients);
        }

        return false;
    }

    private function getSubmissionAuthor($submissionId) {
        $submissionAuthors = $this->pprObjectFactory->submissionUtil()->getSubmissionAuthors($submissionId);
        return empty($submissionAuthors) ? null : reset($submissionAuthors);
    }
}