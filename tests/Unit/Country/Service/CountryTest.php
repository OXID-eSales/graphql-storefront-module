<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Country\Service;

use OxidEsales\Eshop\Application\Model\Country as EshopCountryModel;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Country\DataType\Country as CountryDataType;
use OxidEsales\GraphQL\Storefront\Country\Exception\CountryIsInactive;
use OxidEsales\GraphQL\Storefront\Country\Exception\CountryNotFound;
use OxidEsales\GraphQL\Storefront\Country\Service\Country as CountryService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\GraphQLite\Types\ID;

#[CoversClass(CountryService::class)]
final class CountryTest extends TestCase
{
    #[Test]
    public function countryReturnsInactiveCountryWhenStatusCheckDisabled(): void
    {
        $countryId = uniqid();
        $country = new CountryDataType(
            $this->createConfiguredStub(EshopCountryModel::class, ['getRawFieldData' => '0'])
        );

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->method('getById')
            ->with($countryId, CountryDataType::class, false)
            ->willReturn($country);

        $sut = $this->getSut($repositoryMock);

        $result = $sut->country(new ID($countryId), checkStatusPermission: false);

        $this->assertSame($country, $result);
    }

    #[Test]
    public function countryThrowsWhenCountryWasDeleted(): void
    {
        $countryId = uniqid();

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->method('getById')
            ->with($countryId, CountryDataType::class, false)
            ->willThrowException(new NotFound());

        $sut = $this->getSut($repositoryMock);

        $this->expectException(CountryNotFound::class);
        $sut->country(new ID($countryId), checkStatusPermission: false);
    }

    #[Test]
    public function countryThrowsWhenInactiveCountryViewingNotAllowed(): void
    {
        $countryId = uniqid();

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->method('getById')
            ->with($countryId, CountryDataType::class, false)
            ->willReturn(new CountryDataType(
                $this->createConfiguredStub(EshopCountryModel::class, ['getRawFieldData' => '0'])
            ));

        $authorizationStub = $this->createStub(Authorization::class);
        $authorizationStub->method('isAllowed')->willReturn(false);

        $sut = $this->getSut($repositoryMock, $authorizationStub);

        $this->expectException(CountryIsInactive::class);
        $this->expectExceptionMessage((new CountryIsInactive($countryId))->getMessage());
        $sut->country(new ID($countryId));
    }

    private function getSut(
        ?RepositoryInterface $repository = null,
        ?Authorization $authorization = null
    ): CountryService {
        return new CountryService(
            $repository ?? $this->createStub(RepositoryInterface::class),
            $authorization ?? $this->createStub(Authorization::class)
        );
    }
}
