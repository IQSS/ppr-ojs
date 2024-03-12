<?php

import('tests.src.PPRTestCase');

import('lib.pkp.classes.scheduledTask.ScheduledTaskHelper');
import('lib.pkp.classes.scheduledTask.ScheduledTaskDAO');

class PPRScheduledTaskHelperTest extends PPRTestCase {

    public function test_remove_warning() {
        $this->assertTrue(true);
    }

    /**
     * Method to run manual tests for schedule tasks frequencies.
     * add test_ in from of the method name and adjust the body to suit your use case.
     *
     * Cannot add a formal test as I cannot find a way of mocking the PHP builtin strtotime
     */
    public function test_run_manual_checks_for_frequency() {
        $className = 'classname.to.find';

        $taskDao = $this->createMock(ScheduledTaskDAO::class);
        strtotime('2024-03-12 00:00:00');
        $taskDao->expects($this->once())->method('getLastRunTime')->with($className)->willReturn(strtotime('2024-02-10 00:00:00'));
        DAORegistry::registerDAO('ScheduledTaskDAO', $taskDao);

        $frequency = new XMLNode();
        $frequency->setAttribute('month', '1,2,3,4,5,6,7,8,9,10,11,12');

        $result = ScheduledTaskHelper::checkFrequency($className, $frequency);
        $this->assertEquals(true, $result);
    }

}