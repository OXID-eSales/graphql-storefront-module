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
final class BasketAddProductCest extends BaseCest
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

    public function testAddProductToBasketNoToken(AcceptanceTester $I): void
    {
        $this->basketAddProductMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @group allowed_to_fail_for_anonymous_token
     */
    public function testAddProductToBasketWithAnonymousUser(AcceptanceTester $I): void
    {
        $I->login();

        $result = $this->basketAddProductMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $expectedMessage = FieldsOnCorrectType::undefinedFieldMessage('basketAddProduct', 'Mutation', [], []);
        $I->assertEquals($expectedMessage, $result['errors'][0]['message']);
    }

    public function testAddProductToBasketWrongBasketId(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $this->basketAddProductMutation($I, 'non_existing_basket_id', self::PRODUCT_ID);

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    public function testAddProductToBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->basketAddProductMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID, 2);

        $I->seeResponseCodeIs(HttpCode::OK);

        $basketData = $result['data']['basketAddProduct'];
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

        $this->basketAddProductMutation($I, self::PUBLIC_BASKET, self::PRODUCT_ID, 0);
    }

    public function testAddNonExistingProductToBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $this->basketAddProductMutation($I, self::PUBLIC_BASKET, 'non_existing_product');

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    public function testAddProductToSomeoneElseBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);
        $this->basketAddProductMutation($I, self::PRIVATE_BASKET, self::PRODUCT_FOR_PRIVATE_BASKET);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    private function basketAddProductMutation(AcceptanceTester $I, string $basketId, string $productId, int $amount = 1): array
    {
        $I->sendGQLQuery('
            mutation {
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
                    lastUpdateDate
                }
            }
        ');

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
