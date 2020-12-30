<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Voucher\Exception;

use OxidEsales\GraphQL\Base\Exception\Exists;

final class VoucherNotUsable extends Exists
{
    public static function withMessage(string $message): self
    {
        return new self($message);
    }

    public static function noProductsMessage(): self
    {
        return new self("MESSAGE_COUPON_NOT_APPLIED_FOR_ARTICLES");
    }
}
