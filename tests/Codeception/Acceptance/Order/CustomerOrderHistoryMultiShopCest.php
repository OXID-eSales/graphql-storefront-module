<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Order;

use Codeception\Example;
use Codeception\Scenario;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group order
 * @group oe_graphql_storefront
 */
final class CustomerOrderHistoryMultiShopCest extends MultishopBaseCest
{
    private const USERNAME = 'otheruser@oxid-esales.com';

    private const PASSWORD = 'useruser';

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);
    }

    /**
     * @dataProvider ordersCountProvider
     */
    public function testCustomerOrdersCountPerShop(AcceptanceTester $I, Example $data): void
    {
        $shopId = $data['shopId'];
        $expectedOrdersCount = $data['expectedOrdersCount'];

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'query {
                customer {
                    id
                    orders {
                        id
                    }
                }
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertCount($expectedOrdersCount, $result['data']['customer']['orders']);
    }

    protected function ordersCountProvider(): array
    {
        return [
            [
                'shopId' => 1,
                'expectedOrdersCount' => 3,
            ],
            [
                'shopId' => 2,
                'expectedOrdersCount' => 1,
            ],
        ];
    }
}
