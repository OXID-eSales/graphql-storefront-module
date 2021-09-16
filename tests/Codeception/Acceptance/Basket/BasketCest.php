<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use Codeception\Example;
use GraphQL\Validator\Rules\FieldsOnCorrectType;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group basket
 * @group oe_graphql_storefront
 */
final class BasketCest extends BaseCest
{
    // Public basket
    private const PUBLIC_BASKET = '_test_basket_public';

    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    // Private basket
    private const PRIVATE_BASKET = '_test_basket_private';

    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const OTHER_PASSWORD = 'useruser';

    private const DIFFERENT_USERNAME = 'differentuser@oxid-esales.com';

    private const PRODUCT = '_test_product_for_basket';

    private const BASKET_WISH_LIST = 'wishlist';

    private const BASKET_NOTICE_LIST = 'noticelist';

    private const BASKET_SAVED_BASKET = 'savedbasket';

    public function _after(AcceptanceTester $I): void
    {
        $I->logout();
    }

    public function testGetPublicBasket(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query{
                basket(basketId: "' . self::PUBLIC_BASKET . '") {
                    items {
                        id
                        amount
                        lastUpdateDate
                        product {
                            id
                        }
                    }
                    id
                    public
                    creationDate
                    lastUpdateDate
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $basket = $result['data']['basket'];
        $I->assertEquals(self::PUBLIC_BASKET, $basket['id']);
        $I->assertEquals(true, $basket['public']);

        $I->assertEquals(1, count($basket['items']));
        $basketItem = $basket['items'][0];
        $I->assertEquals('_test_basket_item_1', $basketItem['id']);
        $I->assertEquals(1, $basketItem['amount']);
        $I->assertEquals(self::PRODUCT, $basketItem['product']['id']);
    }

    public function testGetPrivateBasketAuthorized(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);

        $I->sendGQLQuery(
            'query{
                basket(basketId: "' . self::PRIVATE_BASKET . '") {
                    id
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $basket = $result['data']['basket'];

        $I->assertEquals(self::PRIVATE_BASKET, $basket['id']);
    }

    /**
     * @dataProvider boolDataProvider
     */
    public function testGetPrivateBasketNotAuthorized(AcceptanceTester $I, Example $data): void
    {
        $isLogged = $data[0];

        if ($isLogged) {
            $I->login(self::USERNAME, self::PASSWORD);
        }

        $I->sendGQLQuery(
            'query{
                basket(basketId: "' . self::PRIVATE_BASKET . '") {
                    id
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        if ($isLogged) {
            $I->assertSame(
                'Basket is private.',
                $result['errors'][0]['message']
            );
        } else {
            $I->assertSame(
                'The token is invalid',
                $result['errors'][0]['message']
            );
        }
    }

    /**
     * @dataProvider basketCreateDataProvider
     */
    public function testBasketCreateMutation(AcceptanceTester $I, Example $data): void
    {
        $title = $data[0];

        $I->login(self::DIFFERENT_USERNAME, self::PASSWORD);

        $result = $this->basketCreateMutation($I, $title);

        $basket = $result['data']['basketCreate'];
        $I->assertSame('Marc', $basket['owner']['firstName']);
        $I->assertNotEmpty($basket['id']);
        $I->assertFalse($basket['public']);
        $I->assertEmpty($basket['items']);

        $result = $this->basketRemoveMutation($I, $basket['id']);
        $I->assertTrue(
            $result['data']['basketRemove']
        );

        $I->sendGQLQuery(
            'query{
                basket(basketId: "' . $basket['id'] . '") {
                    id
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Basket was not found by id: ' . $basket['id'],
            $result['errors'][0]['message']
        );
    }

    public function testCreateExistingBasketMutation(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $this->basketCreateMutation($I, self::BASKET_WISH_LIST);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            "Basket 'wishlist' already exists!",
            $result['errors'][0]['message']
        );
    }

    public function testCreateBasketMutationWithoutToken(AcceptanceTester $I): void
    {
        $this->basketCreateMutation($I, self::BASKET_WISH_LIST);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertStringStartsWith(
            'Cannot query field "basketCreate" on type "Mutation".',
            $result['errors'][0]['message']
        );
    }

    /**
     * @group allowed_to_fail_for_anonymous_token
     */
    public function testCreateBasketMutationAnonymousUser(AcceptanceTester $I): void
    {
        $I->login();

        $result = $this->basketCreateMutation($I, self::BASKET_WISH_LIST);

        $expectedMessage = FieldsOnCorrectType::undefinedFieldMessage('basketCreate', 'Mutation', [], []);
        $I->assertEquals($expectedMessage, $result['errors'][0]['message']);
    }

    public function testBasketCost(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query{
                basket(basketId: "' . self::PUBLIC_BASKET . '") {
                    id
                    cost {
                        productNet {
                            price
                            vat
                        }
                        productGross {
                            vats {
                                vatRate
                                vatPrice
                            }
                            sum
                        }
                        currency {
                            name
                            rate
                        }
                        payment {
                            price
                        }
                        discount
                        voucher
                        total
                        delivery {
                            price
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $this->assertCost($I, $result['data']['basket']['cost']);
    }

    public function testBasketCostForPublicBasketAndNotLoggedInUser(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query{
                basket(basketId: "' . self::PUBLIC_BASKET . '") {
                    id
                    cost {
                        productNet {
                            price
                            vat
                        }
                        productGross {
                            vats {
                                vatRate
                                vatPrice
                            }
                            sum
                        }
                        currency {
                            name
                            rate
                        }
                        payment {
                            price
                        }
                        discount
                        voucher
                        total
                        delivery {
                            price
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        //We have no user so delivery costs will not be calculated unless blCalculateDelCostIfNotLoggedIn is set
        $this->assertCost($I, $result['data']['basket']['cost'], 0.0);
    }

    protected function boolDataProvider(): array
    {
        return [
            [
                true,
            ],
            [
                false,
            ],
        ];
    }

    protected function basketCreateDataProvider(): array
    {
        return [
            [self::BASKET_WISH_LIST],
            [self::BASKET_NOTICE_LIST],
            [self::BASKET_SAVED_BASKET],
            ['non-existing-list'],
        ];
    }

    private function basketCreateMutation(AcceptanceTester $I, string $title): array
    {
        $I->sendGQLQuery('mutation {
            basketCreate(basket: {title: "' . $title . '"}) {
                owner {
                    firstName
                }
                items(pagination: {limit: 10, offset: 0}) {
                    product {
                        title
                    }
                }
                id
                public
            }
        }');

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function basketRemoveMutation(AcceptanceTester $I, string $basketId): array
    {
        $I->sendGQLQuery('mutation {
            basketRemove(basketId: "' . $basketId . '")
        }');

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function assertCost(AcceptanceTester $I, array $costs, float $expectedDeliveryPrice = 3.9): void
    {
        $expected = [
            'productNet'   => [
                'price' => 8.4,
                'vat'   => 0,
            ],
            'productGross' => [
                'vats' => [
                    [
                        'vatRate'  => 19,
                        'vatPrice' => 1.6,
                    ],
                ],
                'sum'  => 10,
            ],
            'currency'     => [
                'name' => 'EUR',
                'rate' => 1,
            ],
            'payment'      => [
                'price' => 7.5,
            ],
            'discount'     => 0,
            'voucher'      => 0,
            'total'        => 17.5 + $expectedDeliveryPrice,
            'delivery'     => [
                'price' => $expectedDeliveryPrice,
            ],
        ];

        $I->assertEquals($expected, $costs);
    }
}
