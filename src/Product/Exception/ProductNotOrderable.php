<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;

use function sprintf;

final class ProductNotOrderable extends Error
{
    public function __construct($id)
    {
        parent::__construct(sprintf('Product with id %s can not be ordered', $id));
    }
}
