<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Service;

use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Address\DataType\InvoiceAddress as InvoiceAddressDataType;
use OxidEsales\GraphQL\Storefront\Address\Exception\AddressMissingFields;
use OxidEsales\GraphQL\Storefront\Address\Infrastructure\InvoiceAddressFactoryInterface;
use OxidEsales\GraphQL\Storefront\Address\Input\InvoiceAddressInputInterface;
use OxidEsales\GraphQL\Storefront\Customer\Service\CustomerInterface as CustomerService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface;
use OxidEsales\GraphQL\Storefront\Shared\Shop\User;

final class InvoiceAddress
{
    /** @var RepositoryInterface */
    private $repository;

    /** @var Authentication */
    private $authenticationService;

    /** @var InvoiceAddressFactoryInterface */
    private $invoiceAddressFactory;

    /** @var CustomerService */
    private $customerService;

    public function __construct(
        RepositoryInterface $repository,
        Authentication $authenticationService,
        InvoiceAddressFactoryInterface $invoiceAddressFactory,
        CustomerService $customerService
    ) {
        $this->repository = $repository;
        $this->authenticationService = $authenticationService;
        $this->invoiceAddressFactory = $invoiceAddressFactory;
        $this->customerService = $customerService;
    }

    public function customerInvoiceAddress(): InvoiceAddressDataType
    {
        return $this->repository->getById(
            (string)$this->authenticationService->getUser()->id(),
            InvoiceAddressDataType::class
        );
    }

    /**
     * @throws AddressMissingFields
     */
    public function updateInvoiceAddress(InvoiceAddressInputInterface $invoiceAddress): InvoiceAddressDataType
    {
        $customer = $this->customerService->customer(
            (string)$this->authenticationService->getUser()->id()
        );

        $address = $this->invoiceAddressFactory->createValidInvoiceAddressType(
            $customer,
            $invoiceAddress->getSalutation(),
            $invoiceAddress->getFirstName(),
            $invoiceAddress->getLastName(),
            $invoiceAddress->getCompany(),
            $invoiceAddress->getAdditionalInfo(),
            $invoiceAddress->getStreet(),
            $invoiceAddress->getStreetNumber(),
            $invoiceAddress->getZipCode(),
            $invoiceAddress->getCity(),
            $invoiceAddress->getCountryId(),
            $invoiceAddress->getStateId(),
            $invoiceAddress->getVatID(),
            $invoiceAddress->getPhone(),
            $invoiceAddress->getMobile(),
            $invoiceAddress->getFax()
        );

        $userModel = $address->getEshopModel();
        $this->repository->saveModel($userModel);

        /** @var User $userModel */
        $userModel->setAutomaticUserGroups();

        return $this->repository->getById($userModel->getId(), InvoiceAddressDataType::class);
    }
}
