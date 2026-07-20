<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Input;

use TheCodingMachine\GraphQLite\Types\ID;

interface DeliveryAddressInputInterface
{
    public function getSalutation(): ?string;

    public function getFirstName(): ?string;

    public function getLastName(): ?string;

    public function getCompany(): ?string;

    public function getAdditionalInfo(): ?string;

    public function getStreet(): ?string;

    public function getStreetNumber(): ?string;

    public function getZipCode(): ?string;

    public function getCity(): ?string;

    public function getCountryId(): ?ID;

    public function getStateId(): ?ID;

    public function getPhone(): ?string;

    public function getFax(): ?string;
}
