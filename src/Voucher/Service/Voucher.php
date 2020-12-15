<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Voucher\Service;

use OxidEsales\GraphQL\Storefront\Voucher\DataType\Voucher as VoucherDataType;
use OxidEsales\GraphQL\Storefront\Voucher\Infrastructure\Repository;

final class Voucher
{
    /** @var Repository */
    private $repository;

    public function __construct(
        Repository $repository
    ) {
        $this->repository = $repository;
    }

    public function getVoucherById(string $id): VoucherDataType
    {
        return $this->repository->getVoucherById($id);
    }

    public function getVoucherByNumber(string $voucher): VoucherDataType
    {
        return $this->repository->getVoucherByNumber($voucher);
    }
}
