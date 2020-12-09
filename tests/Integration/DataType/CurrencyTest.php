<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Integration\DataType;

use OxidEsales\GraphQL\Catalogue\Currency\DataType\Currency;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers OxidEsales\GraphQL\Catalogue\Currency\DataType\Currency
 */
final class CurrencyTest extends TestCase
{
    public function testGetCurrency(): void
    {
        $currencyObject           = new stdClass();
        $currencyObject->id       = 0;
        $currencyObject->name     = 'EUR';
        $currencyObject->rate     = '1.00';
        $currencyObject->dec      = ',';
        $currencyObject->thousand = '.';
        $currencyObject->sign     = '€';
        $currencyObject->decimal  = '2';
        $currencyObject->selected = 0;

        $currency = new Currency($currencyObject);

        $this->assertSame($currencyObject->id, $currency->getId());
        $this->assertSame((float) $currencyObject->rate, $currency->getRate());
        $this->assertSame($currencyObject->name, $currency->getName());
        $this->assertSame($currencyObject->sign, $currency->getSign());
        $this->assertSame((int) $currencyObject->decimal, $currency->getPrecision());
    }
}
