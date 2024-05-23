<?php

require_once(dirname(__FILE__) . '/PPRMissingUser.inc.php');

/**
 * Service to get author, editor, and reviewer information and replace template variables with the values.
 */
class PPRFirstNamesManagementService {

    private $pprSubmissionUtil;

    public function __construct($pprSubmissionUtil) {
        $this->pprSubmissionUtil = $pprSubmissionUtil;
    }

    public function addFirstNameLabelsToTemplate($templateVariableName) {
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
        $emailVariables['contributorsNames'] = __('review.ppr.author.contributorsNames.label');
        $emailVariables['editorName'] = __('review.ppr.editor.name.label');
        $emailVariables['editorFullName'] = __('review.ppr.editor.name.label');
        $emailVariables['editorFirstName'] = __('review.ppr.editor.firstName.label');
        $templateMgr->assign($templateVariableName, $emailVariables);
    }

    public function addFirstNamesToEmailTemplate($emailTemplate) {
        $submission = $emailTemplate->submission;
        if (!$submission) {
            error_log(sprintf("PPR[PPRFirstNameReplacementService] emailTemplate=%s - submission is null  - skip", $emailTemplate->emailKey));
            return false;
        }

        $submissionAuthor = $this->getSubmissionAuthor($submission->getId());
        $contributorsNames = $this->getContributorsNames($submission, $submissionAuthor);

        $contextId = $submission->getContextId();
        $submissionEditor = $this->getSubmissionEditor($submission->getId(), $contextId);

        // CHECK THE REVIEWER ID MARKER IN TEMPLATE
        $reviewerId = $emailTemplate->getData('reviewerId');
        $requestReviewer = $this->getReviewer($reviewerId);

        $tempTemplate = $this->replaceParams($emailTemplate->getBody(), $emailTemplate->getSubject(), $submissionAuthor, $requestReviewer, $submissionEditor, $contributorsNames);
        $emailTemplate->setBody($tempTemplate->getBody());
        $emailTemplate->setSubject($tempTemplate->getSubject());
    }

    /**
     *
     */
    public function replaceFirstNames($originalText, $submission, $reviewerId = null) {
        $reviewer = $this->getReviewer($reviewerId);
        $editor = $submission ? $this->getSubmissionEditor($submission->getId(), $submission->getContextId()) : PPRMissingUser::defaultMissingUser();
        $author = $submission ? $this->getSubmissionAuthor($submission->getId()) : PPRMissingUser::defaultMissingUser();
        $contributorsNames = $submission ? $this->getContributorsNames($submission, $author) : PPRMissingUser::defaultMissingUser()->getLocalizedGivenName();

        $mailTemplate = $this->replaceParams($originalText, '', $author, $reviewer, $editor, $contributorsNames);
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
        } elseif ($request->getRouter()->getHandler()) {
            $review = $request->getRouter()->getHandler()->getAuthorizedContextObject(ASSOC_TYPE_REVIEW_ASSIGNMENT);
            // LAST CHANCE TO GET A reviewAssignment OBJECT
            if ($review) {
                $reviewer = $this->pprSubmissionUtil->getReviewer($review->getId());
            }
        }

        return $reviewer ?? PPRMissingUser::defaultMissingUser();
    }

    public function getContributorsNames($submission, $author) {
        // USE ALL CONTRIBUTORS OR JUST THE AUTHOR.
        // THIS IS DRIVEN FROM THE SUBMISSION CUSTOM FIELD emailContributors
        // THAT THE AUTHOR POPULATES WHEN A SUBMISSION IS CREATED
        $emailContributors = $submission->getData('emailContributors');
        $contributorsNames = [$author->getLocalizedGivenName()];
        if ($emailContributors) {
            foreach ($submission->getAuthors() as $contributor) {
                if(0 === strcasecmp($author->getEmail(), $contributor->getEmail())) {
                    // AUTHOR ALREADY ADDED => SKIP
                    continue;
                }

                $contributorsNames[] = $contributor->getLocalizedGivenName();
            }
        }
        return implode(", ", $contributorsNames);
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

    private function replaceParams($body, $subject, $author, $reviewer, $editor, $contributorsNames) {
        $mailTemplate = new MailTemplate();
        $mailTemplate->setBody($body);
        $mailTemplate->setSubject($subject);
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
            'contributorsNames' => htmlspecialchars($contributorsNames),
        ]);
        $mailTemplate->replaceParams();
        return $mailTemplate;
    }
}