<?php

import('util.PPRObjectFactory');

/**
 * Utility functions for tests
 */
class PPRTestUtil {

    private $testCase;

    public function __construct($testCase) {
        $this->testCase = $testCase;
    }

    public function createSubmissionWithAuthors($primaryAuthorName, $contributorsNames = []) {
        $primaryAuthor = null;

        if ($primaryAuthorName) {
            $primaryAuthor = $this->createAuthor($this->testCase->getRandomId(), $primaryAuthorName, $primaryAuthorName);
        }

        $contributors = [];
        foreach ($contributorsNames as $name) {
            $contributor = $this->createAuthor($this->testCase->getRandomId(), $name, $name);
            $contributors[] = $contributor;
        }

        $submission = $this->testCase->createMock(Submission::class);
        $submission->method('getId')->willReturn($this->testCase->getRandomId());
        $submission->method('getPrimaryAuthor')->willReturn($primaryAuthor);
        $submission->method('getAuthors')->willReturn($contributors);
        return $submission;
    }

    public function createAuthor($id, $familyName, $givenName = null) {
        $author = $this->testCase->createMock(Author::class);
        $author->method('getId')->willReturn($id);
        $author->method('getLocalizedFamilyName')->willReturn($familyName);
        $author->method('getLocalizedGivenName')->willReturn($givenName);
        $author->method('getFullName')->willReturn($familyName);
        $author->method('getEmail')->willReturn("$familyName@email.com");
        return $author;
    }

    public function createUser($id, $familyName, $givenName = null) {
        $author = $this->testCase->createMock(User::class);
        $author->method('getId')->willReturn($id);
        $author->method('getUsername')->willReturn(strtolower($familyName));
        $author->method('getLocalizedFamilyName')->willReturn($familyName);
        $author->method('getLocalizedGivenName')->willReturn($givenName);
        $author->method('getFullName')->willReturn($familyName);
        $author->method('getEmail')->willReturn("$familyName@email.com");
        return $author;
    }

    public function createSubmission($id) {
        $submission = $this->testCase->createMock(Submission::class);
        $submission->method('getId')->willReturn($id);
        return $submission;
    }

    public function createObjectFactory() {
        $objectFactory = $this->testCase->createMock(PPRObjectFactory::class);
        $submissionUtil = $this->testCase->createMock(PPRSubmissionUtil::class);
        $objectFactory->method('submissionUtil')->willReturn($submissionUtil);

        return $objectFactory;
    }

}