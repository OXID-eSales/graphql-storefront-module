<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Country\DataType;

use OxidEsales\GraphQL\Base\DataType\Sorting as BaseSorting;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class CountrySorting extends BaseSorting
{
    /**
     * @Factory
     *
     * By default the countries will be sorted by their position ('oxorder' column).
     * In case you want to sort them by other field, like title for example,
     * you should set the position as an empty string.
     *
     * query {
     *      countries(
     *          sort: {
     *              position: "",
     *              title: "ASC"
     *          }
     *      ) {
     *          title
     *      }
     * }
     */
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
