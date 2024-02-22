<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Customer\Service;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\Eshop\Application\Model\User as EshopModelUser;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\Eshop\Core\Registry as EshopRegistry;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotDeletable;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Repository as CustomerRepository;
use OxidEsales\GraphQL\Storefront\Customer\Service\Customer as CustomerService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFound;
use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\ListConfiguration;

// TODO: Hack to make testDeletCustomerNotPossible get the right model from shop
// after testing library was dropped for integration tests
define('OXID_PHP_UNIT', 'OXID_PHP_UNIT');

final class CustomerServiceTest extends TestCase
{
    public function testFetchNotExistingCustomer(): void
    {
        $customerId = 'customerId';

        $customerService = ContainerFactory::getInstance()
            ->getContainer()
            ->get(CustomerService::class);

        $this->expectException(CustomerNotFound::class);
        $this->expectExceptionMessage((new CustomerNotFound($customerId))->getMessage());

        $customerService->fetchCustomer($customerId);
    }

    public function testDeleteCustomerNotPossible(): void
    {
        $container = ContainerFactory::getInstance()
            ->getContainer();

        $legacyServiceMock = $this
            ->getMockBuilder(Legacy::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getConfigParam'])
            ->getMock();
        $legacyServiceMock->expects($this->any())
            ->method('getConfigParam')
            ->willReturn(true);

        $user = oxNew(EshopModelUser::class);
        $user->setId('_userid');
        $user->assign([
            'oxusername' => '',
            'oxpassword' => '',
            'oxregister' => '',
        ]);
        $user->save();

        $userMock = $this
            ->getMockBuilder(EshopModelUser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userMock->expects($this->any())
            ->method('load')
            ->willReturn(true);

        $userMock->expects($this->any())
            ->method('getId')
            ->willReturn('_userid');

        $userMock->expects($this->once())
            ->method('setIsDerived');

        $userMock->expects($this->any())
            ->method('delete')
            ->willReturn(false);

        EshopRegistry::getUtilsObject()
            ->setClassInstance(EshopModelUser::class, $userMock);

        $userDataType = new UserDataType($userMock);

        $authenticationMock = $this
            ->getMockBuilder(Authentication::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUser'])
            ->getMock();
        $authenticationMock->expects($this->any())
            ->method('getUser')
            ->willReturn($userDataType);

        $customerService = new CustomerService(
            new Repository(
                $container->get(QueryBuilderFactoryInterface::class),
                new ListConfiguration()
            ),
            $container->get(CustomerRepository::class),
            $authenticationMock,
            $legacyServiceMock,
            new Authorization()
        );

        $this->expectException(CustomerNotDeletable::class);
        $this->expectExceptionMessage((CustomerNotDeletable::byModel())->getMessage());

        $customerService->deleteCustomer();

        $user->delete();
    }
}
