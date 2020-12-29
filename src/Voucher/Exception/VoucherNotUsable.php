<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Voucher\Exception;

use OxidEsales\GraphQL\Base\Exception\Exists;

// From core:
//
//'ERROR_MESSAGE_VOUCHER_INCORRECTPRICE' on too low price
//'ERROR_MESSAGE_VOUCHER_NOTALLOWEDOTHERSERIES' on other series limitation
//'ERROR_MESSAGE_VOUCHER_NOTALLOWEDSAMESERIES' on same series limitation
//'ERROR_MESSAGE_VOUCHER_NOTVALIDUSERGROUP' on incorrect user group,
//'MESSAGE_COUPON_EXPIRED' on expired coupon
//'ERROR_MESSAGE_VOUCHER_NOVOUCHER' if coupon is not yet active
//
// Module special:
//
//'MESSAGE_COUPON_NOT_APPLIED_FOR_ARTICLES' if there are no products (specific product or category configuration does not fit) for this voucher
//'MESSAGE_COUPON_NOT_APPLIED_FOR_SHOP' if coupon serie is not configured for current shop

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
