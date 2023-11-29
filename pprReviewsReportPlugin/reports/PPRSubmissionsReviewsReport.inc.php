<?php

require_once(dirname(__FILE__) . '/../util/PPRReportUtil.inc.php');

/**
 * PPR Submission and Review report
 */
class PPRSubmissionsReviewsReport {

    const PPR_MISSING_DATA = '';
    private $userCache = [];

    function createReport($file, $contextId) {
        $fp = fopen($file, 'wt');
        // Add BOM (byte order mark) to fix UTF-8 in Excel
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

        $reportUtil = new PPRReportUtil();

        $reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO');
        $submissionDao = DAORegistry::getDAO('SubmissionDAO');
        $editDecisionDao = DAORegistry::getDAO('EditDecisionDAO');
        $stageAssignmentDao = DAORegistry::getDAO('StageAssignmentDAO');

        [$editorUserGroupId, $authorUserGroupId] = $this->getGroupIds($contextId);

        $allSubmissions = $submissionDao->getByContextId($contextId);
        $reportData = [];
        while ($submission = $allSubmissions->next()) {
            $submissionData = [];
            $submissionData['submission'] = $submission;
            $submissionData['decisions'] =  $editDecisionDao->getEditorDecisions($submission->getId());
            $submissionData['reviews'] =  $reviewAssignmentDao->getBySubmissionId($submission->getId());
            $submissionData['submissionAssignments'] = $stageAssignmentDao->getBySubmissionAndStageId($submission->getId())->toArray();
            $reportData[] = $submissionData;
        }

        //PRINT HEADERS
        $headers = ['OJS ID', 'Authors Name', 'Title of Document', 'Authors Email', 'Authors Category', 'Authors Institution', 'Authors Department', 'Research Document Type', 'Associate Editor', 'Review Status', 'Author - Paper Received'];
        $headers = array_merge($headers, ['Coauthors Name', 'Coauthors Institute', 'Coauthors Email', 'Coauthors Category', 'Coauthors Department']);
        $headers = array_merge($headers, ['Reviewer', 'Reviewer Email', 'Reviewer Institution', 'Reviewer - 1st Email', 'Reviewer - Sent for Review', 'Reviewer - Response Time', 'Reviewer - Due Date']);
        $headers = array_merge($headers, ['Reviewer - Paper Returned', 'Reviewer - Time (days)', 'Author - Date Returned', 'Author - Review Time (days)']);
        fputcsv($fp, $headers);

        foreach ($reportData as $submissionData) {
            $submission = $submissionData['submission'];
            $publication = $submission->getCurrentPublication();
            if (!$submission->getDateSubmitted()) {
                // SUBMISSION IS PENDING => IGNORE DATA
                continue;
            }

            $submissionSentToReview = false;
            // FIND AUTHOR AND EDITOR FROM ASSIGNMENTS
            $stageAssignments = $submissionData['submissionAssignments'];
            $author = null;
            $editor = null;
            foreach ($stageAssignments as $stageAssignment) {
                if ($stageAssignment->getUserGroupId() === $authorUserGroupId){
                    if ($author) continue;
                    $author = $this->getUser($stageAssignment->getUserId());
                }

                if ($stageAssignment->getUserGroupId() === $editorUserGroupId){
                    if ($editor) continue;
                    $editor = $this->getUser($stageAssignment->getUserId());
                }
            }

            // GET REQUEST REVISIONS DECISION
            $requestRevisionDates = [];
            foreach ($submissionData['decisions'] as $decision) {
                if ($decision['decision'] === SUBMISSION_EDITOR_DECISION_EXTERNAL_REVIEW) {
                    $submissionSentToReview = true;
                }
                if ($decision['decision'] === SUBMISSION_EDITOR_DECISION_PENDING_REVISIONS) {
                    $requestRevisionDates[] = $reportUtil->formatDate($decision['dateDecided']);
                }
            }

            $reviews = array_values($submissionData['reviews']);
            // WE WANT A ROW PER REVIEW, BUT AT LEAST ONE ROW PER SUBMISSION
            $reviews = empty($reviews) ? [null] : $reviews;
            foreach ($reviews as $review) {
                $row = [];
                $row[] = $submission->getId();
                $row[] = $author ? $author->getFullName() : self::PPR_MISSING_DATA;
                $row[] = $publication->getLocalizedFullTitle();
                $row[] = $author ? $author->getData('email') : self::PPR_MISSING_DATA;
                $row[] = $author ? $author->getData('category') : self::PPR_MISSING_DATA;
                $row[] = $author ? $author->getLocalizedData('affiliation') : self::PPR_MISSING_DATA;
                $row[] = $author ? $author->getData('department') : self::PPR_MISSING_DATA;

                $row[] = $submission->getData('researchType');

                $row[] = $editor ? $editor->getFullName() : self::PPR_MISSING_DATA;
                $row[] = $reportUtil->getStatusText($submissionSentToReview, $review ? $review->getStatus(): null);
                $row[] = $reportUtil->formatDate($submission->getDateSubmitted());

                $contributors = $publication->getData('authors');
                $coauthors = $coauthorsInstitution = $coauthorsEmail = $coauthorsCategory = $coauthorsDepartment = [];
                foreach ($contributors as $contributor) {
                    // IGNORE AUTHOR IN LIST OF CONTRIBUTORS
                    if ($author && $contributor->getData('email') === $author->getData('email')) continue;
                    $coauthors[] = $contributor->getFullName();
                    $coauthorsInstitution[] = $contributor->getLocalizedData('affiliation');
                    $coauthorsEmail[] = $contributor->getData('email');
                    $coauthorsCategory[] = $contributor->getData('category');
                    $coauthorsDepartment[] = $contributor->getData('department');
                }
                $row[] = implode(", ", $coauthors);
                $row[] = implode(", ", $coauthorsInstitution);
                $row[] = implode(", ", $coauthorsEmail);
                $row[] = implode(", ", $coauthorsCategory);
                $row[] = implode(", ", $coauthorsDepartment);

                // REVIEWER DATA => SIMPLE CHECK TO KNOW IF THERE IS VALID REVIEW
                if ($review) {
                    $reviewerId = $review->getReviewerId();
                    $reviewer = $this->getUser($reviewerId);

                    $row[] = $reviewer->getFullName();
                    $row[] = $reviewer->getData('email');
                    $row[] = $reviewer->getLocalizedData('affiliation');
                    $row[] = $reportUtil->formatDate($review->getDateAssigned());
                    $row[] = $reportUtil->formatDate($review->getDateConfirmed());

                    $assignedDate = $reportUtil->stringToDate($review->getDateAssigned());
                    $confirmedDate = $reportUtil->stringToDate($review->getDateConfirmed());
                    $completedDate = $reportUtil->stringToDate($review->getDateCompleted());

                    $row[] = $review->getDateConfirmed() ? $assignedDate->diff($confirmedDate)->days : self::PPR_MISSING_DATA;
                    $row[] = $reportUtil->formatDate($review->getDateDue());
                    $row[] = $reportUtil->formatDate($review->getDateCompleted());
                    $row[] = $review->getDateCompleted() ? $assignedDate->diff($completedDate)->days : self::PPR_MISSING_DATA;

                    if ($review->getDateCompleted()) {
                        // ONLY ADD THESE DATES IF REVIEW COMPLETED
                        $row[] = implode(", ", $requestRevisionDates);

                        $submissionDate = $reportUtil->stringToDate($submission->getDateSubmitted());
                        $firstRequestRevision = reset($requestRevisionDates) ?? null;
                        $submissionLag = $firstRequestRevision ? $submissionDate->diff($reportUtil->stringToDate($firstRequestRevision))->days : null;
                        $row[] = $submissionLag;
                    }
                }

                fputcsv($fp, $row);
            }
        }

        fclose($fp);
    }

