<?php

namespace Tests\FrameworkBundle\Unit\Model\Security;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use Shopsys\FrameworkBundle\Model\Administrator\Administrator;
use Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser;
use Shopsys\FrameworkBundle\Model\Order\OrderFlowFacade;
use Shopsys\FrameworkBundle\Model\Security\LoginListener;
use Shopsys\FrameworkBundle\Model\Security\TimelimitLoginInterface;
use Shopsys\FrameworkBundle\Model\Security\UniqueLoginInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListenerTest extends TestCase
{
    public function testOnSecurityInteractiveLoginUnique()
    {
        $emMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['__construct', 'persist', 'flush'])
            ->disableOriginalConstructor()
            ->getMock();
        $emMock->expects($this->once())->method('flush');

        $userMock = $this->createMock(UniqueLoginInterface::class);
        $userMock->expects($this->once())->method('setLoginToken');

        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenMock->expects($this->once())->method('getUser')->willReturn($userMock);

        $eventMock = $this->getMockBuilder(InteractiveLoginEvent::class)
            ->setMethods(['__construct', 'getAuthenticationToken'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())->method('getAuthenticationToken')->willReturn($tokenMock);

        $orderFlowFacadeMock = $this->getMockBuilder(OrderFlowFacade::class)
            ->setMethods(['__construct'])
            ->disableOriginalConstructor()
            ->getMock();

        $administratorActivityFacadeMock = $this->createMock(AdministratorActivityFacade::class);

        $loginListener = new LoginListener($emMock, $orderFlowFacadeMock, $administratorActivityFacadeMock);
        $loginListener->onSecurityInteractiveLogin($eventMock);
    }

    public function testOnSecurityInteractiveLoginTimelimit()
    {
        $emMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['__construct', 'persist', 'flush'])
            ->disableOriginalConstructor()
            ->getMock();
        $emMock->expects($this->any())->method('flush');

        $userMock = $this->createMock(TimelimitLoginInterface::class);
        $userMock->expects($this->once())->method('setLastActivity');

        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenMock->expects($this->once())->method('getUser')->willReturn($userMock);

        $eventMock = $this->getMockBuilder(InteractiveLoginEvent::class)
            ->setMethods(['__construct', 'getAuthenticationToken'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())->method('getAuthenticationToken')->willReturn($tokenMock);

        $orderFlowFacadeMock = $this->getMockBuilder(OrderFlowFacade::class)
            ->setMethods(['__construct'])
            ->disableOriginalConstructor()
            ->getMock();

        $administratorActivityFacadeMock = $this->createMock(AdministratorActivityFacade::class);

        $loginListener = new LoginListener($emMock, $orderFlowFacadeMock, $administratorActivityFacadeMock);
        $loginListener->onSecurityInteractiveLogin($eventMock);
    }

    public function testOnSecurityInteractiveLoginResetOrderForm()
    {
        $emMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['__construct', 'persist', 'flush'])
            ->disableOriginalConstructor()
            ->getMock();
        $emMock->expects($this->any())->method('flush');

        $userMock = $this->getMockBuilder(CustomerUser::class)
            ->setMethods(['__construct'])
            ->disableOriginalConstructor()
            ->getMock();

        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenMock->expects($this->once())->method('getUser')->willReturn($userMock);

        $eventMock = $this->getMockBuilder(InteractiveLoginEvent::class)
            ->setMethods(['__construct', 'getAuthenticationToken'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())->method('getAuthenticationToken')->willReturn($tokenMock);

        $orderFlowFacadeMock = $this->getMockBuilder(OrderFlowFacade::class)
            ->setMethods(['__construct', 'resetOrderForm'])
            ->disableOriginalConstructor()
            ->getMock();
        $orderFlowFacadeMock->expects($this->once())->method('resetOrderForm');

        $administratorActivityFacadeMock = $this->createMock(AdministratorActivityFacade::class);

        $loginListener = new LoginListener($emMock, $orderFlowFacadeMock, $administratorActivityFacadeMock);
        $loginListener->onSecurityInteractiveLogin($eventMock);
    }

    public function testOnSecurityInteractiveLoginAdministrator()
    {
        $emMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['__construct', 'persist', 'flush'])
            ->disableOriginalConstructor()
            ->getMock();
        $emMock->expects($this->once())->method('flush');

        $administratorMock = $this->createMock(Administrator::class);
        $administratorMock->expects($this->once())->method('setLoginToken');

        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenMock->expects($this->once())->method('getUser')->willReturn($administratorMock);

        $eventMock = $this->getMockBuilder(InteractiveLoginEvent::class)
            ->setMethods(['__construct', 'getAuthenticationToken', 'getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())->method('getAuthenticationToken')->willReturn($tokenMock);
        $eventMock->expects($this->once())->method('getRequest')->willReturn(new Request());

        $orderFlowFacadeMock = $this->getMockBuilder(OrderFlowFacade::class)
            ->setMethods(['__construct'])
            ->disableOriginalConstructor()
            ->getMock();

        $administratorActivityFacadeMock = $this->getMockBuilder(AdministratorActivityFacade::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $administratorActivityFacadeMock->expects($this->once())->method('create');

        $loginListener = new LoginListener($emMock, $orderFlowFacadeMock, $administratorActivityFacadeMock);
        $loginListener->onSecurityInteractiveLogin($eventMock);
    }
}
