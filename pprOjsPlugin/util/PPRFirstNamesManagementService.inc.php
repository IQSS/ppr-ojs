<?php

require_once(dirname(__FILE__) . '/PPRMissingUser.inc.php');

/**
 * Service to get author, editor, and reviewer information and replace template variables with the values.
 */
class PPRFirstNamesManagementService {

    private $pprSubmissionUtil;

    function __construct($pprSubmissionUtil) {
        $this->pprSubmissionUtil = $pprSubmissionUtil;
    }

    function addFirstNameLabelsToTemplate($templateVariableName) {
        // ADD FIRST NAME LABELS FOR REVIEWER, AUTHOR, AND EDITOR IN THE EMAIL BODY EDITOR IN THE FORM
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
        $emailVariables = $templateMgr->getTemplateVars($templateVariableName) ?? [];
        $emailVariables['reviewerName'] = __('review.ppr.reviewer.name.label');
        $emailVariables['reviewerFullName'] = __('review.ppr.reviewer.name.label');
        $emailVariables['reviewerFirstName'] = __('review.ppr.reviewer.firstName.label');
        // firstNameOnly (REVIEWER) IS DEPRECATED. ADDED HERE FOR BACKWARDS COMPATIBILITY FOR advancedsearchreviewerform
        $emailVariables['firstNameOnly'] = __('review.ppr.reviewer.firstName.label');
        $emailVariables['authorName'] = __('review.ppr.author.name.label');
        $emailVariables['authorFullName'] = __('review.ppr.author.name.label');
        $emailVariables['authorFirstName'] = __('review.ppr.author.firstName.label');
        $emailVariables['editorName'] = __('review.ppr.editor.name.label');
        $emailVariables['editorFullName'] = __('review.ppr.editor.name.label');
        $emailVariables['editorFirstName'] = __('review.ppr.editor.firstName.label');
        $templateMgr->assign($templateVariableName, $emailVariables);
    }

    function addFirstNamesToEmailTemplate($emailTemplate) {
        $submission = $emailTemplate->submission;
        if (!$submission) {
            error_log(sprintf("PPR[PPRFirstNameReplacementService] emailTemplate=%s - submission is null  - skip", $emailTemplate->emailKey));
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

        // CHECK THE REVIEWER ID MARKER IN TEMPLATE OR REQUEST PARAMETER
        $reviewerId = $emailTemplate->getData('reviewerId');
        $requestReviewer = $this->getReviewer($reviewerId);
        $emailTemplate->addPrivateParam('{$reviewerName}', htmlspecialchars($requestReviewer->getFullName()));
        $emailTemplate->addPrivateParam('{$reviewerFullName}', htmlspecialchars($requestReviewer->getFullName()));
        $emailTemplate->addPrivateParam('{$reviewerFirstName}', htmlspecialchars($requestReviewer->getLocalizedGivenName()));
        // firstNameOnly (REVIEWER) IS DEPRECATED. ADDED HERE FOR BACKWARDS COMPATIBILITY FOR advancedsearchreviewerform
        $emailTemplate->addPrivateParam('{$firstNameOnly}', htmlspecialchars($requestReviewer->getLocalizedGivenName()));
    }

    /**
     *
     */
    public function replaceFirstNames($originalText, $submission, $reviewerId = null) {
        $reviewer = $this->getReviewer($reviewerId);
        $editor = $submission ? $this->getSubmissionEditor($submission->getId(), $submission->getContextId()) : PPRMissingUser::defaultMissingUser();
        $author = $submission ? $this->getSubmissionAuthor($submission->getId()) : PPRMissingUser::defaultMissingUser();

        // WE NEED ANY EMAIL TEMPLATE TO OVERRIDE THE BODY AND USE THE replaceParams METHOD
        $mailTemplate = new MailTemplate();
        $mailTemplate->setBody($originalText);
        $mailTemplate->assignParams([
            'reviewerName' =>  htmlspecialchars($reviewer->getFullName()),
            'reviewerFullName' =>  htmlspecialchars($reviewer->getFullName()),
            'reviewerFirstName' =>  htmlspecialchars($reviewer->getLocalizedGivenName()),
            // firstNameOnly (REVIEWER) IS DEPRECATED. ADDED HERE FOR BACKWARDS COMPATIBILITY FOR advancedsearchreviewerform
            'firstNameOnly' =>  htmlspecialchars($reviewer->getLocalizedGivenName()),
            'editorName' => htmlspecialchars($editor->getFullName()),
            'editorFullName' => htmlspecialchars($editor->getFullName()),
            'editorFirstName' => htmlspecialchars($editor->getLocalizedGivenName()),
            'authorName' => htmlspecialchars($author->getFullName()),
            'authorFullName' => htmlspecialchars($author->getFullName()),
            'authorFirstName' => htmlspecialchars($author->getLocalizedGivenName()),
        ]);
        $mailTemplate->replaceParams();
        return $mailTemplate->getBody();
    }

    public function getReviewer($reviewerId) {
        $request = Application::get()->getRequest();
        $reviewer = null;
        if ($reviewerId) {
            $reviewer = $this->pprSubmissionUtil->getUser($reviewerId);
        } elseif ($reviewerId = $request->getUserVar('reviewerId')) {
            // TRY reviewerId REQUEST PARAMETER
            $reviewer = $this->pprSubmissionUtil->getUser($reviewerId);
        } elseif ($reviewId = $request->getUserVar('reviewAssignmentId')) {
            // TRY reviewAssignment REQUEST PARAMETER
            $reviewer = $this->pprSubmissionUtil->getReviewer($reviewId);
        } elseif ($review = $request->getRouter()->getHandler()->getAuthorizedContextObject(ASSOC_TYPE_REVIEW_ASSIGNMENT)) {
            // LAST CHANCE TO GET A reviewAssignment OBJECT
            $reviewer = $this->pprSubmissionUtil->getReviewer($review->getId());
        }

        return $reviewer ?? PPRMissingUser::defaultMissingUser();
    }

    private function getSubmissionEditor($submissionId, $contextId) {
        $submissionEditors = $this->pprSubmissionUtil->getSubmissionEditors($submissionId, $contextId);
        //GET FIRST EDITOR
        return empty($submissionEditors) ? PPRMissingUser::defaultMissingUser() : reset($submissionEditors);
    }

    private function getSubmissionAuthor($submissionId) {
        $submissionAuthors = $this->pprSubmissionUtil->getSubmissionAuthors($submissionId);
        //GET FIRST AUTHOR
        return empty($submissionAuthors) ? PPRMissingUser::defaultMissingUser() : reset($submissionAuthors);
    }
}