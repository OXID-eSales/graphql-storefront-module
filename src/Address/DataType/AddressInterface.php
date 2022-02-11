<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\DataType;

use TheCodingMachine\GraphQLite\Types\ID;

interface AddressInterface
{
    public function salutation(): string;

    public function firstName(): string;

    public function lastName(): string;

    public function company(): string;

    public function additionalInfo(): string;

    public function street(): string;

    public function streetNumber(): string;

    public function zipCode(): string;

    public function city(): string;

    public function phone(): string;

    public function fax(): string;

    public function countryId(): ID;

    public function stateId(): ID;
}
