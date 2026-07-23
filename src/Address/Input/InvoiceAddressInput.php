<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Input;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Input;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
#[Input(name: 'InvoiceAddressInput', default: true)]
final class InvoiceAddressInput implements InvoiceAddressInputInterface
{
    #[Field]
    private ?string $salutation;

    #[Field]
    private ?string $firstName;

    #[Field]
    private ?string $lastName;

    #[Field]
    private ?string $company;

    #[Field]
    private ?string $additionalInfo;

    #[Field]
    private ?string $street;

    #[Field]
    private ?string $streetNumber;

    #[Field]
    private ?string $zipCode;

    #[Field]
    private ?string $city;

    #[Field]
    private ?ID $countryId;

    #[Field]
    private ?ID $stateId;

    #[Field]
    private ?string $vatID;

    #[Field]
    private ?string $phone;

    #[Field]
    private ?string $mobile;

    #[Field]
    private ?string $workPhone;

    #[Field]
    private ?string $fax;

    public function __construct(
        ?string $salutation = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $company = null,
        ?string $additionalInfo = null,
        ?string $street = null,
        ?string $streetNumber = null,
        ?string $zipCode = null,
        ?string $city = null,
        ?ID $countryId = null,
        ?ID $stateId = null,
        ?string $vatID = null,
        ?string $phone = null,
        ?string $mobile = null,
        ?string $workPhone = null,
        ?string $fax = null
    ) {
        $this->salutation = $salutation;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->company = $company;
        $this->additionalInfo = $additionalInfo;
        $this->street = $street;
        $this->streetNumber = $streetNumber;
        $this->zipCode = $zipCode;
        $this->city = $city;
        $this->countryId = $countryId;
        $this->stateId = $stateId;
        $this->vatID = $vatID;
        $this->phone = $phone;
        $this->mobile = $mobile;
        $this->workPhone = $workPhone;
        $this->fax = $fax;
    }

    public function getSalutation(): ?string
    {
        return $this->salutation;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function getAdditionalInfo(): ?string
    {
        return $this->additionalInfo;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountryId(): ?ID
    {
        return $this->countryId;
    }

    public function getStateId(): ?ID
    {
        return $this->stateId;
    }

    public function getVatID(): ?string
    {
        return $this->vatID;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function getWorkPhone(): ?string
    {
        return $this->workPhone;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }
}
