<?php

import('tests.src.PPRTestCase');
import('settings.PPRPluginSettingsForm');
import('settings.PPRAccessKeyLifeTimeValidator');

class PPRAccessKeyLifeTimeValidatorTest extends PPRTestCase {

    public function test_null_values_are_valid() {
        $formMock = $this->createMock(PPRPluginSettingsForm::class);
        $formMock->expects($this->atLeast(1))->method('getData')->with('accessKeyLifeTime')->willReturn(null);
        $target = new PPRAccessKeyLifeTimeValidator($formMock);

        $this->assertEquals(true, $target->isValid());
    }

    public function test_empty_values_are_valid() {
        $formMock = $this->createMock(PPRPluginSettingsForm::class);
        $formMock->expects($this->atLeast(1))->method('getData')->with('accessKeyLifeTime')->willReturn('');
        $target = new PPRAccessKeyLifeTimeValidator($formMock);

        $this->assertEquals(true, $target->isValid());
    }

    public function test_negative_values_are_not_valid() {
        $formMock = $this->createMock(PPRPluginSettingsForm::class);
        $formMock->expects($this->atLeast(1))->method('getData')->with('accessKeyLifeTime')->willReturn('-10');
        $target = new PPRAccessKeyLifeTimeValidator($formMock);

        $this->assertEquals(false, $target->isValid());
    }

    public function test_values_bigger_than_100_are_not_valid() {
        $formMock = $this->createMock(PPRPluginSettingsForm::class);
        $formMock->expects($this->atLeast(1))->method('getData')->with('accessKeyLifeTime')->willReturn('101');
        $target = new PPRAccessKeyLifeTimeValidator($formMock);

        $this->assertEquals(false, $target->isValid());
    }
}