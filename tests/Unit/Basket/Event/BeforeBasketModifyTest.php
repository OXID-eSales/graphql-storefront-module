<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Basket\Event;

use PHPUnit\Framework\TestCase;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketModify as Event;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @covers OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketModify
 */
final class BeforeBasketModifyTest extends TestCase
{
    protected const BASKET_ID = 'basketId';

    public function testGetEventDataDefault(): void
    {
        $event =
            new Event(
                new ID(self::BASKET_ID)
            );

        $this->assertSame(self::BASKET_ID, (string) $event->getBasketId());
        $this->assertSame(Event::TYPE_NOT_SPECIFIED, $event->getEventType());
    }

    public function testGetEventData(): void
    {
        $event =
            new Event(
                new ID(self::BASKET_ID),
                Event::TYPE_SET_PAYMENT_METHOD
            );

        $this->assertSame(self::BASKET_ID, (string) $event->getBasketId());
        $this->assertSame(Event::TYPE_SET_PAYMENT_METHOD, $event->getEventType());
    }
}
