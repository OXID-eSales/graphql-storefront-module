<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;
use TheCodingMachine\GraphQLite\Middlewares\MissingAuthorizationException as MissingAuthorizationExceptionAlias;

/**
 * @group basket
 * @group oe_graphql_storefront
 */
final class BasketAddItemCest extends BaseCest
{
    // Public basket
    private const PUBLIC_BASKET = '_test_basket_public';

    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const PRODUCT = '_test_product_for_basket';

    private const PRODUCT_ID = 'dc5ffdf380e15674b56dd562a7cb6aec';

    private const PRIVATE_BASKET = '_test_basket_private';

    private const PRODUCT_FOR_PRIVATE_BASKET = '_test_product_for_wish_list';

    public function _after(AcceptanceTester $I): void
    {
        $I->deleteFromDatabase(
            'oxuserbasketitems',
            [
                'OXARTID'    => self::PRODUCT_ID,
                'OXBASKETID' => self::PUBLIC_BASKET,
            ]
        );
    }

    public function testAddItemToBasketNoToken(AcceptanceTester $I): void
    {
        $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertStringStartsWith(
            MissingAuthorizationExceptionAlias::forbidden()->getMessage(),
            $result['errors'][0]['message']
        );
    }

    /**
     * @group allowed_to_fail_for_anonymous_token
     */
    public function testAddItemToBasketWithAnonymousUser(AcceptanceTester $I): void
    {
        $I->login();

        $result = $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID);

