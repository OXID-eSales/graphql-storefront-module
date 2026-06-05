<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Country\DataType;

use OxidEsales\GraphQL\Base\DataType\Sorting\Sorting as BaseSorting;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class CountrySorting extends BaseSorting
{
    // By default, the countries will be sorted by their position ('oxorder' column).
    #[Factory(name: 'CountrySorting', default: true)]
    public static function fromUserInput(
        ?string $position = self::SORTING_ASC,
        ?string $title = null
    ): self {
        return new self([
            'oxorder' => $position,
            'oxtitle' => $title,
        ]);
    }
}
