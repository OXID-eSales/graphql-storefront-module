<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Service;

use OxidEsales\GraphQL\Storefront\Currency\DataType\Currency;
use OxidEsales\GraphQL\Storefront\Currency\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Price;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=Price::class)
 */
final class PriceRelationService
{
    /** @var Repository */
    private $currencyRepository;

    public function __construct(
        Repository $currencyRepository
    ) {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @Field()
     */
    public function getCurrency(Price $price): Currency
    {
        if ($currencyObject = $price->getCurrencyObject()) {
            return new Currency($currencyObject);
        }

        return $this->currencyRepository->getActiveCurrency();
    }
}
