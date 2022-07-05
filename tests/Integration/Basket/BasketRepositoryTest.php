<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Basket;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\Eshop\Application\Model\User as EshopModelUser;
use OxidEsales\Eshop\Application\Model\UserBasket as EshopModelUserBasket;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketForUserNotFound;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Repository;
use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;

final class BasketRepositoryTest extends TestCase
{
    public function testCustomerBasketByTitleFails(): void
    {
        $basketTitle = 'some title';
        $userId = 'customerId';

        $user = $this->getMockBuilder(EshopModelUser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $user->expects($this->any())
            ->method('getBasket')
            ->willReturn(oxNew(EshopModelUserBasket::class));

        $user->expects($this->any())
            ->method('getId')
            ->willReturn($userId);

        $customerDataType = new CustomerDataType($user);

        $basketRepository = ContainerFactory::getInstance()
            ->getContainer()
            ->get(Repository::class);

        $this->expectException(BasketForUserNotFound::class);
        $this->expectExceptionMessage((new BasketForUserNotFound($userId, $basketTitle))->getMessage());

        $basketRepository->customerBasketByTitle($customerDataType, $basketTitle);
    }
}
