<?php

/**
 * Service to manage the emails sent to contributors from completed editorial actions.
 */
class PPREditorialDecisionsEmailService {

    const OJS_SEND_TO_CONTRIBUTORS_TEMPLATES = ['EDITOR_DECISION_REVISIONS', 'EDITOR_DECISION_RESUBMIT'];

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->editorialDecisionsEmailRemoveContributorsEnabled()) {
            HookRegistry::register('sendreviewsform::display', array($this, 'sendReviewsFormDisplay'));
            HookRegistry::register('Mail::send', array($this, 'requestRevisionsUpdateRecipients'));
        }
    }

    function sendReviewsFormDisplay($hookName, $arguments) {
        $sendReviewForm = $arguments[0];
        $author = $this->getAuthor($sendReviewForm->getSubmission());
        if (isset($author)) {
            $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
            $authorFullName =  htmlspecialchars($author->getFullName());

            $sendReviewForm->setData('authorName', $authorFullName);
            $sendReviewForm->setData('personalMessage', str_replace('{$authorFullName}', $authorFullName, $sendReviewForm->getData('personalMessage')));
            $sendReviewForm->setData('revisionsEmail', str_replace('{$authorFullName}', $authorFullName, $templateMgr->getTemplateVars('revisionsEmail')));
            $sendReviewForm->setData('resubmitEmail', str_replace('{$authorFullName}', $authorFullName, $templateMgr->getTemplateVars('resubmitEmail')));
        }

        return false;
    }

    function requestRevisionsUpdateRecipients($hookName, $arguments) {
        $emailTemplate = $arguments[0];
        if ($emailTemplate instanceof SubmissionMailTemplate && in_array($emailTemplate->emailKey,self::OJS_SEND_TO_CONTRIBUTORS_TEMPLATES)) {
            $author = $this->getAuthor($emailTemplate->submission);
            if (isset($author)) {
                $emailTemplate->setRecipients(array(['name' => $author->getFullName(), 'email' => $author->getEmail()]));
            }
        }

        return false;
    }

    private function getAuthor($submission) {
        $author = $submission->getPrimaryAuthor();
        $contributors = $submission->getAuthors();
        if (!isset($author) && !empty($contributors)) {
            $author = $contributors[0];
        }

        return $author;
    }
}