<?php

import('tests.src.PPRTestCase');
import('PPRReviewsReportPlugin');

class PPRReviewsReportPluginTest extends PPRTestCase {

    public function setUp(): void {
        parent::setUp();
    }

    public function test_register_should_initialize_plugin() {
        $target = new PPRReviewsReportPlugin();
        $result = $target->register('reports', 'plugins/reports/pprReviewsReportPlugin', null);

        $this->assertTrue($result);
        $this->assertEquals(1, count($this->getHooks('AcronPlugin::parseCronTab')));
        $this->assertNotNull($target->getPluginSettings());
        $this->assertNotNull($target->getPluginSettingsHandler());
    }
}