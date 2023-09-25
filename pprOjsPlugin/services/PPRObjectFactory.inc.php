<?php

/**
 * Service inject OJS classes into PPR services
 * This is useful for testing
 */
class PPRObjectFactory {

    public function submissionMailTemplate($submission, $emailKey = null, $locale = null, $context = null, $includeSignature = true) {
        import('lib.pkp.classes.mail.SubmissionMailTemplate');
        return new SubmissionMailTemplate($submission, $emailKey, $locale, $context, $includeSignature);
    }

}