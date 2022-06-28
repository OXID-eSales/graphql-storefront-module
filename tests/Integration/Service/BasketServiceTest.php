<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Service;

use TheCodingMachine\GraphQLite\Types\ID;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Repository as BasketRepository;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Basket as BasketInfrastructure;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Basket as SharedInfrastructure;
use OxidEsales\GraphQL\Storefront\Basket\Service\BasketVoucher;
use OxidEsales\GraphQL\Storefront\Voucher\Infrastructure\Repository as VoucherRepository;
use OxidEsales\GraphQL\Storefront\Voucher\Infrastructure\Voucher as VoucherInfrastructure;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Customer as CustomerInfrastructure;
use OxidEsales\GraphQL\Storefront\Address\Service\DeliveryAddress as DeliveryAddressService;
use OxidEsales\GraphQL\Storefront\Country\Service\Country as CountryService;
use OxidEsales\GraphQL\Storefront\Customer\Service\Customer as CustomerService;
use OxidEsales\GraphQL\Storefront\Basket\Service\Basket as StorefrontBasketService;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketDeliveryMethods;
use OxidEsales\EshopCommunity\Internal\Framework\Event\ShopAwareEventDispatcher;
use PHPUnit\Framework\TestCase;

final class BasketServiceTest extends TestCase
{
    public function testGetBasketDeliveryMethodsEvent(): void
    {
        $deliveryMethods = ['foo' => 'bla'];
        $event = new BeforeBasketDeliveryMethods(new ID('dummy_id'));
        $event->setDeliveryMethods($deliveryMethods);

        $eventDispatcher = $this->getMockBuilder(ShopAwareEventDispatcher::class)
            ->onlyMethods(['dispatch'])
            ->getMock();
        $eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->willReturn($event);

        $container = ContainerFactory::getInstance()->getContainer();

        $service = new StorefrontBasketService(
            new Repository($container->get(QueryBuilderFactoryInterface::class)),
            $container->get(BasketRepository::class),
            $container->get(Authentication::class),
            new Authorization(),
            $container->get(Legacy::class),
            $container->get(BasketInfrastructure::class),
            $container->get(ProductService::class),
            $container->get(SharedInfrastructure::class),
            $container->get(BasketVoucher::class),
            $container->get(VoucherInfrastructure::class),
            $container->get(CustomerInfrastructure::class),
            $container->get(BasketInfrastructure::class),
            $container->get(DeliveryAddressService::class),
            $container->get(VoucherRepository::class),
            $container->get(CountryService::class),
            $container->get(CustomerService::class),
            $eventDispatcher
        );

        $this->assertSame($deliveryMethods, $service->getBasketDeliveryMethods(new ID('dummy_id')));
    }
}
