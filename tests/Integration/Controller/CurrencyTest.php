<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\GraphQL\Storefront\Tests\Integration\BaseTestCase;

/**
 * @covers OxidEsales\GraphQL\Storefront\Currency\DataType\Currency
 * @covers OxidEsales\GraphQL\Storefront\Currency\Controller\Currency
 */
final class CurrencyTest extends BaseTestCase
{
    public function testGetCurrencyQuery(): void
    {
        $result = $this->query(
            '
            query {
                currency {
                    id
                    name
                    rate
                    sign
                }
            }
        '
        );

        $configCurrency = Registry::getConfig()->getActShopCurrencyObject();
        $resultCurrency = $result['body']['data']['currency'];

        $this->assertSame(
            $configCurrency->id,
            $resultCurrency['id']
        );
        $this->assertSame(
            $configCurrency->name,
            $resultCurrency['name']
        );
        $this->assertSame(
            (float)$configCurrency->rate,
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
        $result = $this->query(
            sprintf(
                '
            query {
                currency (name: "%s") {
                    id
                    name
                    rate
                    sign
                }
            }
        ',
                $name
            )
        );

        $configCurrency = Registry::getConfig()->getCurrencyObject($name);
        $resultCurrency = $result['body']['data']['currency'];

        $this->assertSame(
            $configCurrency->id,
            $resultCurrency['id']
        );
        $this->assertSame(
            $configCurrency->name,
            $resultCurrency['name']
        );
        $this->assertSame(
            (float)$configCurrency->rate,
            $resultCurrency['rate']
        );
        $this->assertSame(
            $configCurrency->sign,
            $resultCurrency['sign']
        );
    }

    public static function currencyNames(): array
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
        $result = $this->query(
            sprintf(
                '
            query {
                currency (name: "%s") {
                    id
                    name
                    rate
                    sign
                }
            }
        ',
                $name
            )
        );

        $this->assertSame(
            'Currency "' . $name . '" was not found',
            $result['body']['errors'][0]['message']
        );
    }

    public static function incorrectCurrencyNames(): array
    {
        return [
            ['US'],
            ['ABC'],
            ['EU'],
            ['notACurrencyNameAtAll'],
        ];
    }

    public function testGetCurrencyList(): void
    {
        $result = $this->query(
            '
            query {
                currencies{
                    id
                    name
                    rate
                    sign
                }
            }
        '
        );

        $configCurrencies = Registry::getConfig()->getCurrencyArray();
        $resultCurrencies = $result['body']['data']['currencies'];

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
                (float)$expectedCurrency->rate,
                $resultCurrencies[$key]['rate']
            );
            $this->assertSame(
                $expectedCurrency->sign,
                $resultCurrencies[$key]['sign']
            );
        }
    }
}
