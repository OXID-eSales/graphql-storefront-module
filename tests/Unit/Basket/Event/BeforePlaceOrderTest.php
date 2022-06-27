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
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforePlaceOrder as Event;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @covers OxidEsales\GraphQL\Storefront\Basket\Event\BeforePlaceOrder
 */
final class BeforePlaceOrderTest extends TestCase
{
    protected const BASKET_ID = 'basketId';

    public function testGetEventData(): void
    {
        $event =
            new Event(
                new ID(self::BASKET_ID)
            );

        $this->assertSame(self::BASKET_ID, (string) $event->getBasketId());
    }
}
