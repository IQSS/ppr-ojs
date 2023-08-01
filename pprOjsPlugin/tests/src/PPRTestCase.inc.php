<?php

use PHPUnit\Framework\TestCase;

class PPRTestCase extends TestCase {


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
        $hooksForName = HookRegistry::getHooks($hookName);
        $hooks = [];
        foreach ($hooksForName as $priority => $registeredHooks) {
            $hooks = array_merge($hooks, $registeredHooks);
        }

        return $hooks;
    }

}