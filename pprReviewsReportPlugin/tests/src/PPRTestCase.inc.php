<?php
import('classes.core.Request');

import('lib.pkp.classes.site.Site');
import('lib.pkp.classes.user.User');

use PHPUnit\Framework\TestCase;

class PPRTestCase extends TestCase {

    private $requestMock;

    public function setUp(): void {
        parent::setUp();

        $this->requestMock = $this->createMock(Request::class);
        $requestUser = new User();
        $requestUser->_data = ['givenName' => ['en_US' => 'Request'], 'familyName' => ['en_US' => 'User']];
        $this->requestMock->method('getUser')->willReturn($requestUser);
        $this->requestMock->method('getSite')->willReturn(new Site());
        Registry::set('request', $this->requestMock);
        AppLocale::initialize($this->requestMock);

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