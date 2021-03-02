<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use Codeception\Util\HttpCode;
use GraphQL\Validator\Rules\FieldsOnCorrectType;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group basket
 */
final class BasketRemoveProductCest extends BaseCest
{
    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const OTHER_PASSWORD = 'useruser';

    private const BASKET_ID = '_test_basket_private';

    private const PRODUCT_ID = 'dc5ffdf380e15674b56dd562a7cb6aec';

    private const PRODUCT_ID_1 = '_test_product_for_rating_avg';

    private const PRODUCT_ID_2 = '_test_product_for_basket';

    public function testRemoveBasketProductWithoutToken(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);
        $this->basketAddProductMutation($I, self::BASKET_ID, self::PRODUCT_ID);

        $I->logout();
        $this->basketRemoveProductMutation($I, self::BASKET_ID, self::PRODUCT_ID);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function testRemoveBasketProductAnonymousUser(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);
        $this->basketAddProductMutation($I, self::BASKET_ID, self::PRODUCT_ID);

        $I->logout();
        $I->login();
        $result = $this->basketRemoveProductMutation($I, self::BASKET_ID, self::PRODUCT_ID);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $expectedMessage = FieldsOnCorrectType::undefinedFieldMessage('basketRemoveProduct', 'Mutation', [], []);
        $I->assertEquals($expectedMessage, $result['errors'][0]['message']);
    }

    public function testRemoveBasketProductUsingDifferentUser(AcceptanceTester $I): void
    {
        $I->login('admin', 'admin');

        $this->basketRemoveProductMutation($I, self::BASKET_ID, self::PRODUCT_ID_2);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    public function testRemoveBasketProduct(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);
        $this->basketAddProductMutation($I, self::BASKET_ID, self::PRODUCT_ID);

        $result = $this->basketRemoveProductMutation($I, self::BASKET_ID, self::PRODUCT_ID);
        $I->seeResponseCodeIs(HttpCode::OK);

        $items = $result['data']['basketRemoveProduct']['items'];
        $I->assertSame(1, count($items));

        foreach ($items as $item) {
            $I->assertTrue(self::PRODUCT_ID !== $item['product']['id']);
        }
    }

    public function testDecreaseBasketProductAmount(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);
        $result = $this->basketAddProductMutation($I, self::BASKET_ID, self::PRODUCT_ID, 3);
        $items  = $result['data']['basketAddProduct']['items'];
        $I->assertSame(2, count($items));

        foreach ($items as $item) {
            if (self::PRODUCT_ID === $item['product']['id']) {
                $I->assertSame(3, $item['amount']);
            }
        }

        $result = $this->basketRemoveProductMutation($I, self::BASKET_ID, self::PRODUCT_ID, 2);
        $I->seeResponseCodeIs(HttpCode::OK);

        $items = $result['data']['basketRemoveProduct']['items'];
        $I->assertSame(2, count($items));

        foreach ($items as $item) {
            if (self::PRODUCT_ID === $item['product']['id']) {
                $I->assertSame(1, $item['amount']);
            }
        }

        // clean up database
        $this->basketRemoveProductMutation($I, self::BASKET_ID, self::PRODUCT_ID);
    }

    public function testRemoveWrongProductFromBasket(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);
        $this->basketAddProductMutation($I, self::BASKET_ID, self::PRODUCT_ID_1);

        $this->basketRemoveProductMutation($I, self::BASKET_ID, self::PRODUCT_ID);
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);

        // clean up database
        $this->basketRemoveProductMutation($I, self::BASKET_ID, self::PRODUCT_ID_1);
    }

    public function testRemoveNonExistingProductFromBasket(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);

        $this->basketRemoveProductMutation($I, self::BASKET_ID, 'not_a_product');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    public function testRemoveAllProductsFromBasket(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);

        $result = $this->basketRemoveProductMutation($I, self::BASKET_ID, self::PRODUCT_ID_2);
        $I->seeResponseCodeIs(HttpCode::OK);

        $items = $result['data']['basketRemoveProduct']['items'];
        $I->assertEmpty($items);
    }

    private function basketAddProductMutation(AcceptanceTester $I, string $basketId, string $productId, int $amount = 1): array
    {
        $I->sendGQLQuery(
            'mutation {
                basketAddProduct(
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
                }
            }
        '
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function basketRemoveProductMutation(AcceptanceTester $I, string $basketId, string $productId, int $amount = 0): array
    {
        $I->sendGQLQuery(
            'mutation {
                basketRemoveProduct(
                    basketId: "' . $basketId . '",
                    productId: "' . $productId . '",
                    amount: ' . $amount . '
                ) {
                    items(pagination: {limit: 10, offset: 0}) {
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
