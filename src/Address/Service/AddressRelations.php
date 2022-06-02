<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Service;

use OxidEsales\GraphQL\Storefront\Address\DataType\AddressInterface;
use OxidEsales\GraphQL\Storefront\Country\DataType\Country;
use OxidEsales\GraphQL\Storefront\Country\DataType\State;
use OxidEsales\GraphQL\Storefront\Country\Exception\StateNotFound;
use OxidEsales\GraphQL\Storefront\Country\Service\Country as CountryService;
use OxidEsales\GraphQL\Storefront\Country\Service\State as StateService;
use TheCodingMachine\GraphQLite\Annotations\Field;

abstract class AddressRelations
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
        $this->stateService = $stateService;
    }

    /**
     * @Field()
     */
    public function country(AddressInterface $address): ?Country
    {
        return $this->countryService->country(
            $address->countryId()
        );
    }

    /**
     * @Field()
     */
    public function state(AddressInterface $address): ?State
    {
        try {
            return $this->stateService->state(
                (string)$address->stateId()
            );
        } catch (StateNotFound $e) {
            return null;
        }
    }
}
