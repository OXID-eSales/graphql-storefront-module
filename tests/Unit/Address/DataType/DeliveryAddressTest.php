<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Address\DataType;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\GraphQL\Storefront\Address\Exception\AddressMissingFields;
use OxidEsales\GraphQL\Storefront\Address\Infrastructure\DeliveryAddressFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OxidEsales\GraphQL\Storefront\Address\DataType\InvoiceAddress
 */
final class DeliveryAddressTest extends TestCase
{
    public function testCreateDeliveryAddressWithMissingFields(): void
    {
        $deliveryAddressFactory = new DeliveryAddressFactory();

        $user = oxNew(User::class);
        $user->save();

        $this->expectException(AddressMissingFields::class);
        $deliveryAddressFactory->createValidAddressType(
            $user->getId(),
            'Mr',
            'test',
            'name',
        );

        $exceptionMessage = $this->getExpectedExceptionMessage();
        $this->assertEquals(
            'Delivery address is missing required fields: street, streetnr, zip, city, countryid',
            $exceptionMessage
        );
    }
}
