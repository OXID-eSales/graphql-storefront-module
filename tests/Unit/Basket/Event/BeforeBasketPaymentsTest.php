<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Basket\Event;

use PHPUnit\Framework\TestCase;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketPayments as Event;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @covers OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketPayments
 */
final class BeforeBasketPaymentsTest extends TestCase
{
    protected const BASKET_ID = 'basketId';

    public function testSetDeliveryMethods(): void
    {
        $payments = ['bla' => 'foo'];

        $event = $this->prepareEvent();
        $event->setPayments($payments);

        $this->assertSame($payments, $event->getPayments());
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
