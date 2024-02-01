<?php

import('lib.pkp.controllers.grid.users.stageParticipant.StageParticipantGridHandler');

/**
 * Overriding the StageParticipantGridHandler to add author, editor, and reviewer first names to the email template body
 */
class PPRStageParticipantGridHandler extends StageParticipantGridHandler {

    private $pprObjectFactory;

    function __construct($pprObjectFactory = null) {
        $this->pprObjectFactory = $pprObjectFactory ?: new PPRObjectFactory();
        parent::__construct();
    }

    /**
     * This method is a copy of the parent method, only adding first names to the body template variable.
     */
    function fetchTemplateBody($args, $request) {
        $templateId = $request->getUserVar('template');
        import('lib.pkp.classes.mail.SubmissionMailTemplate');
        // PPR UPDATE TO FACILITATE TESTING
        $template = $this->pprObjectFactory->submissionMailTemplate($this->getSubmission(), $templateId);
        if ($template) {
            $user = $request->getUser();
            $dispatcher = $request->getDispatcher();
            $context = $request->getContext();
            $template->assignParams(array(
                'editorialContactSignature' => $user->getContactSignature(),
                'signatureFullName' => htmlspecialchars($user->getFullname()),
            ));
            $template->replaceParams();

            // PPR UPDATE TO REPLACE FIRST NAMES
            $body = $this->pprObjectFactory->firstNamesManagementService()->replaceFirstNames($template->getBody(), $this->getSubmission());

            import('controllers.grid.users.stageParticipant.form.StageParticipantNotifyForm'); // exists in each app.
            $notifyForm = new StageParticipantNotifyForm($this->getSubmission()->getId(), ASSOC_TYPE_SUBMISSION, $this->getAuthorizedContextObject(ASSOC_TYPE_WORKFLOW_STAGE));
            return new JSONMessage(
                true,
                array(
                    'body' => $body,
                    'variables' => $notifyForm->getEmailVariableNames($templateId),
                )
            );
        }
    }

}