<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Currency\Infrastructure;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\GraphQL\Storefront\Currency\DataType\Currency;
use OxidEsales\GraphQL\Storefront\Currency\Exception\CurrencyNotFound;
use stdClass;

final class Repository
{
    /** @var Config */
    private $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @throws CurrencyNotFound
     */
    public function getByName(string $name): Currency
    {
        /** @var null|stdClass */
        $currency = $this->config->getCurrencyObject($name);

        if (!$currency instanceof stdClass) {
            throw new CurrencyNotFound($name);
        }

        return new Currency($currency);
    }

    /**
     * @throws CurrencyNotFound
     */
    public function getActiveCurrency(): Currency
    {
        /** @var null|stdClass */
        $currency = $this->config->getActShopCurrencyObject();

        if (!$currency instanceof stdClass) {
            throw new CurrencyNotFound();
        }

        return new Currency($currency);
    }

    /**
     * @return Currency[]
     */
    public function getAll(): array
    {
        $currencies = [];

        foreach ($this->config->getCurrencyArray() as $currencyObject) {
            $currencies[] = new Currency($currencyObject);
        }

        return $currencies;
    }
}
