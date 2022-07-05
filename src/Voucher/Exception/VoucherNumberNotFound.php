<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Voucher\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

final class VoucherNumberNotFound extends NotFound
{
    protected const DEFAULT_MESSAGE = 'ERROR_MESSAGE_VOUCHER_NOVOUCHER';

    public function __construct(string $number)
    {
        parent::__construct(self::DEFAULT_MESSAGE, ['number' => $number]);
    }
}
