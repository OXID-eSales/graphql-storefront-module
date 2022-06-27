<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Basket\Event;

use PHPUnit\Framework\TestCase;
use OxidEsales\Eshop\Application\Model\UserBasket as EshopModelUserBasket;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\Event\BasketAuthorization as Event;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @covers OxidEsales\GraphQL\Storefront\Basket\Event\BasketAuthorization
 */
final class BasketAuthorizationTest extends TestCase
{
    protected const BASKET_ID = 'basketId';
    protected const CUSTOMER_ID = 'customerId';

    public function testGetEventData(): void
    {
        $basket = new BasketDataType(oxNew(EshopModelUserBasket::class));

        $event =
            new Event(
                $basket,
                new ID(self::CUSTOMER_ID)
            );

        $this->assertFalse($event->getAuthorized());

        $event->setAuthorized(true);

        $this->assertSame($basket, $event->getBasket());
        $this->assertSame(self::CUSTOMER_ID, (string) $event->getCustomerId());
        $this->assertTrue($event->getAuthorized());
    }
}
