<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group basket
 * @group basket_access
 * @group oe_graphql_storefront
 */
final class BasketMultishopCest extends MultishopBaseCest
{
    private const EXISTING_USERNAME = 'existinguser@oxid-esales.com';

    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const PUBLIC_BASKET = '_test_basket_public'; //owned by shop1 user

    private const PRIVATE_BASKET = '_test_basket_private_ex'; //owned by existinguser

    private const BASKET_NOTICE_LIST = 'noticelist';

    public function testGetNotOwnedBasketFromDifferentShop(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD, 2);

        $result = $this->queryPublicBasket($I, self::PRIVATE_BASKET, 2);

        $I->assertSame(
            'Basket is private.',
            $result['errors'][0]['message']
        );
    }

    public function testGetPublicBasketFromDifferentShopNoToken(AcceptanceTester $I): void
    {
        $result = $this->queryPublicBasket($I, self::PUBLIC_BASKET, 2);

        $I->assertSame(
            self::PUBLIC_BASKET,
            $result['data']['publicBasket']['id']
        );
    }

    // TODO Add case for publicBasket query
    public function testGetPrivateBasketFromDifferentShopWithTokenForMallUser(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        $I->login(self::EXISTING_USERNAME, self::PASSWORD, 2);

        $result = $this->queryPublicBasket($I, self::PRIVATE_BASKET, 2);

        $I->assertSame(
            'You are not allowed to access this basket as it belongs to somebody else',
            $result['errors'][0]['message']
        );
    }

    public function testGetPrivateBasketFromSubShopWithToken(AcceptanceTester $I): void
    {
        $I->login(self::EXISTING_USERNAME, self::PASSWORD, 2);

        $result = $this->queryBasket($I, self::PRIVATE_BASKET, 2);

        $I->assertSame(
            self::PRIVATE_BASKET,
            $result['data']['basket']['id']
        );
    }

    public function testCreatePrivateBasketFromDifferentShop(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result   = $this->createBasket($I, self::BASKET_NOTICE_LIST, 'false');
        $basketId = $result['data']['basketCreate']['id'];

        $I->logout();
        $I->login(self::USERNAME, self::PASSWORD, 2);

        $result = $this->queryBasket($I, $basketId, 2);

        $I->assertSame(
            'You are not allowed to access this basket as it belongs to somebody else',
            $result['errors'][0]['message']
        );

        $I->logout();
        $I->login(self::USERNAME, self::PASSWORD);

        $this->removeBasket($I, $basketId, 1);
    }

    public function testCreatePrivateBasketFromDifferentShopForMallUser(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        $I->login(self::USERNAME, self::PASSWORD);

        $result   = $this->createBasket($I, self::BASKET_NOTICE_LIST, 'false');
        $basketId = $result['data']['basketCreate']['id'];

        $I->logout();
        $I->login(self::USERNAME, self::PASSWORD, 2);

        $result = $this->createBasket($I, self::BASKET_NOTICE_LIST, 'false', 2);

        $I->assertSame(
            "Basket '" . self::BASKET_NOTICE_LIST . "' already exists!",
            $result['errors'][0]['message']
        );

        $this->queryBasket($I, $basketId, 2);
        $this->removeBasket($I, $basketId, 2);
    }

    public function testBasketCostFromDifferentShop(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        $I->login(self::USERNAME, self::PASSWORD, 2);

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
                        payment {
                            price
                            vat
                        }
                        currency {
                            name
                            rate
                        }
                        discount
                        voucher
                        total
                        delivery {
                            price
                        }
                    }
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
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
                'payment' => [
                    'price' => 7.5,
                    'vat'   => 19,
                ],
                'currency'     => [
                    'name' => 'EUR',
                    'rate' => 1,
                ],
                'discount'     => 0,
                'voucher'      => 0,
                'total'        => 21.4,
                'delivery'     => [
                    'price' => 3.9,
                ],
            ],
            $result['data']['basket']['cost']
        );
    }

    private function queryBasket(AcceptanceTester $I, string $id, int $shopId): array
    {
        $I->sendGQLQuery(
            'query {
                basket(basketId: "' . $id . '") {
                    id
                    public
                }
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function queryPublicBasket(AcceptanceTester $I, string $id, int $shopId): array
    {
        $I->sendGQLQuery(
            'query {
                publicBasket(basketId: "' . $id . '") {
                    id
                }
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function createBasket(AcceptanceTester $I, string $title, string $public = 'true', int $shopId = 1): array
    {
        $I->sendGQLQuery(
            'mutation {
                basketCreate(basket: {title: "' . $title . '", public: ' . $public . '}) {
                    id
                }
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function removeBasket(AcceptanceTester $I, string $id, int $shopId = 1): array
    {
        $I->sendGQLQuery(
            'mutation {
                basketRemove(basketId: "' . $id . '")
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
