<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Service;

use OxidEsales\GraphQL\Storefront\Address\DataType\InvoiceAddress;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;

/**
 * @ExtendType(class=InvoiceAddress::class)
 */
final class InvoiceAddressRelations extends AddressRelations
{
}
