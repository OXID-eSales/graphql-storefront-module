<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Content\DataType;

use OxidEsales\GraphQL\Base\DataType\Sorting as BaseSorting;

final class Sorting extends BaseSorting
{
    public function __construct()
    {
        parent::__construct([]);
    }
}
