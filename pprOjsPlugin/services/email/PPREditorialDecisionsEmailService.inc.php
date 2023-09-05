<?php

/**
 * Service to manage the emails sent to contributors from completed editorial actions.
 */
class PPREditorialDecisionsEmailService {

    const OJS_SEND_TO_CONTRIBUTORS_TEMPLATES = ['EDITOR_DECISION_REVISIONS', 'EDITOR_DECISION_RESUBMIT', 'EDITOR_DECISION_INITIAL_DECLINE', 'EDITOR_DECISION_DECLINE'];
    const AUTHOR_FULL_NAME_VARIABLE = '{$authorFullName}';
    const AUTHOR_FIRST_NAME_VARIABLE = '{$authorFirstName}';

    private $pprPlugin;

    public function __construct($plugin) {
        $this->pprPlugin = $plugin;
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->editorialDecisionsEmailRemoveContributorsEnabled()) {
            HookRegistry::register('sendreviewsform::display', array($this, 'sendReviewsFormDisplay'));
            HookRegistry::register('Mail::send', array($this, 'editorDecisionEmailsSetRecipients'));
        }
    }

    function sendReviewsFormDisplay($hookName, $arguments) {
        $sendReviewForm = $arguments[0];
        $author = $this->getAuthor($sendReviewForm->getSubmission());
        if (isset($author)) {
            $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
            $authorFullName =  htmlspecialchars($author->getFullName());
            $authorFirstName =  htmlspecialchars($author->getLocalizedGivenName());

            $sendReviewForm->setData('authorName', $authorFullName);

            $personalMessage = $sendReviewForm->getData('personalMessage');
            $personalMessage = str_replace(self::AUTHOR_FULL_NAME_VARIABLE, $authorFullName, $personalMessage);
            $personalMessage = str_replace(self::AUTHOR_FIRST_NAME_VARIABLE, $authorFirstName, $personalMessage);
            $sendReviewForm->setData('personalMessage', $personalMessage);

            $revisionsEmail = $templateMgr->getTemplateVars('revisionsEmail');
            $revisionsEmail = str_replace(self::AUTHOR_FULL_NAME_VARIABLE, $authorFullName, $revisionsEmail);
            $revisionsEmail = str_replace(self::AUTHOR_FIRST_NAME_VARIABLE, $authorFirstName, $revisionsEmail);
            $sendReviewForm->setData('revisionsEmail', $revisionsEmail);

            $resubmitEmail = $templateMgr->getTemplateVars('resubmitEmail');
            $resubmitEmail = str_replace(self::AUTHOR_FULL_NAME_VARIABLE, $authorFullName, $resubmitEmail);
            $resubmitEmail = str_replace(self::AUTHOR_FIRST_NAME_VARIABLE, $authorFirstName, $resubmitEmail);
            $sendReviewForm->setData('resubmitEmail', $resubmitEmail);
        }

        return false;
    }

    /**
     * This is needed to remove submission contributors from the list of recipients and only send the email to the main author
     */
    function editorDecisionEmailsSetRecipients($hookName, $arguments) {
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