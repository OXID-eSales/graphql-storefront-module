<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Promotion\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

final class PromotionNotFound extends NotFound
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Promotion was not found by id: %s', $id));
    }
}
