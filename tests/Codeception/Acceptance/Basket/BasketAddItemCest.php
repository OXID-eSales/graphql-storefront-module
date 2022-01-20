<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketAccessForbidden;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketItemAmountLimitedStock;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketNotFound;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;
use TheCodingMachine\GraphQLite\Middlewares\MissingAuthorizationException as MissingAuthorizationExceptionAlias;

/**
 * @group basket
 * @group oe_graphql_storefront
 */
final class BasketAddItemCest extends BasketBaseCest
{
    // Public basket
    private const PUBLIC_BASKET = '_test_basket_public';

    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const PRODUCT = '_test_product_for_basket';

    private const PRODUCT_ID = 'dc5ffdf380e15674b56dd562a7cb6aec';

    private const PRIVATE_BASKET = '_test_basket_private';

    private const PRODUCT_FOR_PRIVATE_BASKET = '_test_product_for_wish_list';

    private const BASKET_TITLE = 'new_test_basket';

    public function _after(AcceptanceTester $I): void
    {
        $I->deleteFromDatabase(
            'oxuserbasketitems',
            [
                'OXARTID'    => self::PRODUCT_ID,
                'OXBASKETID' => self::PUBLIC_BASKET,
            ]
        );

        $I->deleteFromDatabase(
            'oxuserbaskets',
            [
                'OXTITLE' => self::BASKET_TITLE,
            ]
        );
    }

    public function testAddItemToBasketNoToken(AcceptanceTester $I): void
    {
        $result = $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID);

        $I->assertSame(
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

        $result = $this->basketAddItemMutation($I, 'non_existing_basket_id', self::PRODUCT_ID);

        $I->assertSame(
            BasketNotFound::byId('non_existing_basket_id')->getMessage(),
            $result['errors'][0]['message']
        );
    }

    public function testAddItemToBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID, 2);

        $basket = $result['data']['basketAddItem'];
        $I->assertSame(self::PUBLIC_BASKET, $basket['id']);
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
        ], $basket['items']);
        $I->assertNotNull($basket['lastUpdateDate']);

        $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID, 0);
    }

    public function testAddItemToBasketWithQuantityBiggerThenStock(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $this->updateStock($I, 15, 3);

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
        $this->updateStock($I, 15, 1);
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
        $this->updateStock($I, 15, 3);

        $result = $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID);

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
        $this->updateStock($I, 15, 1);
    }

    /** @group oe_graphql_storefront_basket_limited_stock */
    public function testAddMoreItemsWithLimitedStock(AcceptanceTester $I): void
    {
        $I->wantToTest('adding limited stock product step by step');

        $initialAmount = 3;
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId   = $this->basketCreateMutation($I, self::BASKET_TITLE)['id'];
        $basketItem = $this->basketAddItemMutation($I, $basketId, self::PRODUCT_ID, $initialAmount)['data']['basketAddItem']['items'][0];
        $I->assertEquals($initialAmount, $basketItem['amount']);

        //there's one more item in stock we can add to basket
        $this->updateStock($I, $initialAmount + 1, 3);

        //query the basket, up to now all is well
        $basket = $this->basketQuery($I, $basketId);
        $I->assertFalse(isset($result['errors']));
        $I->assertEquals($initialAmount, $basket['items'][0]['amount']);

        //try to add more of the main items, but only one more can be added
        $addAmount       = 10;
        $result          = $this->basketAddItemMutation($I, $basketId, self::PRODUCT_ID, $addAmount);
        $expectedMessage = BasketItemAmountLimitedStock::limitedAvailability(self::PRODUCT_ID, $initialAmount + 1);
        $I->assertStringStartsWith($expectedMessage, $result['errors'][0]['message']);

        //check the basket, we now should have $initialAmount + 1
        $basket = $this->basketQuery($I, $basketId);
        $I->assertEquals($initialAmount + 1, $basket['items'][0]['amount']);
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

        $this->updateStock($I, 0, 3);

        $result = $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID);

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
        $this->updateStock($I, 15, 1);
    }

    public function addItemToBasketWithQuantityEqualToStock(AcceptanceTester $I): void
    {
        $this->updateStock($I, 2, 3);
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreateMutation($I, 'test-stock-basket')['id'];
        $result   = $this->basketAddItemMutation($I, $basketId, self::PRODUCT_ID, 2);

        $I->assertArrayNotHasKey('errors', $result);
        $I->assertEquals(2, $result['data']['basketAddItem']['items'][0]['amount']);
    }

    public function testAddNonExistingItemToBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);
        $result = $this->basketAddItemMutation($I, self::PUBLIC_BASKET, 'non_existing_product');

        $I->assertSame(
            ProductNotFound::byId('non_existing_product')->getMessage(),
            $result['errors'][0]['message']
        );
    }

    public function testAddItemToSomeoneElseBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);
        $result = $this->basketAddItemMutation($I, self::PRIVATE_BASKET, self::PRODUCT_FOR_PRIVATE_BASKET);

        $I->assertSame(
            BasketAccessForbidden::byAuthenticatedUser()->getMessage(),
            $result['errors'][0]['message']
        );
    }

    private function updateStock(AcceptanceTester $I, int $stock, int $flag): void
    {
        $I->updateInDatabase(
            'oxarticles',
            [
                'oxstockflag' => $flag,
                'oxstock'     => $stock,
            ],
            [
                'oxid' => self::PRODUCT_ID,
            ]
        );
    }
}
