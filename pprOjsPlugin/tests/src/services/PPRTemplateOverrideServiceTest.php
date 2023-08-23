<?php

import('tests.src.PPRTestCase');
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
    
    public function test_overridden_templates() {
        $expectedOverriddenTemplates = [];
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/ppr/workflowInvalidTabMessage.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/controllers/tab/authorDashboard/editorial.tpl';
        $expectedOverriddenTemplates[] = 'templates/controllers/tab/authorDashboard/production.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/controllers/tab/workflow/editorial.tpl';
        $expectedOverriddenTemplates[] = 'templates/controllers/tab/workflow/production.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/controllers/grid/users/reviewer/form/reviewerFormFooter.tpl';
        $expectedOverriddenTemplates[] = 'templates/reviewer/review/step3.tpl';
        $expectedOverriddenTemplates[] = 'templates/controllers/grid/users/reviewer/readReview.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/common/userDetails.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/controllers/grid/users/reviewer/form/createReviewerForm.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/common/userDetails.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/frontend/components/registrationForm.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/frontend/pages/userRegister.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/user/contactForm.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/user/identityForm.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/common/userDetails.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/user/identityForm.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/submission/submissionMetadataFormTitleFields.tpl';
        $expectedOverriddenTemplates[] = 'templates/reviewer/review/step3.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/workflow/editorialLinkActions.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/submission/form/step4.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/submission/form/step2.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/ppr/modalMessage.tpl';
        $expectedOverriddenTemplates[] = 'lib/pkp/templates/controllers/modals/editorDecision/form/sendReviewsForm.tpl';

        $target = new PPRTemplateOverrideService($this->pprPluginMock);
        // ENSURE ALL CONFIGURED TEMPLATES ARE EXPECTED
        foreach($target->getOverriddenTemplates() as $configuredTemplate) {
            $this->assertEquals(true, in_array($configuredTemplate, $expectedOverriddenTemplates));
        }

        // ENSURE ALL EXPECTED TEMPLATES ARE CONFIGURED
        foreach($expectedOverriddenTemplates as $expectedTemplate) {
            $this->assertEquals(true, in_array($expectedTemplate, $target->getOverriddenTemplates()));
        }

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

}