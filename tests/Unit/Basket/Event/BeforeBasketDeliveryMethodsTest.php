<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Basket\Event;

use PHPUnit\Framework\TestCase;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketDeliveryMethods as Event;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @covers OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketDeliveryMethods
 */
final class BeforeBasketDeliveryMethodsTest extends TestCase
{
    protected const BASKET_ID = 'basketId';

    public function testSetDeliveryMethods(): void
    {
        $deliveryMethods = ['bla' => 'foo'];

        $event = $this->prepareEvent();
        $event->setDeliveryMethods($deliveryMethods);

        $this->assertSame($deliveryMethods, $event->getDeliveryMethods());
    }

    protected function prepareEvent(): Event
    {
        $event =
            new Event(
                new ID(self::BASKET_ID)
            );

        $this->assertSame(self::BASKET_ID, (string) $event->getBasketId());

        return $event;
    }
}
