<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Voucher\Service;

use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface;
use OxidEsales\GraphQL\Storefront\Voucher\DataType\VoucherSeries as SeriesDataType;
use OxidEsales\GraphQL\Storefront\Voucher\Exception\SeriesNotFound;

final class VoucherSeries
{
    /** @var RepositoryInterface */
    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function series(string $id): SeriesDataType
    {
        try {
            /** @var SeriesDataType $series */
            $series = $this->repository->getById($id, SeriesDataType::class);
        } catch (NotFound $exception) {
            throw new SeriesNotFound($id);
        }

        return $series;
    }
}
