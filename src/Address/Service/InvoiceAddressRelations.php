<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Service;

use OxidEsales\GraphQL\Storefront\Address\DataType\InvoiceAddress;
use OxidEsales\GraphQL\Storefront\Country\DataType\Country;
use OxidEsales\GraphQL\Storefront\Country\DataType\State;
use OxidEsales\GraphQL\Storefront\Country\Exception\StateNotFound;
use OxidEsales\GraphQL\Storefront\Country\Service\Country as CountryService;
use OxidEsales\GraphQL\Storefront\Country\Service\State as StateService;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=InvoiceAddress::class)
 */
final class InvoiceAddressRelations
{
    /** @var CountryService */
    private $countryService;

    /** @var StateService */
    private $stateService;

    public function __construct(
        CountryService $countryService,
        StateService $stateService
    ) {
        $this->countryService = $countryService;
        $this->stateService   = $stateService;
    }

    /**
     * @Field()
     */
    public function country(InvoiceAddress $invoiceAddress): Country
    {
        return $this->countryService->country(
            $invoiceAddress->countryId()
        );
    }

    /**
     * @Field()
     */
    public function state(InvoiceAddress $invoiceAddress): ?State
    {
        try {
            return $this->stateService->state(
                (string) $invoiceAddress->stateId()
            );
        } catch (StateNotFound $e) {
            return null;
        }
    }
}
