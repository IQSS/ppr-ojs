<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.PPRTemplateOverrideService');
import('settings.PPRPluginSettings');
import('PeerPreReviewProgramPlugin');

class PPRTemplateOverrideServiceTest extends PPRTestCase {

    const CONTEXT_ID = 130;
    private $pprPluginMock;

    public function setUp(): void {
        parent::setUp();
        $pluginSettingsMock = $this->createMock(PPRPluginSettings::class);
        $pluginSettingsMock->method($this->anything())->willReturn(true);
        $this->pprPluginMock = $this->createMock(PeerPreReviewProgramPlugin::class);
        $this->pprPluginMock->method('getPluginSettings')->willReturn($pluginSettingsMock);
    }
    
    public function test_register_should_always_register_template_hook() {
        $target = new PPRTemplateOverrideService($this->pprPluginMock);
        $target->register();
        $this->assertEquals(1, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('TemplateResource::getFilename')));
    }

    public function test_overrideTemplate_should_override_template_and_remove_extension_when_template_ends_in_load_ojs() {
        $template = "path/to/template/name.tpl.load_ojs";
        $this->pprPluginMock ->expects($this->never())->method('_overridePluginTemplates');

        $target = new PPRTemplateOverrideService($this->pprPluginMock);
        $target->overrideTemplate('TemplateResource::getFilename', [&$template]);
        $this->assertEquals("path/to/template/name.tpl", $template);
    }

    public function test_overrideTemplate_should_call_plugin_override_template_when_template_is_configured_to_be_overridden() {
        $target = new PPRTemplateOverrideService($this->pprPluginMock);
        $template = $target->getOverriddenTemplates()[0];
        $this->pprPluginMock ->expects($this->once())->method('_overridePluginTemplates')->with('TemplateResource::getFilename', [&$template]);

        $target->overrideTemplate('TemplateResource::getFilename', [&$template]);
    }

    public function test_overrideTemplate_should_not_call_plugin_override_template_when_template_is_not_configured_to_be_overridden() {
        $template = 'not/known/template.tpl';
        $this->pprPluginMock ->expects($this->never())->method('_overridePluginTemplates');

        $target = new PPRTemplateOverrideService($this->pprPluginMock);
        $target->overrideTemplate('TemplateResource::getFilename', [&$template]);
    }

    public function test_expected_overridden_templates_for_each_setting() {
        $expectedTemplatesForSetting = [];
        $expectedTemplatesForSetting['displayWorkflowMessageEnabled'] = [
            'lib/pkp/templates/ppr/workflowInvalidTabMessage.tpl',
            'lib/pkp/templates/controllers/tab/authorDashboard/editorial.tpl',
            'templates/controllers/tab/authorDashboard/production.tpl',
            'lib/pkp/templates/controllers/tab/workflow/editorial.tpl',
            'templates/controllers/tab/workflow/production.tpl',
        ];

        $expectedTemplatesForSetting['hideReviewMethodEnabled'] = [
            'lib/pkp/templates/controllers/grid/users/reviewer/form/reviewerFormFooter.tpl'
        ];

        $expectedTemplatesForSetting['hideReviewFormDefaultEnabled'] = [
            'lib/pkp/templates/controllers/grid/users/reviewer/form/reviewerFormFooter.tpl'
        ];

        $expectedTemplatesForSetting['hideReviewRecommendationEnabled'] = [
            'templates/reviewer/review/step3.tpl',
            'templates/controllers/grid/users/reviewer/readReview.tpl',
        ];

        $expectedTemplatesForSetting['hidePreferredPublicNameEnabled'] = [
            'lib/pkp/templates/common/userDetails.tpl',
        ];

        $expectedTemplatesForSetting['hideUserBioEnabled'] = [
            'lib/pkp/templates/common/userDetails.tpl',
            'lib/pkp/templates/user/publicProfileForm.tpl',
        ];

        $expectedTemplatesForSetting['userCustomFieldsEnabled'] = [
            'lib/pkp/templates/controllers/grid/users/reviewer/form/createReviewerForm.tpl',
            'lib/pkp/templates/common/userDetails.tpl',
            'lib/pkp/templates/frontend/components/registrationForm.tpl',
            'lib/pkp/templates/frontend/pages/userRegister.tpl',
            'lib/pkp/templates/user/contactForm.tpl',
            'lib/pkp/templates/user/identityForm.tpl',
        ];

        $expectedTemplatesForSetting['userOnLeaveEnabled'] = [
            'lib/pkp/templates/common/userDetails.tpl',
            'lib/pkp/templates/user/identityForm.tpl',
        ];

        $expectedTemplatesForSetting['submissionCommentsForReviewerEnabled'] = [
            'lib/pkp/templates/submission/submissionMetadataFormTitleFields.tpl',
            'templates/reviewer/review/step3.tpl',
        ];

        $expectedTemplatesForSetting['submissionResearchTypeEnabled'] = [
            'lib/pkp/templates/submission/submissionMetadataFormTitleFields.tpl',
        ];

        $expectedTemplatesForSetting['submissionHidePrefixEnabled'] = [
            'lib/pkp/templates/submission/submissionMetadataFormTitleFields.tpl',
        ];

        $expectedTemplatesForSetting['submissionCloseEnabled'] = [
            'lib/pkp/templates/workflow/editorialLinkActions.tpl',
        ];

        $expectedTemplatesForSetting['submissionConfirmationChecklistEnabled'] = [
            'lib/pkp/templates/submission/form/step4.tpl',
        ];

        $expectedTemplatesForSetting['submissionUploadFileValidationEnabled'] = [
            'lib/pkp/templates/submission/form/step2.tpl',
            'lib/pkp/templates/ppr/modalMessage.tpl',
        ];

        $expectedTemplatesForSetting['submissionRequestRevisionsFileValidationEnabled'] = [
            'lib/pkp/templates/controllers/modals/editorDecision/form/sendReviewsForm.tpl',
        ];

        $expectedTemplatesForSetting['hideReviewRoundSelectionEnabled'] = [
            'lib/pkp/templates/controllers/modals/editorDecision/form/sendReviewsForm.tpl',
        ];

        $expectedTemplatesForSetting['hideSendToReviewersEnabled'] = [
            'lib/pkp/templates/controllers/modals/editorDecision/form/sendReviewsForm.tpl',
        ];

        $expectedTemplatesForSetting['authorSubmissionSurveyHtml'] = [
            'lib/pkp/templates/submission/form/complete.tpl',
        ];

        $expectedTemplatesForSetting['reviewerSurveyHtml'] = [
            'lib/pkp/templates/reviewer/review/reviewCompleted.tpl',
        ];

        $expectedOverriddenTemplates = array_merge(...array_values($expectedTemplatesForSetting));
        $target = new PPRTemplateOverrideService($this->pprPluginMock);
        // ENSURE ALL CONFIGURED TEMPLATES ARE EXPECTED
        foreach($target->getOverriddenTemplates() as $configuredTemplate) {
            $this->assertEquals(true, in_array($configuredTemplate, $expectedOverriddenTemplates), "Missing template in test: $configuredTemplate");
        }

        // ENSURE ALL EXPECTED TEMPLATES ARE CONFIGURED
        foreach($expectedOverriddenTemplates as $expectedTemplate) {
            $this->assertEquals(true, in_array($expectedTemplate, $target->getOverriddenTemplates()), "Expected template: $expectedTemplate");
        }

        // CHECK EXPECTED TEMPLATES PER SETTINGS
        foreach ($expectedTemplatesForSetting as $setting => $templates) {
            $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, [$setting => true], false);
            $target = new PPRTemplateOverrideService($pprPluginMock);

            foreach($target->getOverriddenTemplates() as $configuredTemplate) {
                $this->assertEquals(true, in_array($configuredTemplate, $templates));
            }

            foreach($templates as $expectedTemplate) {
                $this->assertEquals(true, in_array($expectedTemplate, $target->getOverriddenTemplates()));
            }
        }
    }

}