    /**
     * Get the associate editor and author group ids to identify users assigned to the submission.
     */
    private function getGroupIds($contextId) {
        $AUTHOR_GROUP_NAME = __('plugins.report.pprReviewsPlugin.author.groupName');
        $ASSOCIATE_EDITOR_GROUP_NAME = __('plugins.report.pprReviewsPlugin.editor.groupName');

        $userGroupDao = DAORegistry::getDAO('UserGroupDAO');
        $userGroups = $userGroupDao->getByContextId($contextId)->toArray();

        $editorGroup = array_filter($userGroups, function($userGroup) use ($ASSOCIATE_EDITOR_GROUP_NAME) {
            return (0 === strcasecmp($userGroup->getLocalizedName(), $ASSOCIATE_EDITOR_GROUP_NAME));
        });
        $editorGroup = reset($editorGroup) ?? null;
        $editorUserGroupId = $editorGroup ? $editorGroup->getId() : null;

        $authorGroup = array_filter($userGroups, function($userGroup) use ($AUTHOR_GROUP_NAME) {
            return (0 === strcasecmp($userGroup->getLocalizedName(), $AUTHOR_GROUP_NAME));
        });
        $authorGroup = reset($authorGroup) ?? null;
        $authorUserGroupId = $authorGroup ? $authorGroup->getId() : null;

        return [$editorUserGroupId, $authorUserGroupId];
    }

    private function getUser($userId) {
        if(!isset($this->userCache[$userId])) {
            $userDao = DAORegistry::getDAO('UserDAO');
            $this->userCache[$userId] = $userDao->getById($userId);
        }

        return $this->userCache[$userId];
    }

}

