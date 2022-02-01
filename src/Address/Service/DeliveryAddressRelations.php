<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Service;

use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryAddress;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;

/**
 * @ExtendType(class=DeliveryAddress::class)
 */
final class DeliveryAddressRelations extends AddressRelations
{
}
