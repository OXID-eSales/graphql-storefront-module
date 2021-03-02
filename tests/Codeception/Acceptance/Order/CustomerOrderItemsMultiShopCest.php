<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Order;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group order
 */
final class CustomerOrderItemsMultiShopCest extends MultishopBaseCest
{
    private const USER_SHOP_2 = 'user@oxid-esales.com';

    private const USER_SHOP_1 = 'otheruser@oxid-esales.com';

    private const PASSWORD = 'useruser';

    /**
     * User from shop 2, has created an order in shop 2 with product which belongs to shop 1.
     */
    public function testCustomerOrderItems(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', false, 'bool', [1, 2]);
        $I->login(self::USER_SHOP_2, self::PASSWORD, 2);

        $result = $this->queryCustomerOrderItems($I);
        $items  = $result['data']['customer']['orders'][0]['items'];

        $I->assertCount(1, $items);

        $expectedItem = [
            'id'               => '677688370a4a64d8336107bcf174fdeb',
            'amount'           => 1.0,
            'product'          => [
                'id'    => '_test_product_for_basket',
                'title' => 'Product 621',
            ],
            'sku'              => '621',
            'title'            => 'Product 1',
            'shortDescription' => '',
            'price'            => [
                'price' => 10.0,
                'vat'   => 19.0,
            ],
            'itemPrice'        => [
                'price' => 10.0,
            ],
            'dimensions'       => [
                'length' => 0.0,
                'width'  => 0.0,
                'height' => 0.0,
                'weight' => 0.0,
            ],
            'insert'           => '2020-05-25T00:00:00+02:00',
            'cancelled'        => false,
            'bundle'           => false,
        ];

        $I->assertEquals($expectedItem, $items[0]);
    }

    /**
     * User from shop 1, has created an order in shop 2 with product which belongs to shop 1.
     */
    public function testCustomerOrderItemsMallUsers(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        $I->login(self::USER_SHOP_1, self::PASSWORD, 2);

        $result = $this->queryCustomerOrderItems($I);

        $items  = [];
        $orders = $result['data']['customer']['orders'];

        foreach ($orders as $order) {
            if ($order['orderNumber'] == '7') {
                $items = $order['items'];

                break;
            }
        }

        $I->assertCount(1, $items);

        $expectedItem = [
            'id'               => '677688370a4a64d8336107bcf174fde1',
            'amount'           => 1.0,
            'product'          => [
                'id'    => '_test_product_for_basket',
                'title' => 'Product 621',
            ],
            'sku'              => '621',
            'title'            => 'Product 1',
            'shortDescription' => '',
            'price'            => [
                'price' => 10.0,
                'vat'   => 19.0,
            ],
            'itemPrice'        => [
                'price' => 10.0,
            ],
            'dimensions'       => [
                'length' => 0.0,
                'width'  => 0.0,
                'height' => 0.0,
                'weight' => 0.0,
            ],
            'insert'           => '2020-05-25T00:00:00+02:00',
            'cancelled'        => false,
            'bundle'           => false,
        ];

        $I->assertEquals($expectedItem, $items[0]);
    }

    private function queryCustomerOrderItems(AcceptanceTester $I): array
    {
        $I->sendGQLQuery(
            'query {
                customer {
                    id
                    orders {
                        id
                        orderNumber
                        items {
                            id
                            amount
                            product {
                                id
                                title
                            }
                            sku
                            title
                            shortDescription
                            price {
                                price
                                vat
                            }
                            itemPrice {
                                price
                            }
                            dimensions {
                                length
                                width
                                height
                                weight
                            }
                            insert
                            cancelled
                            bundle
                        }
                    }
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
