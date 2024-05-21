<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\Exception;

use GraphQL\Error\Error;

final class ProductVariant extends Error
{
    public static function loadingDisabled(string $id): self
    {
        return new self(sprintf('Variant loading for product %s is disabled', $id));
    }
}
