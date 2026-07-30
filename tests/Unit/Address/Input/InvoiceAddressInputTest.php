<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Address\Input;

use OxidEsales\GraphQL\Storefront\Address\Input\InvoiceAddressInput;
use OxidEsales\GraphQL\Storefront\Address\Input\InvoiceAddressInputInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\GraphQLite\Types\ID;

#[CoversClass(InvoiceAddressInput::class)]
final class InvoiceAddressInputTest extends TestCase
{
    #[Test]
    public function emptyInputHasAllNullValues(): void
    {
        $sut = $this->getSut();

        $this->assertInstanceOf(InvoiceAddressInputInterface::class, $sut);
        $this->assertNull($sut->getSalutation());
        $this->assertNull($sut->getFirstName());
        $this->assertNull($sut->getLastName());
        $this->assertNull($sut->getCompany());
        $this->assertNull($sut->getAdditionalInfo());
        $this->assertNull($sut->getStreet());
        $this->assertNull($sut->getStreetNumber());
        $this->assertNull($sut->getZipCode());
        $this->assertNull($sut->getCity());
        $this->assertNull($sut->getCountryId());
        $this->assertNull($sut->getStateId());
        $this->assertNull($sut->getVatID());
        $this->assertNull($sut->getPhone());
        $this->assertNull($sut->getMobile());
        $this->assertNull($sut->getFax());
    }

    #[Test]
    public function gettersReturnConstructorValues(): void
    {
        $countryId = new ID('a7c40f631fc920687.20179984');
        $stateId = new ID('BW');

        $sut = $this->getSut(
            salutation: 'MR',
            firstName: 'Marc',
            lastName: 'Muster',
            company: 'Musterfirma',
            additionalInfo: 'additional',
            street: 'Bertoldstrasse',
            streetNumber: '48',
            zipCode: '79098',
            city: 'Freiburg',
            countryId: $countryId,
            stateId: $stateId,
            vatID: 'DE123456789',
            phone: '1234567',
            mobile: '015112345678',
            fax: '7654321',
        );

        $this->assertSame('MR', $sut->getSalutation());
        $this->assertSame('Marc', $sut->getFirstName());
        $this->assertSame('Muster', $sut->getLastName());
        $this->assertSame('Musterfirma', $sut->getCompany());
        $this->assertSame('additional', $sut->getAdditionalInfo());
        $this->assertSame('Bertoldstrasse', $sut->getStreet());
        $this->assertSame('48', $sut->getStreetNumber());
        $this->assertSame('79098', $sut->getZipCode());
        $this->assertSame('Freiburg', $sut->getCity());
        $this->assertSame($countryId, $sut->getCountryId());
        $this->assertInstanceOf(ID::class, $sut->getCountryId());
        $this->assertSame($stateId, $sut->getStateId());
        $this->assertInstanceOf(ID::class, $sut->getStateId());
        $this->assertSame('DE123456789', $sut->getVatID());
        $this->assertSame('1234567', $sut->getPhone());
        $this->assertSame('015112345678', $sut->getMobile());
        $this->assertSame('7654321', $sut->getFax());
    }

    #[Test]
    public function stateIdMayBeNullWhileCountryIdSet(): void
    {
        $countryId = new ID('a7c40f631fc920687.20179984');

        $sut = $this->getSut(countryId: $countryId);

        $this->assertSame($countryId, $sut->getCountryId());
        $this->assertNull($sut->getStateId());
    }

    private function getSut(
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
        ?string $fax = null,
    ): InvoiceAddressInput {
        return new InvoiceAddressInput(
            salutation: $salutation,
            firstName: $firstName,
            lastName: $lastName,
            company: $company,
            additionalInfo: $additionalInfo,
            street: $street,
            streetNumber: $streetNumber,
            zipCode: $zipCode,
            city: $city,
            countryId: $countryId,
            stateId: $stateId,
            vatID: $vatID,
            phone: $phone,
            mobile: $mobile,
            fax: $fax,
        );
    }
}
