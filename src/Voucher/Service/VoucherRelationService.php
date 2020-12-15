<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Voucher\Service;

use OxidEsales\GraphQL\Storefront\Voucher\DataType\Voucher;
use OxidEsales\GraphQL\Storefront\Voucher\DataType\VoucherSeries;
use OxidEsales\GraphQL\Storefront\Voucher\Service\VoucherSeries as VoucherSeriesService;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=Voucher::class)
 */
final class VoucherRelationService
{
    /** @var VoucherSeriesService */
    private $voucherSeriesService;

    public function __construct(VoucherSeriesService $voucherSeriesService)
    {
        $this->voucherSeriesService = $voucherSeriesService;
    }

    /**
     * @Field()
     */
    public function series(Voucher $voucher): VoucherSeries
    {
        return $this->voucherSeriesService->series((string) $voucher->seriesId());
    }
}
