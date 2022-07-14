<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Voucher\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class VoucherNotApplied extends NotFound
{
    public function __construct(string $voucherId, string $basketId)
    {
        parent::__construct(
            sprintf(
                'Voucher with id: %s was not applied to basket with id: %s',
                $voucherId,
                $basketId
            )
        );
    }
}
