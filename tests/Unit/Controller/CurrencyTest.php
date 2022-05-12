<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Controller;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\GraphQL\Storefront\Currency\Controller\Currency;
use OxidEsales\GraphQL\Storefront\Currency\DataType\Currency as CurrencyDataType;
use OxidEsales\GraphQL\Storefront\Currency\Exception\CurrencyNotFound;
use OxidEsales\GraphQL\Storefront\Currency\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Currency\Service\Currency as CurrencyService;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers OxidEsales\GraphQL\Storefront\Currency\Controller\Currency
 * @covers OxidEsales\GraphQL\Storefront\Currency\Infrastructure\Repository
 * @covers OxidEsales\GraphQL\Storefront\Currency\Service\Currency
 * @covers OxidEsales\GraphQL\Storefront\Currency\Exception\CurrencyNotFound
 */
final class CurrencyTest extends TestCase
{
    public function testGetCurrencyFromController(): void
    {
        $currency = new Currency(
            new CurrencyService(
                new Repository(
                    new ValidCurrenciesConfig()
                )
            )
        );
        $this->assertInstanceOf(
            CurrencyDataType::class,
            $currency->currency('EUR')
        );
        $this->assertInstanceOf(
            CurrencyDataType::class,
            $currency->currency()
        );
    }

    public function testExceptionFromControllerOnWrongCurrency(): void
    {
        $currency = new Currency(
            new CurrencyService(
                new Repository(
                    new InvalidCurrenciesConfig()
                )
            )
        );
        $this->expectException(CurrencyNotFound::class);
        $currency->currency('FOOBAR');
    }

    public function testExceptionFromControllerOnNoActiveCurrency(): void
    {
        $currency = new Currency(
            new CurrencyService(
                new Repository(
                    new InvalidCurrenciesConfig()
                )
            )
        );
        $this->expectException(CurrencyNotFound::class);
        $currency->currency();
    }

    public function testGetCurrencyList(): void
    {
        $currency = new Currency(
            new CurrencyService(
                new Repository(
                    new ValidCurrenciesConfig()
                )
            )
        );
        $this->assertCount(
            1,
            $currency->currencies()
        );
        $this->assertInstanceOf(
            CurrencyDataType::class,
            $currency->currencies()[0]
        );
    }

    public function testGetEmptyCurrencyList(): void
    {
        $currency = new Currency(
            new CurrencyService(
                new Repository(
                    new InvalidCurrenciesConfig()
                )
            )
        );
        $this->assertSame(
            [],
            $currency->currencies()
        );
    }
}

final class ValidCurrenciesConfig extends Config // phpcs:ignore
{
    public function getCurrencyObject($name)
    {
        $cur = new stdClass();
        $cur->id = 0;
        $cur->name = $name;
        $cur->rate = '1.0';
        $cur->dec = ',';
        $cur->thousand = '.';
        $cur->sign = '€';
        $cur->decimal = '2';

        return $cur;
    }

    public function getActShopCurrencyObject()
    {
        $cur = new stdClass();
        $cur->id = 0;
        $cur->name = 'EUR';
        $cur->rate = '1.0';
        $cur->dec = ',';
        $cur->thousand = '.';
        $cur->sign = '€';
        $cur->decimal = '2';

        return $cur;
    }

    public function getCurrencyArray($currency = null)
    {
        return [
            $this->getActShopCurrencyObject(),
        ];
    }
}

final class InvalidCurrenciesConfig extends Config // phpcs:ignore
{
    public function getCurrencyObject($name)
    {
        return null;
    }

    public function getActShopCurrencyObject()
    {
        return null;
    }

    public function getCurrencyArray($currency = null)
    {
        return [];
    }
}
