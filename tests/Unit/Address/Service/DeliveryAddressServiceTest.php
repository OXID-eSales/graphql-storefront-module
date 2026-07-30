<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Address\Service;

use OxidEsales\Eshop\Application\Model\Address as EshopAddressModel;
use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\GraphQL\Base\DataType\User;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryAddress as DeliveryAddressDataType;
use OxidEsales\GraphQL\Storefront\Address\Exception\AddressMissingFields;
use OxidEsales\GraphQL\Storefront\Address\Infrastructure\DeliveryAddressFactoryInterface;
use OxidEsales\GraphQL\Storefront\Address\Input\DeliveryAddressInputInterface;
use OxidEsales\GraphQL\Storefront\Address\Service\DeliveryAddress as DeliveryAddressService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\GraphQLite\Types\ID;

#[CoversClass(DeliveryAddressService::class)]
final class DeliveryAddressServiceTest extends TestCase
{
    #[Test]
    public function storeRaisesAddressMissingFieldsFromFactoryBeforePersisting(): void
    {
        $userId = uniqid();

        $factoryMock = $this->createMock(DeliveryAddressFactoryInterface::class);
        $factoryMock->expects($this->once())
            ->method('createValidAddressType')
            ->willThrowException(new AddressMissingFields('delivery', ['street', 'zip']));

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->expects($this->never())->method('saveModel');
        $repositoryMock->expects($this->never())->method('getById');

        $authenticationMock = $this->getAuthenticationMock($userId);

        $sut = $this->getSut(
            repository: $repositoryMock,
            authentication: $authenticationMock,
            factory: $factoryMock,
        );

        $this->expectException(AddressMissingFields::class);

        $sut->store($this->getInputStub());
    }

    #[Test]
    public function storePersistsAddressFromFactoryAndReturnsReloadedDataType(): void
    {
        $userId = uniqid();
        $addressId = uniqid();

        $createdAddress = new DeliveryAddressDataType(
            $this->createConfiguredStub(EshopAddressModel::class, ['getId' => $addressId])
        );
        $reloadedAddress = new DeliveryAddressDataType(
            $this->createStub(EshopAddressModel::class)
        );

        $factoryMock = $this->createMock(DeliveryAddressFactoryInterface::class);
        $factoryMock->expects($this->once())
            ->method('createValidAddressType')
            ->with(
                $userId,
                'Mr',
                'Marc',
                'Muster',
                'OXID',
                'info',
                'Bertoldstrasse',
                '48',
                '79098',
                'Freiburg',
                null,
                null,
                '123',
                '456'
            )
            ->willReturn($createdAddress);

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->expects($this->once())->method('saveModel');
        $repositoryMock->expects($this->once())
            ->method('getById')
            ->with($addressId, DeliveryAddressDataType::class)
            ->willReturn($reloadedAddress);

        $sut = $this->getSut(
            repository: $repositoryMock,
            authentication: $this->getAuthenticationMock($userId),
            factory: $factoryMock,
        );

        $result = $sut->store($this->getInputStub());

        $this->assertSame($reloadedAddress, $result);
    }

    private function getInputStub(): DeliveryAddressInputInterface
    {
        $input = $this->createStub(DeliveryAddressInputInterface::class);
        $input->method('getSalutation')->willReturn('Mr');
        $input->method('getFirstName')->willReturn('Marc');
        $input->method('getLastName')->willReturn('Muster');
        $input->method('getCompany')->willReturn('OXID');
        $input->method('getAdditionalInfo')->willReturn('info');
        $input->method('getStreet')->willReturn('Bertoldstrasse');
        $input->method('getStreetNumber')->willReturn('48');
        $input->method('getZipCode')->willReturn('79098');
        $input->method('getCity')->willReturn('Freiburg');
        $input->method('getCountryId')->willReturn(null);
        $input->method('getStateId')->willReturn(null);
        $input->method('getPhone')->willReturn('123');
        $input->method('getFax')->willReturn('456');

        return $input;
    }

    private function getAuthenticationMock(string $userId): Authentication
    {
        $authenticationMock = $this->createMock(Authentication::class);
        $authenticationMock->expects($this->once())
            ->method('getUser')
            ->willReturn(
                new User(
                    $this->createConfiguredStub(EshopUserModel::class, ['getId' => $userId])
                )
            );

        return $authenticationMock;
    }

    private function getSut(
        ?RepositoryInterface $repository = null,
        ?Authentication $authentication = null,
        ?DeliveryAddressFactoryInterface $factory = null,
    ): DeliveryAddressService {
        return new DeliveryAddressService(
            $repository ?? $this->createStub(RepositoryInterface::class),
            $authentication ?? $this->createStub(Authentication::class),
            $this->createStub(Authorization::class),
            $factory ?? $this->createStub(DeliveryAddressFactoryInterface::class),
        );
    }
}
