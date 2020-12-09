<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use Codeception\Example;
use Codeception\Util\HttpCode;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group basket
 */
final class BasketAddProductMultishopCest extends MultishopBaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const OTHER_PASSWORD = 'useruser';

    private const PUBLIC_BASKET = '_test_basket_public'; //owned by shop1 user

    private const PRIVATE_BASKET = '_test_basket_private'; //owned by otheruser

    private const SHOP_1_PRODUCT_ID = '_test_product_wished_price_3_';

    private const SHOP_2_PRODUCT_ID = '_test_product_5_';

    public function _after(AcceptanceTester $I): void
    {
        $I->logout();

        $I->deleteFromDatabase(
            'oxuserbasketitems',
            [
                'OXARTID'    => self::SHOP_1_PRODUCT_ID,
            ]
        );

        $I->deleteFromDatabase(
            'oxuserbasketitems',
            [
                'OXARTID'    => self::SHOP_2_PRODUCT_ID,
            ]
        );
    }

    /**
     * @dataProvider dataProviderAddProductToBasketPerShop
     */
    public function testAddProductToBasketPerShop(AcceptanceTester $I, Example $data): void
    {
        $shopId    = $data['shopId'];
        $basketId  = $data['basketId'];
        $productId = $data['productId'];

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'mutation {
                 basketAddProduct(
                    basketId: "' . $basketId . '"
                    productId: "' . $productId . '"
                    amount: 2
                 ) {
                    id
                    items {
                        product {
                            id
                        }
                        amount
                    }
                }
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            [
                'id'    => $basketId,
                'items' => [
                    [
                        'product' => [
                            'id' => $productId,
                        ],
                        'amount' => 2,
                    ], [
                        'product' => [
                            'id' => '_test_product_for_basket',
                        ],
                        'amount' => 1,
                    ],
                ],
            ],
            $result['data']['basketAddProduct']
        );
    }

    public function testAddProductToBasketFromOtherSubshop(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD, 2);

        $I->sendGQLQuery(
            'mutation {
                 basketAddProduct(
                    basketId: "' . self:: PRIVATE_BASKET . '"
                    productId: "' . self::SHOP_2_PRODUCT_ID . '"
                    amount: 2
                 ) {
                    id
                    items {
                        product {
                            id
                        }
                        amount
                    }
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            [
                'id'    => self:: PRIVATE_BASKET,
                'items' => [
                    [
                        'product' => [
                            'id' => self::SHOP_2_PRODUCT_ID,
                        ],
                        'amount' => 2,
                    ], [
                        'product' => [
                            'id' => '_test_product_for_basket',
                        ],
                        'amount' => 1,
                    ],
                ],
            ],
            $result['data']['basketAddProduct']
        );
    }

    protected function dataProviderAddProductToBasketPerShop()
    {
        return [
            'shop_1' => [
                'shopId'    => 1,
                'basketId'  => self::PUBLIC_BASKET,
                'productId' => self::SHOP_1_PRODUCT_ID,
            ],
            'shop_2' => [
                'shopId'    => 2,
                'basketId'  => '_test_shop2_basket_public',
                'productId' => self::SHOP_2_PRODUCT_ID,
            ],
        ];
    }
}
