<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group basket
 * @group oe_graphql_storefront
 */
final class BasketsCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const BASKET_ID = 'test_make_wishlist_private'; //owned by user@oxid-esales.com

    private const BASKET_ID_2 = '_test_basket_private'; //owned by otheruser@oxid-esales.com

    private const BASKET_ID_3 = '_test_wish_list_private'; //owned by otheruser@oxid-esales.com

    private const LAST_NAME = 'Muster';

    public function testBasketsWithoutToken(AcceptanceTester $I): void
    {
        $response = $this->basketsQuery($I, self::USERNAME);

        $baskets = $response['data']['baskets'];

        $I->assertSame(4, count($baskets));
    }

    public function testGetOnlyPublicBaskets(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);
        $this->basketMakePrivateMutation($I, self::BASKET_ID);

        $I->logout();
        $response = $this->basketsQuery($I, self::USERNAME);

        $baskets = $response['data']['baskets'];
        $I->assertSame(3, count($baskets));

        // restore database
        $I->login(self::USERNAME, self::PASSWORD);
        $this->basketMakePublicMutation($I, self::BASKET_ID);
    }

    public function testGetBasketsFromOtherUser(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::PASSWORD);
        $this->basketMakePublicMutation($I, self::BASKET_ID_2);

        $I->login(self::USERNAME, self::PASSWORD);
        $response = $this->basketsQuery($I, self::OTHER_USERNAME);

        $baskets = $response['data']['baskets'];
        $I->assertSame(1, count($baskets));

        // restore database
        $I->login(self::OTHER_USERNAME, self::PASSWORD);
        $this->basketMakePrivateMutation($I, self::BASKET_ID_2);
    }

    public function testGetBasketsByLastName(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::PASSWORD);
        $this->basketMakePublicMutation($I, self::BASKET_ID_2);
        $this->basketMakePublicMutation($I, self::BASKET_ID_3);

        $I->logout();
        $response = $this->basketsQuery($I, self::LAST_NAME);

        $baskets = $response['data']['baskets'];
        $I->assertSame(6, count($baskets));
    }

    public function testBasketsCosts(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                baskets(owner: "' . self::USERNAME . '") {
                    id
                    cost {
                        productNet {
                            price
                        }
                        payment {
                            price
                        }
                        discount
                        total
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame([
            [
                'id'   => self::BASKET_ID,
                'cost' => [
                    'productNet' => [
                        'price' => 0,
                    ],
                    'payment'    => [
                        'price' => 0,
                    ],
                    'discount'   => 0,
                    'total'      => 0,
                ],
            ], [
                'id'   => '_test_basket_public',
                'cost' => [
                    'productNet' => [
                        'price' => 8.4,
                    ],
                    'payment'    => [
                        'price' => 7.5,
                    ],
                    'discount'   => 0,
                    'total'      => 21.4,
                ],
            ], [
                'id'   => '_test_voucher_public',
                'cost' => [
                    'productNet' => [
                        'price' => 8.4,
                    ],
                    'payment'    => [
                        'price' => 0,
                    ],
                    'discount'   => 0,
                    'total'      => 13.9,
                ],
            ], [
                'id'   => '_test_wish_list_public',
                'cost' => [
                    'productNet' => [
                        'price' => 8.4,
                    ],
                    'payment'    => [
                        'price' => 0,
                    ],
                    'discount'   => 0,
                    'total'      => 13.9,
                ],
            ],
        ], $result['data']['baskets']);
    }

    private function basketsQuery(AcceptanceTester $I, string $owner): array
    {
        $I->sendGQLQuery('query {
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
                public
                creationDate
                lastUpdateDate
            }
        }');

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function basketMakePrivateMutation(AcceptanceTester $I, string $basketId): array
    {
        $I->sendGQLQuery('mutation {
            basketMakePrivate(basketId: "' . $basketId . '") {
                public
            }
        }');

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function basketMakePublicMutation(AcceptanceTester $I, string $basketId): array
    {
        $I->sendGQLQuery('mutation {
            basketMakePublic(basketId: "' . $basketId . '") {
                public
            }
        }');

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
