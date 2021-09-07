<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use Codeception\Example;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group basket
 * @group oe_graphql_storefront
 */
final class BasketsMultishopCest extends MultishopBaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    /**
     * @dataProvider shopDataProvider
     */
    public function testGetBasketsPerShops(AcceptanceTester $I, Example $data): void
    {
        $shopId       = $data[0];
        $basketsCount = $data[1];

        $response = $this->basketsQuery($I, self::USERNAME, $shopId);

        $baskets = $response['data']['baskets'];
        $I->assertSame($basketsCount, count($baskets));
    }

    /**
     * @dataProvider shopDataProvider
     */
    public function testGetBasketsPerShopsWithMallUser(AcceptanceTester $I, Example $data): void
    {
        $shopId = $data[0];
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        $response = $this->basketsQuery($I, self::USERNAME, $shopId);

        $baskets = $response['data']['baskets'];
        $I->assertEquals(5, count($baskets));
    }

    protected function shopDataProvider(): array
    {
        return [
            [1, 4],
            [2, 1],
        ];
    }

    private function basketsQuery(AcceptanceTester $I, string $owner, $shopId = 1): array
    {
        $I->sendGQLQuery(
            'query {
                baskets(owner: "' . $owner . '") {
                    owner {
                        lastName
                    }
                    items(pagination: {limit: 10, offset: 0}) {
                        product {
                            title
                        }
                        amount
                    }
                    id
                    title
                    creationDate
                    lastUpdateDate
                }
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
