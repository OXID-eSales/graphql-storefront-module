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
use TheCodingMachine\GraphQLite\Middlewares\MissingAuthorizationException;

/**
 * @group basket
 * @group basket_access
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

    private const PUBLIC_NOTICE_LIST = '_test_noticelist_public';

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
                publicBasket(basketId: "' . self::PUBLIC_BASKET . '") {
                    items {
                        id
                        amount
                        lastUpdateDate
                        product {
                            id
                        }
                    }
                    id
                    creationDate
                    lastUpdateDate
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $basket = $result['data']['publicBasket'];
        $I->assertEquals(self::PUBLIC_BASKET, $basket['id']);

        $I->assertEquals(1, count($basket['items']));
        $basketItem = $basket['items'][0];
        $I->assertEquals('_test_basket_item_1', $basketItem['id']);
        $I->assertEquals(1, $basketItem['amount']);
        $I->assertEquals(self::PRODUCT, $basketItem['product']['id']);
    }

    public function testPublicNoticelistNotShownInPublicBasketQuery(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query{
                publicBasket(basketId: "' . self::PUBLIC_NOTICE_LIST . '") {
                    id
                    creationDate
                    lastUpdateDate
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Basket is private.',
            $result['errors'][0]['message']
        );
    }

    public function testGetPrivateBasketAuthorized(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);

        $result = $this->queryBasket($I, self::PRIVATE_BASKET);
        $basket = $result['data']['basket'];

        $I->assertEquals(self::PRIVATE_BASKET, $basket['id']);
    }

    /**
     * @dataProvider getPrivateBasketNotAuthorizedDataProvider
     */
    public function testGetPrivateBasketNotAuthorized(AcceptanceTester $I, Example $example): void
    {
        if ($example['isLogged']) {
            $I->login(self::USERNAME, self::PASSWORD);
        }

        $result = $this->queryBasket($I, self::PRIVATE_BASKET);

        $I->assertSame($example['message'], $result['errors'][0]['message']);
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
        $I->assertTrue($result['data']['basketRemove']);

        $result = $this->queryBasket($I, $basket['id']);

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
            MissingAuthorizationException::forbidden()->getMessage(),
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

        $I->assertEquals(
            MissingAuthorizationException::forbidden()->getMessage(),
            $result['errors'][0]['message']
        );
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

    protected function getPrivateBasketNotAuthorizedDataProvider(): array
    {
        return [
            [
                'isLogged' => true,
                'message'  => 'You are not allowed to access this basket as it belongs to somebody else',
            ],
            [
                'isLogged' => false,
                'message'  => 'You do not have sufficient rights to access this field',
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

    private function queryBasket(AcceptanceTester $I, string $basketId): array
    {
        $I->sendGQLQuery(
            'query{
                basket(basketId: "' . $basketId . '") {
                    id
                }
            }'
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
