<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\WishedPrice;

use Codeception\Example;
use Codeception\Scenario;
use OxidEsales\Eshop\Application\Model\PriceAlarm;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group wishedprice
 * @group oe_graphql_storefront
 * @group pricing
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
        $shopId = $data['shopId'];

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'query{
                wishedPrice(wishedPriceId: "' . $data['wishedPriceId'] . '") {
                    id
                }
            }',
            [],
            $languageId,
            $shopId
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            $data['wishedPriceId'],
            $result['data']['wishedPrice']['id']
        );
    }

    /**
     * @dataProvider dataProviderWishedPricePerShop
     */
    public function testAdminWishedPricePerShop(AcceptanceTester $I, Example $data): void
    {
        $languageId = 0;
        $shopId = $data['shopId'];

        $I->login('admin', 'admin', $shopId);

        $I->sendGQLQuery(
            'query{
                wishedPrice(wishedPriceId: "' . $data['wishedPriceId'] . '") {
                    id
                }
            }',
            [],
            $languageId,
            $shopId
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            $data['wishedPriceId'],
            $result['data']['wishedPrice']['id']
        );
    }

    protected function dataProviderWishedPricePerShop()
    {
        return [
            [
                'shopId' => 1,
                'wishedPriceId' => self::WISHED_PRICE_SHOP_1,
            ],
            [
                'shopId' => 2,
                'wishedPriceId' => self::WISHED_PRICE_SHOP_2,
            ],
        ];
    }

    public function testGetUserWishedPriceFromShop1ToShop2(AcceptanceTester $I): void
    {
        $languageId = 0;
        $shopId = 2;

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'query{
                wishedPrice(wishedPriceId: "' . self::WISHED_PRICE_SHOP_1 . '") {
                    id
                }
            }',
            [],
            $languageId,
            $shopId
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Wished price was not found by id: ' . self::WISHED_PRICE_SHOP_1,
            $result['errors'][0]['message']
        );
    }

    public function testDeleteShop1WishedPriceFromShop2(AcceptanceTester $I): void
    {
        $languageId = 0;
        $shopId = 2;

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'mutation {
                wishedPriceDelete(wishedPriceId: "' . self::WISHED_PRICE_TO_BE_DELETED . '")
            }',
            [],
            $languageId,
            $shopId
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Wished price was not found by id: ' . self::WISHED_PRICE_TO_BE_DELETED,
            $result['errors'][0]['message']
        );
    }

    /**
     * @dataProvider wishedPriceSetPerShopDataProvider
     */
    public function testWishedPriceSetPerShop(AcceptanceTester $I, Example $data): void
    {
        $shopId = $data['shopId'];
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

        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();

        /** @var PriveAlarm */
        $wishedPrice = oxNew(PriceAlarm::class);
        $wishedPrice->load($result['data']['wishedPriceSet']['id']);

        $I->assertTrue($wishedPrice->isLoaded());
        $I->assertEquals($shopId, $wishedPrice->getShopId());
    }

    protected function wishedPriceSetPerShopDataProvider(): array
    {
        return [
            [
                'shopId' => 1,
                'productId' => self::PRODUCT_ID_SHOP_1,
            ],
            [
                'shopId' => 2,
                'productId' => self::PRODUCT_ID_SHOP_2,
            ],
        ];
    }

    public function testWishedPriceProductExistsInOtherShopOnly(AcceptanceTester $I): void
    {
        $shopId = 2;
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

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Product was not found by id: ' . self::PRODUCT_ID_SHOP_1,
            $result['errors'][0]['message']
        );
    }
}
