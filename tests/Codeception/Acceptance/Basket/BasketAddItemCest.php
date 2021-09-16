<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use GraphQL\Validator\Rules\FieldsOnCorrectType;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

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
            'Cannot query field "basketAddItem" on type "Mutation".',
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

        $expectedMessage = FieldsOnCorrectType::undefinedFieldMessage('basketAddItem', 'Mutation', [], []);
        $I->assertEquals($expectedMessage, $result['errors'][0]['message']);
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
            ],
            [
                'oxid' => self::PRODUCT_ID,
            ]
        );

        $result = $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID, 200);

        $basketData = $result['data']['basketAddItem'];
        $I->assertSame(self::PUBLIC_BASKET, $basketData['id']);
        $I->assertSame('Availability of this item is limited to 15', $result['errors'][0]['message']);
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

        $this->basketAddItemMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID, 0);
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
