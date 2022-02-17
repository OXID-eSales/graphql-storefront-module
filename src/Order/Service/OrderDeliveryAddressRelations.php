<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\Service;

use OxidEsales\GraphQL\Storefront\Address\Service\AddressRelations;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderDeliveryAddress;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;

/**
 * @ExtendType(class=OrderDeliveryAddress::class)
 */
final class OrderDeliveryAddressRelations extends AddressRelations
{
}
