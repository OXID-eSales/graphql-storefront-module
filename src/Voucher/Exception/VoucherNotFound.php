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
    public function __construct(
        $message = "ERROR_MESSAGE_VOUCHER_NOVOUCHER",
        $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function byId(string $id): self
    {
        $exception = new self();
        $exception->setContext([
            'id' => $id
        ]);

        return $exception;
    }

    public static function byNumber(string $number): self
    {
        $exception = new self();
        $exception->setContext([
            'number' => $number
        ]);

        return $exception;
    }
}