        $I->assertEquals(
            MissingAuthorizationExceptionAlias::forbidden()->getMessage(),
            $result['errors'][0]['message']
        );
    }

    public function testAddItemToBasketWrongBasketId(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $this->basketAddItemMutation($I, 'non_existing_basket_id', self::PRODUCT_ID);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Basket was not found by id: non_existing_basket_id',
            $result['errors'][0]['message']
        );
    }

    public function testAddItemToBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID, 2);

        $basketData = $result['data']['basketAddItem'];
        $I->assertSame(self::PUBLIC_BASKET, $basketData['id']);
        $I->assertSame([
            [
                'product' => [
                    'id' => self::PRODUCT_ID,
                ],
                'amount' => 2,
            ], [
                'product' => [
                    'id' => self::PRODUCT,
                ],
                'amount' => 1,
            ],
        ], $basketData['items']);
        $I->assertNotNull($basketData['lastUpdateDate']);

        $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID, 0);
    }

    public function testAddItemToBasketWithQuantityBiggerThenStock(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->updateInDatabase(
            'oxarticles',
            [
                'oxstockflag' => 3,
                'oxstock'     => 15,
            ],
            [
                'oxid' => self::PRODUCT_ID,
            ]
        );

        $result = $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID, 200);

        $basketData = $result['data']['basketAddItem'];
        $I->assertSame(self::PUBLIC_BASKET, $basketData['id']);
        $I->assertSame('Not enough items of product with id ' . self::PRODUCT_ID . ' in stock! Available: 15', $result['errors'][0]['message']);
        $I->assertSame('LIMITEDAVAILABILITY', $result['errors'][0]['extensions']['type']);

        $I->assertSame([
            [
                'product' => [
                    'id' => self::PRODUCT_ID,
                ],
                'amount' => 15,
            ], [
                'product' => [
                    'id' => self::PRODUCT,
                ],
                'amount' => 1,
            ],
        ], $basketData['items']);
        $I->assertNotNull($basketData['lastUpdateDate']);

        // reset article
        $I->updateInDatabase(
            'oxarticles',
            [
                'oxstockflag' => 1,
            ],
            [
                'oxid' => self::PRODUCT_ID,
            ]
        );
    }

    public function testAddItemToBasketWhenCurrentAmountIsBiggerThanStock(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result     = $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID, 20);
        $basketData = $result['data']['basketAddItem'];
        $I->assertSame([
            [
                'product' => [
                    'id' => self::PRODUCT_ID,
                ],
                'amount' => 20,
            ], [
                'product' => [
                    'id' => self::PRODUCT,
                ],
                'amount' => 1,
            ],
        ], $basketData['items']);

        // update stock flag to "If out of stock, not orderable"
        $I->updateInDatabase(
            'oxarticles',
            [
                'oxstockflag' => 3,
                'oxstock'     => 15,
            ],
            [
                'oxid' => self::PRODUCT_ID,
            ]
        );

        $result = $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID, 1);

        $basketData = $result['data']['basketAddItem'];
        $I->assertSame(self::PUBLIC_BASKET, $basketData['id']);
        $I->assertSame('Not enough items of product with id ' . self::PRODUCT_ID . ' in stock! Available: 15', $result['errors'][0]['message']);
        $I->assertSame('LIMITEDAVAILABILITY', $result['errors'][0]['extensions']['type']);

        $I->assertSame([
            [
                'product' => [
                    'id' => self::PRODUCT_ID,
                ],
                'amount' => 15,
            ], [
                'product' => [
                    'id' => self::PRODUCT,
                ],
                'amount' => 1,
            ],
        ], $basketData['items']);
        $I->assertNotNull($basketData['lastUpdateDate']);

        // reset article
        $I->updateInDatabase(
            'oxarticles',
            [
                'oxstockflag' => 1,
            ],
            [
                'oxid' => self::PRODUCT_ID,
            ]
        );
    }

    /** @group oe_graphql_storefront_basket_limited_stock */
    public function testAddMoreItemsWithLimitedStock(AcceptanceTester $I): void
    {
        $I->wantToTest('adding limited stock product step by step');

        $initialAmount = 3;
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId   = $this->basketCreateMutation($I);
        $basketItem = $this->basketAddItemMutation($I, $basketId, self::PRODUCT_ID, $initialAmount)['data']['basketAddItem']['items'][0];
        $I->assertEquals($initialAmount, $basketItem['amount']);

        //there's one more item in stock we can add to basket
        $I->updateInDatabase(
            'oxarticles',
            [
                'oxstockflag' => 3,
                'oxstock'     => $initialAmount + 1,
            ],
            [
                'oxid' => self::PRODUCT_ID,
            ]
        );

        //query the basket, up to now all is well
        $result = $this->basketQuery($I, $basketId);
        $I->assertFalse(isset($result['errors']));
        $I->assertEquals($initialAmount, $result['data']['basket']['items'][0]['amount']);

        //try to add more of the main items, but only one more can be added
        $addAmount = 10;
        $result    = $this->basketAddItemMutation($I, $basketId, self::PRODUCT_ID, $addAmount);
        $I->assertStringStartsWith('Not enough items of product with id ' . self::PRODUCT_ID . ' in stock', $result['errors'][0]['message']);

        //check the basket, we now should have $initialAmount + 1
        $result = $this->basketQuery($I, $basketId);
        $I->assertEquals($initialAmount + 1, $result['data']['basket']['items'][0]['amount']);
    }

    public function testAutomaticallyRemoveOutOfStockItemFromBasketOnAdd(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result     = $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID, 2);
        $basketData = $result['data']['basketAddItem'];
        $I->assertSame([
            [
                'product' => [
                    'id' => self::PRODUCT_ID,
                ],
                'amount' => 2,
            ], [
                'product' => [
                    'id' => self::PRODUCT,
                ],
                'amount' => 1,
            ],
        ], $basketData['items']);

        $I->updateInDatabase(
            'oxarticles',
            [
                'oxstockflag' => 3,
                'oxstock'     => 0,
            ],
            [
                'oxid' => self::PRODUCT_ID,
            ]
        );

        $result = $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID, 1);

        $basketData = $result['data']['basketAddItem'];
        $I->assertSame(self::PUBLIC_BASKET, $basketData['id']);
        $I->assertSame('Product with id ' . self::PRODUCT_ID . ' is out of stock', $result['errors'][0]['message']);
        $I->assertSame([
            [
                'product' => [
                    'id' => self::PRODUCT,
                ],
                'amount' => 1,
            ],
        ], $basketData['items']);
        $I->assertNotNull($basketData['lastUpdateDate']);

        // reset article
        $I->updateInDatabase(
            'oxarticles',
            [
                'oxstockflag' => 1,
                'oxstock'     => 15,
            ],
            [
                'oxid' => self::PRODUCT_ID,
            ]
        );
    }

    public function testAddNonExistingItemToBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $this->basketAddItemMutation($I, self::PUBLIC_BASKET, 'non_existing_product');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Product was not found by id: non_existing_product',
            $result['errors'][0]['message']
        );
    }

    public function testAddItemToSomeoneElseBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);
        $this->basketAddItemMutation($I, self::PRIVATE_BASKET, self::PRODUCT_FOR_PRIVATE_BASKET);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'You are not allowed to access this basket as it belongs to somebody else',
            $result['errors'][0]['message']
        );
    }

    protected function basketCreateMutation(
        AcceptanceTester $I,
        string $title = 'new_test_basket'
    ): string {
        $mutation = '
            mutation ($title: String!) {
                basketCreate(basket: {title: $title}){
                    id
                }
            }
        ';

        $variables = [
            'title' => $title,
        ];

        $I->sendGQLQuery($mutation, $variables);
        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray()['data']['basketCreate']['id'];
    }

    protected function basketQuery(
        AcceptanceTester $I,
        string $basketId
    ): array {
        $query = '
            query ($basketId: ID!) {
                basket(basketId: $basketId) {
                    id
                    items {
                        amount
                        id
                        product {
                            id
                        }
                    }
                }
            }
        ';

        $variables = [
            'basketId' => $basketId,
        ];

        $I->sendGQLQuery($query, $variables);
        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function basketAddItemMutation(AcceptanceTester $I, string $basketId, string $productId, int $amount = 1): array
    {
        $I->sendGQLQuery('
            mutation {
                basketAddItem(
                    basketId: "' . $basketId . '",
                    productId: "' . $productId . '",
                    amount: ' . $amount . '
                ) {
                    id
                    items {
                        product {
                            id
                        }
                        amount
                    }
                    lastUpdateDate
                }
            }
        ');

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
