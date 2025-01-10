<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('settings.PPRReportPluginSettings');

class PPRReportPluginSettingsTest extends PPRTestCase {

    const CONTEXT_ID = 100;

    public function setUp(): void {
        parent::setUp();
    }

    public function test_default_values() {
        $expectedDefaultValues = array(
            // fieldName => [methodName, defaultValue]
            'submissionsReviewsReportEnabled' => [null, null],
            'submissionsReviewsReportRecipients' => [null, []],
            'globalEmailSender' => [null, 'peerprereview@iq.harvard.edu'],
        );

        foreach (PPRReportPluginSettings::CONFIG_VARS as $configVar => $varInfo) {
            $this->assertEquals(true, array_key_exists($configVar, $expectedDefaultValues), "PPR settings default value not tested for: $configVar");
        }

        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, []);
        $target = new PPRReportPluginSettings(self::CONTEXT_ID, $pprPluginMock);
        foreach ($expectedDefaultValues as $configVar => $varInfo) {
            $result = call_user_func([$target, $varInfo[0] ?? $configVar]);
            $this->assertEquals($varInfo[1], $result, "Error for config var: $configVar");
        }
    }

    public function test_submissionsReviewsReportRecipients_splits_comma_separated_string_values_into_map() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['submissionsReviewsReportRecipients' => 'first, second, third']);
        $target = new PPRReportPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals(['first', 'second', 'third'], $target->submissionsReviewsReportRecipients());
    }

    public function test_submissionsReviewsReportRecipients_handles_empty_string() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['submissionsReviewsReportRecipients' => '']);
        $target = new PPRReportPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals([], $target->submissionsReviewsReportRecipients());
    }

    public function test_submissionsReviewsReportRecipients_should_filter_empty_strings() {
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['submissionsReviewsReportRecipients' => 'first, ,second']);
        $target = new PPRReportPluginSettings(self::CONTEXT_ID, $pprPluginMock);

        $this->assertEquals(['first', 'second'], $target->submissionsReviewsReportRecipients());
    }
}