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
        $this->repository            = $repository;
        $this->authenticationService = $authenticationService;
    }

    public function customerInvoiceAddress(): InvoiceAddressDataType
    {
        return $this->repository->getById(
            $this->authenticationService->getUserId(),
            InvoiceAddressDataType::class
        );
    }

    public function updateInvoiceAddress(InvoiceAddressDataType $invoiceAddress): InvoiceAddressDataType
    {
        if (!$id = (string) $this->authenticationService->getUserId()) {
            throw new InvalidLogin('Unauthorized');
        }

        $this->repository->saveModel($invoiceAddress->getEshopModel());
        $invoiceAddress->getEshopModel()->setAutomaticUserGroups();

        return $invoiceAddress;
    }
}
