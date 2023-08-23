<?php

use PHPUnit\Framework\TestCase;

import('classes.core.Request');

class PPRTestCase extends TestCase {

    public function setUp(): void {
        parent::setUp();

        $request = $this->createMock(Request::class);
        AppLocale::initialize($request);

        //RESET HOOKS ON EVERY CALL
        $emptyHooks = [];
        Registry::set('hooks', $emptyHooks);
    }


    public function countHooks() {
        $hookList = HookRegistry::getHooks();
        $totalHooks = 0;
        foreach ($hookList as $hookName => $hooksByPriority) {
            foreach ($hooksByPriority as $priority => $registeredHooks) {
                $totalHooks += count($registeredHooks);
            }
        }

        return $totalHooks;
    }

    public function getHooks($hookName) {
        $hooksForName = HookRegistry::getHooks($hookName) ?? [];
        $hooks = [];
        foreach ($hooksForName as $priority => $registeredHooks) {
            $hooks = array_merge($hooks, $registeredHooks);
        }

        return $hooks;
    }

    public function getRandomId() {
        return rand(10000, 9999999);
    }

}