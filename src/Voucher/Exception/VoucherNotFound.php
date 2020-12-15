<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Voucher\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class VoucherNotFound extends NotFound
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Voucher was not found by id: %s', $id));
    }

    public static function byNumber(string $number): self
    {
        return new self(sprintf('Voucher by number: %s was not found or was not applicable', $number));
    }
}
