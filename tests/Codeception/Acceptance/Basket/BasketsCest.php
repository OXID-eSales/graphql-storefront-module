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

    private const CUSTOMER_ID = 'e7af1c3b786fd02906ccd75698f4e6b9';

    private const OTHER_CUSTOMER_ID = '245ad3b5380202966df6ff128e9eecaq';

    private const BASKET_ID = 'test_make_wishlist_private'; //owned by user@oxid-esales.com

    private const BASKET_ID_2 = '_test_basket_private'; //owned by otheruser@oxid-esales.com

    private const BASKET_ID_3 = '_test_wish_list_private'; //owned by otheruser@oxid-esales.com

    private const LAST_NAME = 'Muster';

    public function _after(AcceptanceTester $I): void
    {
        $I->deleteFromDatabase('oxobject2group', ['OXID' => '_testrelationa']);
        $I->deleteFromDatabase('oxobject2group', ['OXID' => '_testrelationb']);

        parent::_after($I);
    }

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

    public function testPublicBasketsItemPriceForNotLoggedInUser(AcceptanceTester $I): void
    {
        $I->wantToTest('that for a public basket no individual prices are displayed');

        $this->assignSpecialPricesGroup($I);

        $I->sendGQLQuery(
            'query {
                baskets(owner: "' . self::USERNAME . '") {
                    id
                    items {
                        id
                        product {
                            price {
                                price
                            }
                        }
                    }
                }
            }'
        );
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('_test_basket_public', $result['data']['baskets'][1]['id']);
        $I->assertEquals('_test_basket_item_1', $result['data']['baskets'][1]['items'][0]['id']);
        $I->assertEquals(10, $result['data']['baskets'][1]['items'][0]['product']['price']['price']);

        //check what the logged in basket owner will see
        $I->login(self::USERNAME, self::PASSWORD);

        $query = 'query {
                    basket (basketId: "_test_basket_public") {
                        items {
                            product {
                                id
                                price {
                                    price
                                }
                            }
                        }
                    }
                }';

        $I->sendGQLQuery($query);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(6.66, $result['data']['basket']['items'][0]['product']['price']['price']);
    }

    public function testPublicBasketsItemPriceForLoggedInSameUser(AcceptanceTester $I): void
    {
        $I->wantToTest('that for my own public baskets my group A prices are displayed');

        $this->assignSpecialPricesGroup($I);

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                baskets(owner: "' . self::USERNAME . '") {
                    id
                    items {
                        id
                        product {
                            price {
                                price
                            }
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('_test_basket_public', $result['data']['baskets'][1]['id']);
        $I->assertEquals('_test_basket_item_1', $result['data']['baskets'][1]['items'][0]['id']);
        $I->assertEquals(6.66, $result['data']['baskets'][1]['items'][0]['product']['price']['price']);
    }

    public function testPublicBasketsItemPriceForLoggedInOtherUser(AcceptanceTester $I): void
    {
        $I->wantToTest('that for a public basket (not my own) my group b prices are displayed');

        $this->assignSpecialPricesGroup($I);

        $I->login(self::OTHER_USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                baskets(owner: "' . self::USERNAME . '") {
                    id
                    items {
                        id
                        product {
                            price {
                                price
                            }
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('_test_basket_public', $result['data']['baskets'][1]['id']);
        $I->assertEquals('_test_basket_item_1', $result['data']['baskets'][1]['items'][0]['id']);
        $I->assertEquals(8.88, $result['data']['baskets'][1]['items'][0]['product']['price']['price']);
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

    private function assignSpecialPricesGroup(AcceptanceTester $I): void
    {
        $I->haveInDatabase(
            'oxobject2group',
            [
                'OXID'       => '_testrelationA',
                'OXOBJECTID' => self::CUSTOMER_ID,
                'OXGROUPSID' => 'oxidpricea',
            ]
        );

        $I->haveInDatabase(
            'oxobject2group',
            [
                'OXID'       => '_testrelationB',
                'OXOBJECTID' => self::OTHER_CUSTOMER_ID,
                'OXGROUPSID' => 'oxidpriceb',
            ]
        );

        $I->updateInDatabase('oxarticles', ['OXPRICEA' => '6.66'], ['oxid' => '_test_product_for_basket']);
        $I->updateInDatabase('oxarticles', ['OXPRICEB' => '8.88'], ['oxid' => '_test_product_for_basket']);
    }
}
