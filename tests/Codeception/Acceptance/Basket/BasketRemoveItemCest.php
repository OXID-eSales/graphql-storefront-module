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
final class BasketRemoveItemCest extends BaseCest
{
    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const OTHER_PASSWORD = 'useruser';

    private const BASKET_ID = '_test_basket_private';

    private const PRODUCT_ID = 'dc5ffdf380e15674b56dd562a7cb6aec';

    private const PRODUCT_ID_1 = '_test_product_for_rating_avg';

    private const PRODUCT_ID_2 = '_test_product_for_basket';

    private const BASKET_ITEM_ID_2 = '_test_basket_item_2';

    public function testRemoveBasketItemWithoutToken(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);
        $items = $this->basketAddItemMutation($I, self::BASKET_ID, self::PRODUCT_ID);

        $basketItemId = null;

        foreach ($items as $item) {
            if (self::PRODUCT_ID === $item['product']['id']) {
                $basketItemId = $item['id'];
            }
        }

        $I->logout();
        $this->basketRemoveItemMutation($I, self::BASKET_ID, $basketItemId);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'You do not have sufficient rights to access this field',
            $result['errors'][0]['message']
        );
    }

    /**
     * @group allowed_to_fail_for_anonymous_token
     */
    public function testRemoveBasketItemAnonymousUser(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);
        $items = $this->basketAddItemMutation($I, self::BASKET_ID, self::PRODUCT_ID);

        $basketItemId = null;

        foreach ($items as $item) {
            if (self::PRODUCT_ID === $item['product']['id']) {
                $basketItemId = $item['id'];
            }
        }

        $I->logout();
        $I->login();
        $result = $this->basketRemoveItemMutation($I, self::BASKET_ID, $basketItemId);

        $I->assertEquals('You do not have sufficient rights to access this field', $result['errors'][0]['message']);
    }

    public function testRemoveBasketItemUsingDifferentUser(AcceptanceTester $I): void
    {
        $I->login('admin', 'admin');

        $this->basketRemoveItemMutation($I, self::BASKET_ID, self::BASKET_ITEM_ID_2);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'You are not allowed to access this basket as it belongs to somebody else',
            $result['errors'][0]['message']
        );
    }

    public function testRemoveBasketItem(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);
        $basketItems = $this->basketAddItemMutation($I, self::BASKET_ID, self::PRODUCT_ID);

        $basketItemId = null;

        foreach ($basketItems as $basketItem) {
            if (self::PRODUCT_ID === $basketItem['product']['id']) {
                $basketItemId = $basketItem['id'];
            }
        }

        $result = $this->basketRemoveItemMutation($I, self::BASKET_ID, $basketItemId);

        $items = $result['data']['basketRemoveItem']['items'];
        $I->assertSame(1, count($items));

        foreach ($items as $item) {
            $I->assertTrue(self::PRODUCT_ID !== $item['product']['id']);
        }
    }

    public function testDecreaseBasketProductAmount(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);
        $items = $this->basketAddItemMutation($I, self::BASKET_ID, self::PRODUCT_ID, 3);
        $I->assertSame(2, count($items));

        $basketItemId = null;

        foreach ($items as $item) {
            if (self::PRODUCT_ID === $item['product']['id']) {
                $I->assertSame(3, $item['amount']);
                $basketItemId = $item['id'];
            }
        }

        $result = $this->basketRemoveItemMutation($I, self::BASKET_ID, $basketItemId, 2);

        $items = $result['data']['basketRemoveItem']['items'];
        $I->assertSame(2, count($items));

        foreach ($items as $item) {
            if (self::PRODUCT_ID === $item['product']['id']) {
                $I->assertSame(1, $item['amount']);
            }
        }

        // clean up database
        $this->basketRemoveItemMutation($I, self::BASKET_ID, $basketItemId);
    }

    public function testRemoveWrongItemFromBasket(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);

        $this->basketRemoveItemMutation($I, self::BASKET_ID, '_test_basket_item_1');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Basket item with id _test_basket_item_1 not found in your basket ' . self::BASKET_ID,
            $result['errors'][0]['message']
        );
    }

    public function testRemoveNonExistingItemFromBasket(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);

        $this->basketRemoveItemMutation($I, self::BASKET_ID, 'not_a_basket_item');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Basket item with id not_a_basket_item not found in your basket ' . self::BASKET_ID,
            $result['errors'][0]['message']
        );
    }

    public function testRemoveAllItemsFromBasket(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);

        $result = $this->basketRemoveItemMutation($I, self::BASKET_ID, self::BASKET_ITEM_ID_2);

        $items = $result['data']['basketRemoveItem']['items'];
        $I->assertEmpty($items);
    }

    private function basketAddItemMutation(AcceptanceTester $I, string $basketId, string $productId, int $amount = 1): array
    {
        $I->sendGQLQuery(
            'mutation {
                basketAddItem(
                    basketId: "' . $basketId . '",
                    productId: "' . $productId . '",
                    amount: ' . $amount . '
                ) {
                    id
                    items {
                        id
                        product {
                            id
                        }
                        amount
                    }
                }
            }
        '
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        return $result['data']['basketAddItem']['items'];
    }

    private function basketRemoveItemMutation(AcceptanceTester $I, string $basketId, string $basketItemId, int $amount = 0): array
    {
        $I->sendGQLQuery(
            'mutation {
                basketRemoveItem(
                    basketId: "' . $basketId . '",
                    basketItemId: "' . $basketItemId . '",
                    amount: ' . $amount . '
                ) {
                    items(pagination: {limit: 10, offset: 0}) {
                        id
                        product {
                            id
                            title
                        }
                        amount
                    }
                    id
                }
            }'
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
