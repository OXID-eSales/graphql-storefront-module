<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Basket\Event;

use OxidEsales\GraphQL\Storefront\Basket\Event\AbstractItemEvent as Event;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @covers OxidEsales\GraphQL\Storefront\Basket\Event\AbstractItemEventTest
 * @covers OxidEsales\GraphQL\Storefront\Basket\Event\AfterAddItem
 * @covers OxidEsales\GraphQL\Storefront\Basket\Event\AfterRemoveItem
 */
abstract class AbstractItemEventTest extends TestCase
{
    protected const BASKET_ID = 'basketId';

    protected const BASKET_ITEM_ID = 'basketItemId';

    protected const AMMOUNT = 15;

    protected const SET_AMMOUNT = 50;

    public function testBeforeRemoveItem(): void
    {
        $event = $this->prepareEvent();

        $this->assertThat(
            $event->getBasketId(),
            $this->isInstanceOf(ID::class)
        );
        $this->assertSame(self::BASKET_ID, (string) $event->getBasketId());

        $this->assertThat(
            $event->getBasketItemId(),
            $this->isInstanceOf(ID::class)
        );
        $this->assertSame(self::BASKET_ITEM_ID, (string) $event->getBasketItemId());

        $this->assertThat(
            $event->getAmount(),
            $this->isType(IsType::TYPE_FLOAT)
        );
        $this->assertSame((float) self::AMMOUNT, $event->getAmount());
    }

    protected function prepareEvent(): Event
    {
        return new Event(
            new ID(self::BASKET_ID),
            new ID(self::BASKET_ITEM_ID),
            (float) self::AMMOUNT
        );
    }
}
