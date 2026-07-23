<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Address\Service;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\GraphQL\Base\DataType\User;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Address\DataType\InvoiceAddress as InvoiceAddressDataType;
use OxidEsales\GraphQL\Storefront\Address\Exception\AddressMissingFields;
use OxidEsales\GraphQL\Storefront\Address\Infrastructure\InvoiceAddressFactoryInterface;
use OxidEsales\GraphQL\Storefront\Address\Input\InvoiceAddressInputInterface;
use OxidEsales\GraphQL\Storefront\Address\Service\InvoiceAddress as InvoiceAddressService;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Customer\Service\CustomerInterface as CustomerService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvoiceAddressService::class)]
final class InvoiceAddressServiceTest extends TestCase
{
    #[Test]
    public function updateInvoiceAddressRaisesAddressMissingFieldsFromFactoryBeforePersisting(): void
    {
        $userId = uniqid();

        $customer = new CustomerDataType($this->createStub(EshopUserModel::class));

        $customerServiceMock = $this->createMock(CustomerService::class);
        $customerServiceMock->expects($this->once())
            ->method('customer')
            ->with($userId)
            ->willReturn($customer);

        $factoryMock = $this->createMock(InvoiceAddressFactoryInterface::class);
        $factoryMock->expects($this->once())
            ->method('createValidInvoiceAddressType')
            ->willThrowException(new AddressMissingFields('invoice', ['street', 'zip']));

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->expects($this->never())->method('saveModel');
        $repositoryMock->expects($this->never())->method('getById');

        $sut = $this->getSut(
            repository: $repositoryMock,
            authentication: $this->getAuthenticationMock($userId),
            factory: $factoryMock,
            customerService: $customerServiceMock,
        );

        $this->expectException(AddressMissingFields::class);

        $sut->updateInvoiceAddress($this->getInputStub());
    }

    #[Test]
    public function updateInvoiceAddressPersistsAddressFromFactoryAndReturnsReloadedDataType(): void
    {
        $userId = uniqid();
        $addressId = uniqid();

        $customer = new CustomerDataType($this->createStub(EshopUserModel::class));

        $userModelSpy = $this->createMock(InvoiceAddressUserModelSpy::class);
        $userModelSpy->method('getId')->willReturn($addressId);
        $userModelSpy->expects($this->once())->method('setAutomaticUserGroups');

        $createdAddress = new InvoiceAddressDataType($userModelSpy);
        $reloadedAddress = new InvoiceAddressDataType($this->createStub(EshopUserModel::class));

        $customerServiceMock = $this->createMock(CustomerService::class);
        $customerServiceMock->expects($this->once())
            ->method('customer')
            ->with($userId)
            ->willReturn($customer);

        $factoryMock = $this->createMock(InvoiceAddressFactoryInterface::class);
        $factoryMock->expects($this->once())
            ->method('createValidInvoiceAddressType')
            ->with(
                $customer,
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
                'DE123456789',
                '123',
                '456',
                '654',
                '789'
            )
            ->willReturn($createdAddress);

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->expects($this->once())
            ->method('saveModel')
            ->with($userModelSpy);
        $repositoryMock->expects($this->once())
            ->method('getById')
            ->with($addressId, InvoiceAddressDataType::class)
            ->willReturn($reloadedAddress);

        $sut = $this->getSut(
            repository: $repositoryMock,
            authentication: $this->getAuthenticationMock($userId),
            factory: $factoryMock,
            customerService: $customerServiceMock,
        );

        $result = $sut->updateInvoiceAddress($this->getInputStub());

        $this->assertSame($reloadedAddress, $result);
    }

    private function getInputStub(): InvoiceAddressInputInterface
    {
        $input = $this->createStub(InvoiceAddressInputInterface::class);
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
        $input->method('getVatID')->willReturn('DE123456789');
        $input->method('getPhone')->willReturn('123');
        $input->method('getMobile')->willReturn('456');
        $input->method('getWorkPhone')->willReturn('654');
        $input->method('getFax')->willReturn('789');

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
        ?InvoiceAddressFactoryInterface $factory = null,
        ?CustomerService $customerService = null,
    ): InvoiceAddressService {
        return new InvoiceAddressService(
            $repository ?? $this->createStub(RepositoryInterface::class),
            $authentication ?? $this->createStub(Authentication::class),
            $factory ?? $this->createStub(InvoiceAddressFactoryInterface::class),
            $customerService ?? $this->createStub(CustomerService::class),
        );
    }
}
