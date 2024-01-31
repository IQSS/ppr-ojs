<?php

use PHPUnit\Framework\TestCase;

import('tests.src.PPRTestUtil');

import('classes.core.Request');
import('classes.core.Application');
import('lib.pkp.classes.core.PKPRouter');
import('lib.pkp.classes.core.Dispatcher');
import('lib.pkp.classes.handler.PKPHandler');

import('lib.pkp.classes.site.Site');
import('lib.pkp.classes.user.User');

class PPRTestCase extends TestCase {

    private $requestMock;
    private $dispatcherMock;

    private $pprTestUtil;

    public function setUp(): void {
        parent::setUp();

        $this->pprTestUtil = new PPRTestUtil($this);

        $this->requestMock = $this->createMock(Request::class);

        $requestUser = new User();
        $requestUser->_data = ['givenName' => ['en_US' => 'Request'], 'familyName' => ['en_US' => 'User']];
        $this->requestMock->method('getUser')->willReturn($requestUser);
        $this->requestMock->method('getSite')->willReturn(new Site());

        $this->dispatcherMock = $this->createMock(Dispatcher::class);
        $this->requestMock->method('getDispatcher')->willReturn($this->dispatcherMock);

        $router = $this->createMock(PKPRouter::class);
        $router->method('getHandler')->willReturn($this->createMock(PKPHandler::class));
        $this->requestMock->method('getRouter')->willReturn($router);

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

    public function resultFactoryMock($dataArray) {
        $resultFactory = $this->createMock(DAOResultFactory::class);
        $resultFactory->method('toArray')->willReturn($dataArray);
        return $resultFactory;
    }

    public function servicesRegister($servicesArray) {
        Services::register(new PPRServicesProviderMock($servicesArray));
    }

    public function getRandomId() {
        return rand(10000, 9999999);
    }

    public function getRequestMock() {
        return $this->requestMock;
    }

    public function getDispatcherMock() {
        return $this->dispatcherMock;
    }

    public function getTestUtil() {
        return $this->pprTestUtil;
    }

    // THIS IS NEEDED TO SUPPORT TEST UTILITY CLASSES
    public function createMock(string $originalClassName): \PHPUnit\Framework\MockObject\MockObject {
        return parent::createMock($originalClassName);
    }

}