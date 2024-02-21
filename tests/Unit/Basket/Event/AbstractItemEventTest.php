<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Basket\Event;

use OxidEsales\GraphQL\Storefront\Basket\Event\AfterAddItem;
use OxidEsales\GraphQL\Storefront\Basket\Event\AfterRemoveItem;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @covers OxidEsales\GraphQL\Storefront\Basket\Event\AbstractItemEvent
 * @covers OxidEsales\GraphQL\Storefront\Basket\Event\AfterAddItem
 * @covers OxidEsales\GraphQL\Storefront\Basket\Event\AfterRemoveItem
 */
class AbstractItemEventTest extends TestCase
{
    protected const BASKET_ID = 'basketId';

    protected const BASKET_ITEM_ID = 'basketItemId';

    protected const PRODUCT_ID = 'productId';

    protected const AMMOUNT = 15;

    protected const SET_AMMOUNT = 50;

    public function testAfterAddItem(): void
    {
        $event = $this->prepareAddEvent();

        $this->assertThat(
            $event->getBasketId(),
            $this->isInstanceOf(ID::class)
        );
        $this->assertSame(self::BASKET_ID, (string)$event->getBasketId());

        $this->assertThat(
            $event->getProductId(),
            $this->isInstanceOf(ID::class)
        );
        $this->assertSame(self::PRODUCT_ID, (string)$event->getProductId());

        $this->assertThat(
            $event->getAmount(),
            $this->isType(IsType::TYPE_FLOAT)
        );
        $this->assertSame((float)self::AMMOUNT, $event->getAmount());
    }

    public function testAfterRemoveItem(): void
    {
        $event = $this->prepareRemoveEvent();

        $this->assertThat(
            $event->getBasketId(),
            $this->isInstanceOf(ID::class)
        );
        $this->assertSame(self::BASKET_ID, (string)$event->getBasketId());

        $this->assertThat(
            $event->getBasketItemId(),
            $this->isInstanceOf(ID::class)
        );
        $this->assertSame(self::BASKET_ITEM_ID, (string)$event->getBasketItemId());

        $this->assertThat(
            $event->getAmount(),
            $this->isType(IsType::TYPE_FLOAT)
        );
        $this->assertSame((float)self::AMMOUNT, $event->getAmount());
    }

    protected function prepareAddEvent(): AfterAddItem
    {
        return new AfterAddItem(
            new ID(self::BASKET_ID),
            new ID(self::PRODUCT_ID),
            (float)self::AMMOUNT
        );
    }

    protected function prepareRemoveEvent(): AfterRemoveItem
    {
        return new AfterRemoveItem(
            new ID(self::BASKET_ID),
            new ID(self::BASKET_ITEM_ID),
            (float)self::AMMOUNT
        );
    }
}
