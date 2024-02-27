<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Infrastructure;

use OxidEsales\Eshop\Application\Model\PriceAlarm;
use OxidEsales\GraphQL\Storefront\Currency\Infrastructure\Repository as CurrencyRepository;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPrice;
use TheCodingMachine\GraphQLite\Types\ID;

final class WishedPriceFactory
{
    /** @var CurrencyRepository */
    private $currencyRepository;

    public function __construct(
        CurrencyRepository $currencyRepository
    ) {
        $this->currencyRepository = $currencyRepository;
    }

    public function createWishedPrice(
        string $userId,
        string $userName,
        ID $productId,
        string $currencyName,
        float $price
    ): WishedPrice {
        $currency = $this->currencyRepository->getByName($currencyName);

        /** @var PriceAlarm $model */
        $model = oxNew(PriceAlarm::class);
        $model->assign(
            [
                'OXUSERID' => $userId,
                'OXEMAIL' => $userName,
                'OXARTID' => (string)$productId->val(),
                'OXPRICE' => round($price, $currency->getPrecision()),
                'OXCURRENCY' => $currency->getName(),
            ]
        );

        return new WishedPrice($model);
    }
}
