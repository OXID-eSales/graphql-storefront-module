<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Country\DataType;

use OxidEsales\GraphQL\Base\DataType\Sorting as BaseSorting;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class StateSorting extends BaseSorting
{
    /**
     * @Factory(name="StateSorting")
     */
    public static function fromUserInput(
        ?string $title = null
    ): self {
        return new self([
            'oxtitle' => $title,
        ]);
    }
}
