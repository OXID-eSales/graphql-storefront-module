<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Integration\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\GraphQL\Base\Tests\Integration\MultishopTestCase;

/**
 * @covers OxidEsales\GraphQL\Catalogue\Currency\DataType\Currency
 */
final class CurrencyEnterpriseTest extends MultishopTestCase
{
    public function testGetSecondShopCurrency(): void
    {
        $this->setGETRequestParameter('shp', '2');

        $result = $this->query('
            query {
                currency {
                    id
                    name
                    rate
                    sign
                }
            }
        ');

        $configCurrency = Registry::getConfig()->getActShopCurrencyObject();
        $resultCurrency = $result['body']['data']['currency'];

        $this->assertResponseStatus(
            200,
            $result
        );
        $this->assertSame(
            $configCurrency->id,
            $resultCurrency['id']
        );
        $this->assertSame(
            $configCurrency->name,
            $resultCurrency['name']
        );
        $this->assertSame(
            (float) $configCurrency->rate,
            $resultCurrency['rate']
        );
        $this->assertSame(
            $configCurrency->sign,
            $resultCurrency['sign']
        );
    }

    /**
     * @dataProvider currencyNames
     */
    public function testGetCurrencyByName(string $name): void
    {
        $result = $this->query(sprintf('
            query {
                currency (name: "%s") {
                    id
                    name
                    rate
                    sign
                }
            }
        ', $name));

        $configCurrency = Registry::getConfig()->getCurrencyObject($name);
        $resultCurrency = $result['body']['data']['currency'];

        $this->assertResponseStatus(
            200,
            $result
        );
        $this->assertSame(
            $configCurrency->id,
            $resultCurrency['id']
        );
        $this->assertSame(
            $configCurrency->name,
            $resultCurrency['name']
        );
        $this->assertSame(
            (float) $configCurrency->rate,
            $resultCurrency['rate']
        );
        $this->assertSame(
            $configCurrency->sign,
            $resultCurrency['sign']
        );
    }

    public function currencyNames(): array
    {
        return [
            ['EUR'],
            ['GBP'],
            ['USD'],
            ['CHF'],
        ];
    }

    /**
     * @dataProvider incorrectCurrencyNames
     */
    public function testGetCurrencyByNameShouldFail(string $name): void
    {
        $result = $this->query(sprintf('
            query {
                currency (name: "%s") {
                    id
                    name
                    rate
                    sign
                }
            }
        ', $name));

        $this->assertResponseStatus(
            404,
            $result
        );
    }

    public function incorrectCurrencyNames(): array
    {
        return [
            ['US'],
            ['EU'],
            ['ABC'],
            ['notACurrencyNameAtAll'],
            ['null'],
            [17],
        ];
    }

    public function testGetSecondShopCurrencyList(): void
    {
        $this->setGETRequestParameter('shp', '2');

        $result = $this->query('
            query {
                currencies{
                    id
                    name
                    rate
                    sign
                }
            }
        ');

        $configCurrencies = Registry::getConfig()->getCurrencyArray();
        $resultCurrencies = $result['body']['data']['currencies'];

        $this->assertResponseStatus(200, $result);

        foreach ($configCurrencies as $key => $expectedCurrency) {
            $this->assertSame(
                $expectedCurrency->id,
                $resultCurrencies[$key]['id']
            );
            $this->assertSame(
                $expectedCurrency->name,
                $resultCurrencies[$key]['name']
            );
            $this->assertSame(
                (float) $expectedCurrency->rate,
                $resultCurrencies[$key]['rate']
            );
            $this->assertSame(
                $expectedCurrency->sign,
                $resultCurrencies[$key]['sign']
            );
        }
    }
}
