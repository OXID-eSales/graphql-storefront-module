<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use Codeception\Example;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group oe_graphql_checkout
 * @group basket
 * @group payment
 * @group oe_graphql_storefront
 */
final class BasketPaymentCest extends BaseCest
{
    private const USERNAME = 'standarduser@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const BASKET_TITLE = 'basketpayments';

    private const BASKET_PAYMENT_COST = 'basket_payment_cost';

    private const BASKET_WITH_PAYMENT_ID = 'basket_user_address_payment';

    private const BASKET_WITHOUT_PAYMENT_ID = 'basket_user_3';

    private const PAYMENT_ID = 'oxiddebitnote';

    public function _after(AcceptanceTester $I): void
    {
        $I->logout();
    }

    /**
     * @dataProvider basketPaymentProvider
     */
    public function getBasketPayment(AcceptanceTester $I, Example $data): void
    {
        $basketId = $data['basketId'];
        $paymentId = $data['paymentId'];

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                basket(basketId: "' . $basketId . '") {
                    id
                    payment {
                        id
                    }
                }
            }'
        );

        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();
        $basket = $result['data']['basket'];

        if ($paymentId !== null) {
            $I->assertSame(self::PAYMENT_ID, $basket['payment']['id']);
        } else {
            $I->assertNull($basket['payment']);
        }
    }

    public function testBasketPayments(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreate($I);

        $result = $this->basketPaymentQuery($I, $basketId);
        $expected = [
            'oxidinvoice' => [
                'id' => 'oxidinvoice',
                'title' => 'Rechnung',
                'cost' => [
                    'price' => 0,
                ],
            ],
            'oxidpayadvance' => [
                'id' => 'oxidpayadvance',
                'title' => 'Vorauskasse',
                'cost' => [
                    'price' => 0,
                ],
            ],
            'oxiddebitnote' => [
                'id' => 'oxiddebitnote',
                'title' => 'Bankeinzug/Lastschrift',
                'cost' => [
                    'price' => 0,
                ],
            ],
            'oxidcashondel' => [
                'id' => 'oxidcashondel',
                'title' => 'Nachnahme',
                'cost' => [
                    'price' => 7.5,
                ],
            ],
            'oxidgraphql' => [
                'id' => 'oxidgraphql',
                'title' => 'GraphQL',
                'cost' => [
                    'price' => 7.77,
                ],
            ],
        ];

        $I->assertNotEmpty($result['data']['basketPayments']);
        $I->assertSame(count($result['data']['basketPayments']), count($expected));

        foreach ($result['data']['basketPayments'] as $basketPayment) {
            $I->assertSame($expected[$basketPayment['id']], $basketPayment);
        }
    }

    public function testNonExistingBasketPayments(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = 'non-existing-basket';

        $I->sendGQLQuery(
            'query {
              basketPayments(basketId: "' . $basketId . '") {
                id
                title
              }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Basket was not found by id: ' . $basketId,
            $result['errors'][0]['message']
        );
    }

    /**
     * @dataProvider dataProviderPaymentCost
     */
    public function testBasketPaymentsCost(AcceptanceTester $I, Example $data): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->updateInDatabase('oxdiscount', [
            'OXACTIVE' => 1,
        ], [
            'OXID' => '9fc3e801da9cdd0b2.74513077',
        ]);

        $this->preparePayment($I, [
            'OXADDSUM' => 10,
            'OXADDSUMTYPE' => '%',
            'OXADDSUMRULES' => $data['payment_rule'],
        ]);

        $result = $this->basketPaymentQuery($I, self::BASKET_PAYMENT_COST);

        $I->assertSame([
            'id' => 'oxidgraphql',
            'title' => 'GraphQL',
            'cost' => [
                'price' => $data['cost'],
            ],
        ], end($result['data']['basketPayments']));

        //Reset payment and discounts
        $this->preparePayment($I, [
            'OXADDSUM' => 7.77,
            'OXADDSUMTYPE' => 'abs',
        ]);
        $I->updateInDatabase('oxdiscount', [
            'OXACTIVE' => 0,
        ], [
            'OXID' => '9fc3e801da9cdd0b2.74513077',
        ]);
    }

    public function testBasketPaymentsCostChange(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $this->preparePayment($I, [
            'OXADDSUM' => 10,
            'OXADDSUMTYPE' => '%',
            'OXADDSUMRULES' => 15,
        ]);

        $result = $this->basketPaymentQuery($I, self::BASKET_PAYMENT_COST);

        $I->assertSame([
            'id' => 'oxidgraphql',
            'title' => 'GraphQL',
            'cost' => [
                'price' => 25.47,
            ],
        ], end($result['data']['basketPayments']));

        $this->basketAddItemMutation($I, self::BASKET_PAYMENT_COST, 'f4f2d8eee51b0fd5eb60a46dff1166d8', 1);

        $result = $this->basketPaymentQuery($I, self::BASKET_PAYMENT_COST);

        $I->assertSame([
            'id' => 'oxidgraphql',
            'title' => 'GraphQL',
            'cost' => [
                'price' => 38.37,
            ],
        ], end($result['data']['basketPayments']));

        $this->preparePayment($I, [
            'OXADDSUM' => 7.77,
            'OXADDSUMTYPE' => 'abs',
        ]);
    }

    protected function dataProviderPaymentCost(): array
    {
        return [
            'products_discount_voucher_shipping' => [
                'payment_rule' => 15,
                'cost' => 22.89,
            ],
            'products_discount_voucher' => [
                'payment_rule' => 7,
                'cost' => 22.22,
            ],
            'products_discount' => [
                'payment_rule' => 3,
                'cost' => 23.22,
            ],
            'products' => [
                'payment_rule' => 1,
                'cost' => 25.8,
            ],
        ];
    }

    protected function basketPaymentProvider(): array
    {
        return [
            [
                'basketId' => self::BASKET_WITH_PAYMENT_ID,
                'paymentId' => self::PAYMENT_ID,
            ],
            [
                'basketId' => self::BASKET_WITHOUT_PAYMENT_ID,
                'paymentId' => null,
            ],
        ];
    }

    private function basketCreate(AcceptanceTester $I)
    {
        $I->sendGQLQuery(
            'mutation {
                basketCreate(basket: {title: "' . self::BASKET_TITLE . '"}) {
                    id
                }
            }'
        );

        $result = $I->grabJsonResponseAsArray();

        return $result['data']['basketCreate']['id'];
    }

    private function basketPaymentQuery(AcceptanceTester $I, string $basketId)
    {
        $I->sendGQLQuery(
            'query {
              basketPayments(basketId: "' . $basketId . '") {
                id
                title
                cost {
                    price
                }
              }
            }'
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function basketAddItemMutation(
        AcceptanceTester $I,
        string $basketId,
        string $productId,
        int $amount = 1
    ): void {
        $I->sendGQLQuery(
            '
            mutation {
                basketAddItem(
                    basketId: "' . $basketId . '",
                    productId: "' . $productId . '",
                    amount: ' . $amount . '
                ) {
                    id
                }
            }
        '
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            $basketId,
            $result['data']['basketAddItem']['id']
        );
    }

    private function preparePayment(AcceptanceTester $I, array $data): void
    {
        $I->updateInDatabase(
            'oxpayments',
            $data,
            [
                'OXID' => 'oxidgraphql',
            ]
        );
    }
}
