<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\DataType;

interface DataType
{
    /**
     * @return class-string
     */
    public static function getModelClass(): string;
}
