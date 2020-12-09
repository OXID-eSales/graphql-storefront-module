<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Promotion\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

final class PromotionNotFound extends NotFound
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Promotion was not found by id: %s', $id));
    }
}
