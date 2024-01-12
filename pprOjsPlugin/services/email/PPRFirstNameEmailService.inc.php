<?php

/**
 * Service to add author and editor first name to all SubmissionMailTemplates
 */
class PPRFirstNameEmailService {
    const SUPPORTED_TEMPLATES =
        [
            //BATCH 1
            'REVIEW_REMIND',
            'REVIEW_REMIND_ONECLICK',
            'PPR_REVIEW_REQUEST_DUE_DATE_REVIEWER',
            'PPR_REVIEW_DUE_DATE_EDITOR',
            'PPR_REVIEW_DUE_DATE_WITH_FILES_REVIEWER'.
            'PPR_REVIEW_DUE_DATE_REVIEWER',
            'PPR_REVIEW_PENDING_WITH_FILES_REVIEWER',
            //BATCH 2
            'PPR_REVIEW_ACCEPTED',
            'PPR_REVIEW_SUBMITTED',
            'PPR_SUBMISSION_APPROVED',
            'PPR_REVIEW_SENT_AUTHOR',
        ];

    private $pprPlugin;
    private $pprObjectFactory;

    function __construct($plugin, $pprObjectFactory = null) {
        $this->pprPlugin = $plugin;
        $this->pprObjectFactory = $pprObjectFactory ?: new PPRObjectFactory();
        $this->pprPlugin->import('util.PPRMissingUser');
    }

    function register() {
        if ($this->pprPlugin->getPluginSettings()->firstNameEmailEnabled()) {
            HookRegistry::register('Mail::send', array($this, 'addFirstName'));
        }
    }

    function addFirstName($hookName, $arguments) {
        $emailTemplate = $arguments[0];
        if ($this->isTemplateSupported($emailTemplate)) {
            $submission = $emailTemplate->submission;
            if (!$submission) {
                error_log("PPR[PPRFirstNameEmailService] submission is null - skip");
                return false;
            }

            // SETTING PRIVATE PARAMS IN THE EMAIL TEMPLATE WILL GET REPLACED IN THE BODY AFTER THIS HOOK COMPLETES
            // AT THIS POINT REGULAR PARAMETERS HAVE ALREADY BEEN REPLACED
            $submissionAuthor = $this->getSubmissionAuthor($submission->getId());
            $emailTemplate->addPrivateParam('{$authorName}', htmlspecialchars($submissionAuthor->getFullName()));
            $emailTemplate->addPrivateParam('{$authorFullName}', htmlspecialchars($submissionAuthor->getFullName()));
            $emailTemplate->addPrivateParam('{$authorFirstName}', htmlspecialchars($submissionAuthor->getLocalizedGivenName()));

            $contextId = $submission->getContextId();
            $submissionEditor = $this->getSubmissionEditor($submission->getId(), $contextId);
            $emailTemplate->addPrivateParam('{$editorName}', htmlspecialchars($submissionEditor->getFullName()));
            $emailTemplate->addPrivateParam('{$editorFullName}', htmlspecialchars($submissionEditor->getFullName()));
            $emailTemplate->addPrivateParam('{$editorFirstName}', htmlspecialchars($submissionEditor->getLocalizedGivenName()));

            $requestReviewer = $this->getReviewer($emailTemplate);
            $emailTemplate->addPrivateParam('{$reviewerName}', htmlspecialchars($requestReviewer->getFullName()));
            $emailTemplate->addPrivateParam('{$reviewerFullName}', htmlspecialchars($requestReviewer->getFullName()));
            $emailTemplate->addPrivateParam('{$reviewerFirstName}', htmlspecialchars($requestReviewer->getLocalizedGivenName()));
        }

        return false;
    }

    public function isTemplateSupported($emailTemplate) {
        return $emailTemplate instanceof SubmissionMailTemplate && in_array($emailTemplate->emailKey,self::SUPPORTED_TEMPLATES);
    }

    public function getReviewer($emailTemplate) {
        // CHECK THE REVIEWER ID MARKER IN TEMPLATE
        $reviewerId = $emailTemplate->getData('reviewerId');
        $reviewer = null;
        if($reviewerId) {
            $reviewer = $this->pprObjectFactory->submissionUtil()->getUser($reviewerId);
        } else {
            // TRY THE REQUEST PARAMETER
            $request = Application::get()->getRequest();
            $reviewId = $request->getUserVar('reviewAssignmentId');
            if ($reviewId) {
                $reviewer = $this->pprObjectFactory->submissionUtil()->getReviewer($reviewId);
            }
        }

        return $reviewer ?? PPRMissingUser::defaultMissingUser();
    }

    private function getSubmissionEditor($submissionId, $contextId) {
        $submissionEditors = $this->pprObjectFactory->submissionUtil()->getSubmissionEditors($submissionId, $contextId);
        //GET FIRST EDITOR
        return empty($submissionEditors) ? PPRMissingUser::defaultMissingUser() : reset($submissionEditors);
    }

    private function getSubmissionAuthor($submissionId) {
        $submissionAuthors = $this->pprObjectFactory->submissionUtil()->getSubmissionAuthors($submissionId);
        //GET FIRST AUTHOR
        return empty($submissionAuthors) ? PPRMissingUser::defaultMissingUser() : reset($submissionAuthors);
    }
}