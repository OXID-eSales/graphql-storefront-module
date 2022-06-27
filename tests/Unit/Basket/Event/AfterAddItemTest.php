<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Basket\Event;

use PHPUnit\Framework\TestCase;
use OxidEsales\GraphQL\Storefront\Basket\Event\AfterAddItem as Event;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @covers OxidEsales\GraphQL\Storefront\Basket\Event\AfterAddItem
 */
final class AfterAddItemTest extends TestCase
{
    protected const BASKET_ID = 'basketId';
    protected const PRODUCT_ID = 'productId';

    public function testGetEventData(): void
    {
        $event =
            new Event(
                new ID(self::BASKET_ID),
                new ID(self::PRODUCT_ID),
                3
            );

        $this->assertSame(self::BASKET_ID, (string) $event->getBasketId());
        $this->assertSame(self::PRODUCT_ID, (string) $event->getProductId());
        $this->assertEquals(3, $event->getAmount());
    }
}
