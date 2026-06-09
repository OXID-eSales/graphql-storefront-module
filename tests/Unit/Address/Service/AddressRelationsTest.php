<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Address\Service;

use OxidEsales\Eshop\Application\Model\Country as EshopCountryModel;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Address\DataType\AbstractAddress;
use OxidEsales\GraphQL\Storefront\Address\Service\DeliveryAddressRelations;
use OxidEsales\GraphQL\Storefront\Country\DataType\Country as CountryDataType;
use OxidEsales\GraphQL\Storefront\Country\Service\Country as CountryService;
use OxidEsales\GraphQL\Storefront\Country\Service\State as StateService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\GraphQLite\Types\ID;

#[CoversClass(\OxidEsales\GraphQL\Storefront\Address\Service\AddressRelations::class)]
final class AddressRelationsTest extends TestCase
{
    #[Test]
    public function country(): void
    {
        $countryId = uniqid();
        $country = new CountryDataType(
            $this->createConfiguredStub(EshopCountryModel::class, ['getRawFieldData' => '0'])
        );

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->method('getById')
            ->with($countryId, CountryDataType::class, false)
            ->willReturn($country);

        $addressStub = $this->createConfiguredStub(AbstractAddress::class, [
            'countryId' => new ID($countryId),
        ]);

        $sut = $this->getSut($repositoryMock);

        $result = $sut->country($addressStub);

        $this->assertSame($country, $result);
    }

    #[Test]
    public function countryReturnsNullWhenCountryWasDeleted(): void
    {
        $countryId = uniqid();

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->method('getById')
            ->with($countryId, CountryDataType::class, false)
            ->willThrowException(new NotFound());

        $addressStub = $this->createConfiguredStub(AbstractAddress::class, [
            'countryId' => new ID($countryId),
        ]);

        $sut = $this->getSut($repositoryMock);

        $result = $sut->country($addressStub);

        $this->assertNull($result);
    }

    private function getSut(RepositoryInterface $countryRepository): DeliveryAddressRelations
    {
        $countryService = new CountryService(
            $countryRepository,
            $this->createStub(Authorization::class)
        );

        return new DeliveryAddressRelations(
            $countryService,
            new StateService($this->createStub(RepositoryInterface::class))
        );
    }
}
