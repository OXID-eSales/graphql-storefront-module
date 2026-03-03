<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Basket\Event;

use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeRemoveItem as Event;
use PHPUnit\Framework\Attributes\CoversClass;
use TheCodingMachine\GraphQLite\Types\ID;

#[CoversClass(\OxidEsales\GraphQL\Storefront\Basket\Event\BeforeRemoveItem::class)]
final class BeforeRemoveItemTest extends AbstractItemEventTest
{
    public function testSetAmmount(): void
    {
        $event = $this->prepareEvent();
        $event->setAmount((float)self::SET_AMMOUNT);

        $this->assertSame((float)self::SET_AMMOUNT, $event->getAmount());
    }

    protected function prepareEvent(): Event
    {
        return new Event(
            new ID(self::BASKET_ID),
            new ID(self::BASKET_ITEM_ID),
            (float)self::AMMOUNT
        );
    }
}
