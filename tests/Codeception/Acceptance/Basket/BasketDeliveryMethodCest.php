<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use Codeception\Example;
use Codeception\Util\HttpCode;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group oe_graphql_checkout
 * @group basket
 * @group delivery
 */
final class BasketDeliveryMethodCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const BASKET_ID = 'basket_payment';

    private const BASKET_SHIPPING_ID = 'basket_shipping';

    private const PRODUCT_ID = 'dc5ffdf380e15674b56dd562a7cb6aec';

    public function testBasketDeliveries(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            $this->basketDeliveryMethodsMutation(self::BASKET_ID)
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $this->shippingCosts($I, $result['data']['basketDeliveryMethods'], [
            'price'    => 3.9,
            'vatValue' => 0.62,
        ], [
            'price'    => 6.66,
            'vatValue' => 1.06,
        ]);
    }

    /**
     * @dataProvider dataProviderDeliveryCostChange
     */
    public function testBasketDeliveryCostChange(AcceptanceTester $I, Example $data): void
    {
        $I->updateInDatabase('oxdelivery', [
            'OXFIXED'      => 0,
            'OXADDSUMTYPE' => $data['surcharge_type'],
        ], [
            'OXID' => '_graphqldel',
        ]);

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            $this->basketDeliveryMethodsMutation(self::BASKET_ID)
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $this->shippingCosts($I, $result['data']['basketDeliveryMethods'], $data['originalCost']['standard'], $data['originalCost']['graphql']);

        //Add product to apply free cost rule for standard delivery
        $this->basketAddProductMutation($I, self::BASKET_ID, self::PRODUCT_ID, $data['productAmount']);

        $I->sendGQLQuery(
            $this->basketDeliveryMethodsMutation(self::BASKET_ID)
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $this->shippingCosts($I, $result['data']['basketDeliveryMethods'], $data['changedCost']['standard'], $data['changedCost']['graphql']);

        //Reset basket
        $this->basketRemoveProductMutation($I, self::BASKET_ID, self::PRODUCT_ID, $data['productAmount']);
        $I->updateInDatabase('oxdelivery', [
            'OXFIXED'      => 0,
            'OXADDSUMTYPE' => 'abs',
        ], [
            'OXID' => '_graphqldel',
        ]);
    }

    public function dataProviderDeliveryCostChange(): array
    {
        return [
            'shipping_cost_abs'        => [
                'surcharge_type' => 'abs',
                'productAmount'  => 3,
                'originalCost'   => [
                    'standard' => [
                        'price'    => 3.9,
                        'vatValue' => 0.62,
                    ],
                    'graphql'  => [
                        'price'    => 6.66,
                        'vatValue' => 1.06,
                    ],
                ],
                'changedCost'    => [
                    'standard' => [
                        'price'    => 0,
                        'vatValue' => 0,
                    ],
                    'graphql'  => [
                        'price'    => 6.66,
                        'vatValue' => 1.06,
                    ],
                ],
            ],
            'shipping_cost_percentage' => [
                'surcharge_type' => '%',
                'productAmount'  => 1,
                'originalCost'   => [
                    'standard' => [
                        'price'    => 3.9,
                        'vatValue' => 0.62,
                    ],
                    'graphql'  => [
                        'price'    => 1.99,
                        'vatValue' => 0.32,
                    ],
                ],
                'changedCost'    => [
                    'standard' => [
                        'price'    => 3.9,
                        'vatValue' => 0.62,
                    ],
                    'graphql'  => [
                        'price'    => 3.98,
                        'vatValue' => 0.64,
                    ],
                ],
            ],
        ];
    }

    /**
     * @group testy
     * @dataProvider dataProviderDeliveryCost
     */
    public function testBasketDeliveryCostByChargeType(AcceptanceTester $I, Example $shipping): void
    {
        $I->updateInDatabase('oxdelivery', [
            'OXFIXED' => $shipping['calculation_rule'],
        ], [
            'OXID' => '_graphqldel',
        ]);

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            $this->basketDeliveryMethodsMutation(self::BASKET_SHIPPING_ID)
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $this->shippingCosts($I, $result['data']['basketDeliveryMethods'], [
            'price'    => 3.9,
            'vatValue' => 0.62,
        ], $shipping['cost']);

        //Reset delivery
        $I->updateInDatabase('oxdelivery', [
            'OXFIXED' => 0,
        ], [
            'OXID' => '_graphqldel',
        ]);
    }

    public function dataProviderDeliveryCost(): array
    {
        return [
            'shipping_cost_per_cart'              => [
                'calculation_rule' => 0,
                'cost'             => [
                    'price'    => 6.66,
                    'vatValue' => 1.06,
                ],
            ],
            'shipping_cost_per_different_product' => [
                'calculation_rule' => 1,
                'cost'             => [
                    'price'    => 13.32,
                    'vatValue' => 2.13,
                ],
            ],
            'shipping_cost_per_each_product'      => [
                'calculation_rule' => 2,
                'cost'             => [
                    'price'    => 19.98,
                    'vatValue' => 3.19,
                ],
            ],
        ];
    }

    public function getNonExistingBasketDeliveryMethods(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = 'non-existing-basket';

        $I->sendGQLQuery(
            $this->basketDeliveryMethodsMutation($basketId)
        );

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    private function basketDeliveryMethodsMutation(string $basketId): string
    {
        return '
            query {
              basketDeliveryMethods(basketId: "' . $basketId . '") {
                id
                title
                cost {
                  price
                  vat
                  vatValue
                }
              }
            }
        ';
    }

    private function basketAddProductMutation(AcceptanceTester $I, string $basketId, string $productId, int $amount = 1): void
    {
        $I->sendGQLQuery('
            mutation {
                basketAddProduct(
                    basketId: "' . $basketId . '",
                    productId: "' . $productId . '",
                    amount: ' . $amount . '
                ) {
                    id
                }
            }
        ');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    private function basketRemoveProductMutation(AcceptanceTester $I, string $basketId, string $productId, int $amount = 1): void
    {
        $I->sendGQLQuery('
            mutation {
                basketRemoveProduct(
                    basketId: "' . $basketId . '",
                    productId: "' . $productId . '",
                    amount: ' . $amount . '
                ) {
                    id
                }
            }
        ');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    private function shippingCosts(AcceptanceTester $I, array $deliveries, array $standard, array $graph): void
    {
        $I->assertSame([
            [
                'id'    => 'oxidstandard',
                'title' => 'Standard',
                'cost'  => [
                    'price'    => $standard['price'],
                    'vat'      => 19,
                    'vatValue' => $standard['vatValue'],
                ],
            ], [
                'id'    => '_deliveryset',
                'title' => 'graphql set',
                'cost'  => [
                    'price'    => $graph['price'],
                    'vat'      => 19,
                    'vatValue' => $graph['vatValue'],
                ],
            ],
        ], $deliveries);
    }
}
