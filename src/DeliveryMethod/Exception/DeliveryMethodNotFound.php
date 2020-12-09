<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\DeliveryMethod\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

final class DeliveryMethodNotFound extends NotFound
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Delivery method was not found by id: %s', $id));
    }
}
