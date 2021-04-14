<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Country\Controller;

use OxidEsales\GraphQL\Storefront\Country\DataType\Country as CountryDataType;
use OxidEsales\GraphQL\Storefront\Country\DataType\CountryFilterList;
use OxidEsales\GraphQL\Storefront\Country\DataType\CountrySorting;
use OxidEsales\GraphQL\Storefront\Country\Service\Country as CountryService;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Types\ID;

final class Country
{
    /** @var CountryService */
    private $countryService;

    public function __construct(
        CountryService $countryService
    ) {
        $this->countryService = $countryService;
    }

    /**
     * @Query()
     */
    public function country(ID $countryId): CountryDataType
    {
        return $this->countryService->country($countryId);
    }

    /**
     * @Query()
     *
     * @return CountryDataType[]
     */
    public function countries(
        ?CountryFilterList $filter = null,
        ?CountrySorting $sort = null
    ): array {
        return $this->countryService->countries(
            $filter ?? new CountryFilterList(),
            $sort ?? CountrySorting::fromUserInput()
        );
    }
}
