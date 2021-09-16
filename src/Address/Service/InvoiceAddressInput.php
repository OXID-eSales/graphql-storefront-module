<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Service;

use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Address\DataType\InvoiceAddress;
use OxidEsales\GraphQL\Storefront\Address\Infrastructure\InvoiceAddressFactory;
use OxidEsales\GraphQL\Storefront\Customer\Service\Customer as CustomerService;
use TheCodingMachine\GraphQLite\Annotations\Factory;
use TheCodingMachine\GraphQLite\Types\ID;

final class InvoiceAddressInput
{
    /** @var Authentication */
    private $authenticationService;

    /** @var CustomerService */
    private $customerService;

    /** @var InvoiceAddressFactory */
    private $invoiceAddressFactory;

    public function __construct(
        InvoiceAddressFactory $invoiceAddressFactory,
        Authentication $authenticationService,
        CustomerService $customerService
    ) {
        $this->invoiceAddressFactory = $invoiceAddressFactory;
        $this->authenticationService = $authenticationService;
        $this->customerService       = $customerService;
    }

    /**
     * @Factory
     */
    public function fromUserInput(
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
        ?string $fax = null
    ): InvoiceAddress {
        $customer = $this->customerService
            ->customer($this->authenticationService->getUserId());

        return $this->invoiceAddressFactory->createValidInvoiceAddressType(
            $customer,
            $salutation,
            $firstName,
            $lastName,
            $company,
            $additionalInfo,
            $street,
            $streetNumber,
            $zipCode,
            $city,
            $countryId,
            $stateId,
            $vatID,
            $phone,
            $mobile,
            $fax
        );
    }
}
