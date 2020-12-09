<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\WishedPrice;

use Codeception\Example;
use Codeception\Scenario;
use Codeception\Util\HttpCode;
use OxidEsales\Eshop\Application\Model\PriceAlarm;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group wishedprice
 */
final class WishedPriceMultiShopCest extends MultishopBaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const WISHED_PRICE_SHOP_1 = '_test_wished_price_1_';

    private const WISHED_PRICE_SHOP_2 = '_test_wished_price_8_';

    private const WISHED_PRICE_TO_BE_DELETED = '_test_wished_price_delete_';

    private const PRODUCT_ID_SHOP_1 = '_test_product_wp1_';

    private const PRODUCT_ID_SHOP_2 = '_test_product_wp2_';

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $I->updateInDatabase(
            'oxshops',
            [
                'oxorderemail' => 'reply@myoxideshop.com',
            ],
            [
                'oxid' => 2,
            ]
        );
    }

    /**
     * @dataProvider dataProviderWishedPricePerShop
     */
    public function testUserWishedPricePerShop(AcceptanceTester $I, Example $data): void
    {
        $languageId = 0;
        $shopId     = $data['shopId'];

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'query{
                wishedPrice(id: "' . $data['wishedPriceId'] . '") {
                    id
                }
            }',
            [],
            $languageId,
            $shopId
        );

        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @dataProvider dataProviderWishedPricePerShop
     */
    public function testAdminWishedPricePerShop(AcceptanceTester $I, Example $data): void
    {
        $languageId = 0;
        $shopId     = $data['shopId'];

        $I->login('admin', 'admin', $shopId);

        $I->sendGQLQuery(
            'query{
                wishedPrice(id: "' . $data['wishedPriceId'] . '") {
                    id
                }
            }',
            [],
            $languageId,
            $shopId
        );

        $I->seeResponseCodeIs(HttpCode::OK);
    }

    public function dataProviderWishedPricePerShop()
    {
        return [
            [
                'shopId'        => 1,
                'wishedPriceId' => self::WISHED_PRICE_SHOP_1,
            ],
            [
                'shopId'        => 2,
                'wishedPriceId' => self::WISHED_PRICE_SHOP_2,
            ],
        ];
    }

    public function testGetUserWishedPriceFromShop1ToShop2(AcceptanceTester $I): void
    {
        $languageId = 0;
        $shopId     = 2;

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'query{
                wishedPrice(id: "' . self::WISHED_PRICE_SHOP_1 . '") {
                    id
                }
            }',
            [],
            $languageId,
            $shopId
        );

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    public function testDeleteShop1WishedPriceFromShop2(AcceptanceTester $I): void
    {
        $languageId = 0;
        $shopId     = 2;

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'mutation {
                wishedPriceDelete(id: "' . self::WISHED_PRICE_TO_BE_DELETED . '")
            }',
            [],
            $languageId,
            $shopId
        );

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    /**
     * @dataProvider wishedPriceSetPerShopDataProvider
     */
    public function testWishedPriceSetPerShop(AcceptanceTester $I, Example $data): void
    {
        $shopId     = $data['shopId'];
        $languageId = 0;

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'mutation {
                wishedPriceSet(wishedPrice: {
                    productId: "' . $data['productId'] . '",
                    currencyName: "EUR",
                    price: 15.00
                }) {
                    id
                }
            }',
            [],
            $languageId,
            $shopId
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();

        $wishedPrice = oxNew(PriceAlarm::class);
        $wishedPrice->load($result['data']['wishedPriceSet']['id']);

        $I->assertTrue($wishedPrice->isLoaded());
        $I->assertEquals($shopId, $wishedPrice->getShopId());
    }

    public function wishedPriceSetPerShopDataProvider(): array
    {
        return [
            [
                'shopId'    => 1,
                'productId' => self::PRODUCT_ID_SHOP_1,
            ],
            [
                'shopId'    => 2,
                'productId' => self::PRODUCT_ID_SHOP_2,
            ],
        ];
    }

    public function testWishedPriceProductExistsInOtherShopOnly(AcceptanceTester $I): void
    {
        $shopId     = 2;
        $languageId = 0;

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'mutation {
                wishedPriceSet(wishedPrice: {
                    productId: "' . self::PRODUCT_ID_SHOP_1 . '",
                    currencyName: "EUR",
                    price: 11.00
                }) {
                    id
                }
            }',
            [],
            $languageId,
            $shopId
        );

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }
}
