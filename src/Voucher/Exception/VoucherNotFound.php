<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Voucher\Exception;

use OxidEsales\GraphQL\Storefront\Shared\Exception\NotFound;

final class VoucherNotFound extends NotFound
{
    protected const DEFAULT_MESSAGE = 'ERROR_MESSAGE_VOUCHER_NOVOUCHER';

    public static function byId(string $id): self
    {
        return new self(
            self::DEFAULT_MESSAGE,
            null,
            null,
            null,
            null,
            null,
            [
                'id' => $id,
            ]
        );
    }

    public static function byNumber(string $number): self
    {
        return new self(
            self::DEFAULT_MESSAGE,
            null,
            null,
            null,
            null,
            null,
            [
                'number' => $number,
            ]
        );
    }
}
