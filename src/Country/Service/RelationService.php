<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Country\Service;

use OxidEsales\GraphQL\Base\DataType\IDFilter;
use OxidEsales\GraphQL\Storefront\Country\DataType\Country as CountryDataType;
use OxidEsales\GraphQL\Storefront\Country\DataType\State as StateDataType;
use OxidEsales\GraphQL\Storefront\Country\DataType\StateFilterList;
use OxidEsales\GraphQL\Storefront\Country\DataType\StateSorting;
use OxidEsales\GraphQL\Storefront\Country\Service\State as StateService;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @ExtendType(class=CountryDataType::class)
 */
final class RelationService
{
    /** @var StateService */
    private $stateService;

    public function __construct(StateService $stateService)
    {
        $this->stateService = $stateService;
    }

    /**
     * @Field()
     *
     * @return StateDataType[]
     */
    public function states(
        CountryDataType $country,
        ?StateSorting $sort
    ): array {
        return $this->stateService->states(
            new StateFilterList(
                new IDFilter(
                    new ID(
                        (string) $country->getId()
                    )
                )
            ),
            $sort ?? new StateSorting([])
        );
    }
}
