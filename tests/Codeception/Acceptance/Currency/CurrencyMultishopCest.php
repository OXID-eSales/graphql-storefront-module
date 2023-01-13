<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Currency;

use Codeception\Example;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group currency
 * @group oe_graphql_storefront
 * @group other
 */
final class CurrencyMultishopCest extends MultishopBaseCest
{
    public function testGetSecondShopCurrency(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
            currency {
                id
                name
                rate
                sign
            }
        }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $configCurrency = Registry::getConfig()->getActShopCurrencyObject();
        $resultCurrency = $response['data']['currency'];

        $I->assertEquals(
            $configCurrency->id,
            $resultCurrency['id']
        );
        $I->assertEquals(
            $configCurrency->name,
            $resultCurrency['name']
        );
        $I->assertEquals(
            (float)$configCurrency->rate,
            $resultCurrency['rate']
        );
        $I->assertEquals(
            $configCurrency->sign,
            $resultCurrency['sign']
        );
    }


    /**
     * @dataProvider currencyNames
     */
    public function testGetCurrencyByName(AcceptanceTester $I, Example $data): void
    {
        $I->sendGQLQuery(
            sprintf(
                'query {
                currency (name: "%s") {
                    id
                    name
                    rate
                    sign
                }
            }',
                $data['name']
            )
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $configCurrency = Registry::getConfig()->getCurrencyObject($data['name']);
        $resultCurrency = $response['data']['currency'];

        $I->assertEquals(
            $configCurrency->id,
            $resultCurrency['id']
        );
        $I->assertEquals(
            $configCurrency->name,
            $resultCurrency['name']
        );
        $I->assertEquals(
            (float)$configCurrency->rate,
            $resultCurrency['rate']
        );
        $I->assertEquals(
            $configCurrency->sign,
            $resultCurrency['sign']
        );
    }

    protected function currencyNames(): array
    {
        return [
            ['name' => 'EUR'],
            ['name' => 'GBP'],
            ['name' => 'USD'],
            ['name' => 'CHF'],
        ];
    }

    /**
     * @dataProvider incorrectCurrencyNames
     */
    public function testGetCurrencyByNameShouldFail(AcceptanceTester $I, Example $data): void
    {
        $I->sendGQLQuery(
            sprintf(
                'query {
                    currency (name: "%s") {
                        id
                        name
                        rate
                        sign
                    }
                }',
                $data['name']
            )
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Currency "' . $data['name'] . '" was not found',
            $response['errors'][0]['message']
        );
    }

    protected function incorrectCurrencyNames(): array
    {
        return [
            ['name' => 'US'],
            ['name' => 'EU'],
            ['name' => 'ABC'],
            ['name' => 'notACurrencyNameAtAll'],
            ['name' => 'null'],
            ['name' => '17'],
        ];
    }

    public function testGetSecondShopCurrencyList(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                currencies{
                    id
                    name
                    rate
                    sign
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $configCurrencies = Registry::getConfig()->getCurrencyArray();
        $resultCurrencies = $response['data']['currencies'];

        foreach ($configCurrencies as $key => $expectedCurrency) {
            $I->assertEquals(
                $expectedCurrency->id,
                $resultCurrencies[$key]['id']
            );
            $I->assertEquals(
                $expectedCurrency->name,
                $resultCurrencies[$key]['name']
            );
            $I->assertEquals(
                (float)$expectedCurrency->rate,
                $resultCurrencies[$key]['rate']
            );
            $I->assertEquals(
                $expectedCurrency->sign,
                $resultCurrencies[$key]['sign']
            );
        }
    }
}
