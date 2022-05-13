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
final class CustomerOrderPaymentMultiShopCest extends MultishopBaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);
    }

    /**
     * @dataProvider ordersPerShopProvider
     */
    public function testCustomerOrderPaymentPerShop(AcceptanceTester $I, Example $data): void
    {
        $languageId = 0;
        $shopId = $data['shopId'];
        $orderNumber = $data['orderNumber'];
        $paymentId = $data['paymentId'];

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'query {
                customer {
                    orders {
                        orderNumber
                        payment {
                            payment {
                                id
                            }
                        }
                    }
                }
            }',
            [],
            $languageId,
            $shopId
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $orders = $result['data']['customer']['orders'];

        foreach ($orders as $order) {
            if ($order['orderNumber'] != $orderNumber) {
                continue;
            }

            $orderPayment = $order['payment'];
            $I->assertNotNull($orderPayment);
            $I->assertSame($paymentId, $orderPayment['payment']['id']);
        }
    }

    private function ordersPerShopProvider(): array
    {
        return [
            'shop_1' => [
                'shopId' => 1,
                'orderNumber' => 4,
                'paymentId' => 'oxiddebitnote',
            ],
            'shop_2' => [
                'shopId' => 2,
                'orderNumber' => 5,
                'paymentId' => 'oxidinvoice',
            ],
        ];
    }
}
