<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Address\DataType\InvoiceAddress as InvoiceAddressDataType;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Shop\User;

final class InvoiceAddress
{
    /** @var Repository */
    private $repository;

    /** @var Authentication */
    private $authenticationService;

    public function __construct(
        Repository $repository,
        Authentication $authenticationService
    ) {
        $this->repository = $repository;
        $this->authenticationService = $authenticationService;
    }

    public function customerInvoiceAddress(): InvoiceAddressDataType
    {
        return $this->repository->getById(
            (string)$this->authenticationService->getUser()->id(),
            InvoiceAddressDataType::class
        );
    }

    public function updateInvoiceAddress(InvoiceAddressDataType $invoiceAddress): InvoiceAddressDataType
    {
        if (!(string)$this->authenticationService->getUser()->id()) {
            throw new InvalidLogin('Unauthorized');
        }

        $userModel = $invoiceAddress->getEshopModel();
        $this->repository->saveModel($userModel);

        /** @var User $userModel */
        $userModel->setAutomaticUserGroups();

        return $this->repository->getById($userModel->getId(), InvoiceAddressDataType::class);
    }
}
