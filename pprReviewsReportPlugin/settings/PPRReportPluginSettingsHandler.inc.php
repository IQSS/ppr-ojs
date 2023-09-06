<?php

/**
 * Handler for the plugin setting actions
 */
class PPRReportPluginSettingsHandler {

    private $pprPlugin;

    public function __construct($pprPlugin) {
        $this->pprPlugin = $pprPlugin;
    }

    function getActions($request, $actionArgs) {
        $router = $request->getRouter();
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        return array(
            new LinkAction(
                'report_settings',
                new AjaxModal($router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->pprPlugin->getName(), 'category' => 'reports')), $this->pprPlugin->getDisplayName()),
                __('manager.plugins.settings'),
                null
            ),
        );
    }

    function manage($args, $request) {
        $context = $request->getContext();
        $contextId = ($context == null) ? 0 : $context->getId();

        $this->pprPlugin->import('settings.PPRReportPluginSettingsForm');
        $form = new PPRReportPluginSettingsForm($this->pprPlugin, $contextId);
        if ($request->getUserVar('save')) {
            $form->readInputData();
            if ($form->validate()) {
                $form->execute();
                return new JSONMessage(true);
            }
        } else {
            $form->initData();
        }

        return new JSONMessage(true, $form->fetch($request));
    }
}