<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Payment\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

final class PaymentNotFound extends NotFound
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Payment was not found by id: %s', $id));
    }
}
