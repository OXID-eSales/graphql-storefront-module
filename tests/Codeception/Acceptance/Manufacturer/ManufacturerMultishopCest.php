<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Manufacturer;

use Codeception\Example;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group manufacturer
 * @group oe_graphql_storefront
 * @group other
 */
final class ManufacturerMultishopCest extends MultishopBaseCest
{
    private const MANUFACTURER_ID = 'adc6df0977329923a6330cc8f3c0a906';

    private const MANUFACTURER_MAP_ID = '909';

    private const MANUFACTURER_WITH_SINGLE_PRODUCT_MAP_ID = '914';

    private const PRODUCT_RELATED_TO_MANUFACTURER = 'b56164c54701f07df14b141da197c207';

    private const PRODUCT_RELATED_TO_MANUFACTURER_MAP_ID = '1109';

    public function testGetSingleNotInShopActiveManufacturerWillFail(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                manufacturer (manufacturerId: "' . self::MANUFACTURER_ID . '") {
                    id
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Manufacturer was not found by id: ' . self::MANUFACTURER_ID,
            $response['errors'][0]['message']
        );
    }

    public function testGetEmptyManufacturerListOfNotMainShop(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query{
            manufacturers {
                id
            }
        }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertCount(
            0,
            $response['data']['manufacturers']
        );
    }

    public function testGetSingleInShopActiveManufacturerWillWork(AcceptanceTester $I): void
    {
        $this->addManufacturerToShop($I, 2, self::MANUFACTURER_MAP_ID);
        $I->sendGQLQuery(
            'query {
            manufacturer (manufacturerId: "' . self::MANUFACTURER_ID . '") {
                id,
                title
            }
        }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'id' => self::MANUFACTURER_ID,
                'title' => 'Liquid Force',
            ],
            $response['data']['manufacturer']
        );

        $this->removeManufacturerFromShop($I, 2);
    }

    public function testGetOneManufacturerInListOfNotMainShop(AcceptanceTester $I): void
    {
        $this->addManufacturerToShop($I, 2, self::MANUFACTURER_MAP_ID);

        $I->sendGQLQuery(
            'query{
            manufacturers {
                id
            }
        }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertCount(
            1,
            $response['data']['manufacturers']
        );
        $this->removeManufacturerFromShop($I, 2);
    }

    /**
     * @dataProvider providerGetManufacturerMultilanguage
     */
    public function testGetSingleTranslatedSecondShopManufacturer(AcceptanceTester $I, Example $data): void
    {
        $this->addManufacturerToShop($I, 2, self::MANUFACTURER_MAP_ID);

        $I->sendGQLQuery(
            'query {
            manufacturer (manufacturerId: "' . self::MANUFACTURER_ID . '") {
                id
                title
            }
        }',
            null,
            $data['languageId'],
            $data['shopId']
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'id' => self::MANUFACTURER_ID,
                'title' => $data['title'],
            ],
            $response['data']['manufacturer']
        );
        $this->removeManufacturerFromShop($I, 2);
    }

    /**
     * @dataProvider providerGetManufacturerMultilanguage
     */
    public function testGetListTranslatedSecondShopManufacturers(AcceptanceTester $I, Example $data): void
    {
        $this->addManufacturerToShop($I, 2, self::MANUFACTURER_MAP_ID);

        $I->sendGQLQuery(
            'query {
                manufacturers(filter: {
                    title: {
                        equals: "' . $data['title'] . '"
                    }
            }) {
                id,
                title
            }
        }',
            null,
            $data['languageId'],
            $data['shopId']
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'id' => self::MANUFACTURER_ID,
                'title' => $data['title'],
            ],
            $response['data']['manufacturers'][0]
        );
        $this->removeManufacturerFromShop($I, 2);
    }

    protected function providerGetManufacturerMultilanguage(): array
    {
        return [
            'shop_1_de' => [
                'shopId' => 1,
                'languageId' => 0,
                'title' => 'Liquid Force',
            ],
            'shop_1_en' => [
                'shopId' => 1,
                'languageId' => 1,
                'title' => 'Liquid Force Kite',
            ],
            'shop_2_de' => [
                'shopId' => 2,
                'languageId' => 0,
                'title' => 'Liquid Force',
            ],
            'shop_2_en' => [
                'shopId' => 2,
                'languageId' => 1,
                'title' => 'Liquid Force Kite',
            ],
        ];
    }

    public function testGetActiveProduct(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                manufacturer (manufacturerId: "' . self::MANUFACTURER_ID . '") {
                    products
                    {
                        id
                        title
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        //fixtures have 7 active products assigned to manufacturer in shop 1
        $I->assertEquals(7, count($response['data']['manufacturer']['products']));
    }

    public function testGetProductFromSecondShop(AcceptanceTester $I): void
    {
        $this->addManufacturerToShop($I, 2, self::MANUFACTURER_WITH_SINGLE_PRODUCT_MAP_ID);
        $this->addProductToShop($I, 2);

        $I->sendGQLQuery(
            'query {
                manufacturer (manufacturerId: "' . self::MANUFACTURER_ID . '") {
                    products
                    {
                        id
                        title
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        //fixtures have 7 active products assigned to manufacturer in shop 1
        $I->assertEquals(
            self::PRODUCT_RELATED_TO_MANUFACTURER,
            $response['data']['manufacturer']['products'][0]['id']
        );

        $this->removeManufacturerFromShop($I, 2);
        $this->removeProductFromShop($I, 2);
    }

    public function testProductIsNotFetchedFromFirstShop(AcceptanceTester $I): void
    {
        $this->addManufacturerToShop($I, 2, self::MANUFACTURER_MAP_ID);

        $I->sendGQLQuery(
            'query {
                manufacturer (manufacturerId: "' . self::MANUFACTURER_ID . '") {
                    id
                    products
                    {
                        id
                    }
                }
            }'
        );

        $this->removeManufacturerFromShop($I, 2);
    }

    private function addManufacturerToShop(AcceptanceTester $I, int $shopId, string $manufacturerId): void
    {
        $I->haveInDatabase(
            'oxmanufacturers2shop',
            [
                'oxshopid' => $shopId,
                'oxmapobjectid' => $manufacturerId,
            ]
        );
    }

    private function removeManufacturerFromShop(AcceptanceTester $I, int $shopId): void
    {
        $I->deleteFromDatabase(
            'oxmanufacturers2shop',
            [
                'oxshopid' => $shopId,
                'oxmapobjectid' => self::MANUFACTURER_MAP_ID,
            ]
        );
        $I->deleteFromDatabase(
            'oxmanufacturers2shop',
            [
                'oxshopid' => $shopId,
                'oxmapobjectid' => self::MANUFACTURER_WITH_SINGLE_PRODUCT_MAP_ID,
            ]
        );
    }

    private function addProductToShop(AcceptanceTester $I, int $shopId): void
    {
        $I->haveInDatabase(
            'oxarticles2shop',
            [
                'oxshopid' => $shopId,
                'oxmapobjectid' => self::PRODUCT_RELATED_TO_MANUFACTURER_MAP_ID,
            ]
        );
    }

    private function removeProductFromShop(AcceptanceTester $I, int $shopId): void
    {
        $I->deleteFromDatabase(
            'oxarticles2shop',
            [
                'oxshopid' => $shopId,
                'oxmapobjectid' => self::PRODUCT_RELATED_TO_MANUFACTURER_MAP_ID,
            ]
        );
    }
}
