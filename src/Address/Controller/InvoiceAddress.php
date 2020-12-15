<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Controller;

use OxidEsales\GraphQL\Storefront\Address\DataType\InvoiceAddress as InvoiceAddressDataType;
use OxidEsales\GraphQL\Storefront\Address\Service\InvoiceAddress as InvoiceAddressService;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class InvoiceAddress
{
    /** @var InvoiceAddressService */
    private $invoiceAddressService;

    public function __construct(
        InvoiceAddressService $invoiceAddressService
    ) {
        $this->invoiceAddressService = $invoiceAddressService;
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function customerInvoiceAddressSet(
        InvoiceAddressDataType $invoiceAddress
    ): InvoiceAddressDataType {
        return $this->invoiceAddressService->updateInvoiceAddress($invoiceAddress);
    }

    /**
     * @Query()
     * @Logged()
     */
    public function customerInvoiceAddress(): InvoiceAddressDataType
    {
        return $this->invoiceAddressService->customerInvoiceAddress();
    }
}
