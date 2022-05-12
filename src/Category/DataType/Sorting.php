<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Category\DataType;

use OxidEsales\GraphQL\Base\DataType\Sorting\Sorting as BaseSorting;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class Sorting extends BaseSorting
{
    /**
     * @Factory(name="CategorySorting", default=true)
     *
     * By default the categories will be sorted by their position ('oxsort' column).
     * In case you want to sort them by other field, like title for example,
     * you should set the position as an empty string.
     *
     * query {
     *      categories(
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
            'oxsort' => $position,
            'oxtitle' => $title,
        ]);
    }
}
