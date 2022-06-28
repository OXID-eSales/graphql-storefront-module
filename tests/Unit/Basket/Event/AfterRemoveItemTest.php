<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Basket\Event;

use PHPUnit\Framework\TestCase;
use OxidEsales\GraphQL\Storefront\Basket\Event\AfterRemoveItem as Event;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @covers OxidEsales\GraphQL\Storefront\Basket\Event\AfterRemoveItem
 */
final class AfterRemoveItemTest extends TestCase
{
    protected const BASKET_ID = 'basketId';
    protected const BASKETITEM_ID = 'basketItemId';

    public function testGetEventData(): void
    {
        $event =
            new Event(
                new ID(self::BASKET_ID),
                new ID(self::BASKETITEM_ID),
                3
            );

        $this->assertSame(self::BASKET_ID, (string) $event->getBasketId());
        $this->assertSame(self::BASKETITEM_ID, (string) $event->getBasketItemId());
        $this->assertEquals(3, $event->getAmount());
    }
}
