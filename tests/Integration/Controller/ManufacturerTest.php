<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Storefront\Tests\Integration\BaseTestCase;

final class ManufacturerTest extends BaseTestCase
{
    private const ACTIVE_MANUFACTURER = 'oiaf6ab7e12e86291e86dd3ff891fe40';

    private const ACTIVE_MANUFACTURER_WITHOUT_PRODUCTS = '3a909e7c886063857e86982c7a3c5b84';

    private const INACTIVE_MANUFACTURER = 'dc50589ad69b6ec71721b25bdd403171';

    private const ACTIVE_MULTILANGUAGE_MANUFACTURER = 'adc6df0977329923a6330cc8f3c0a906';

    private const PRODUCT_RELATED_TO_ACTIVE_MANUFACTURER = '058e613db53d782adfc9f2ccb43c45fe';

    public function setUp(): void
    {
        parent::setUp();

        $this->setGETRequestParameter(
            'lang',
            '0'
        );
    }

    public function testGetSingleActiveManufacturer(): void
    {
        $result = $this->query(
            'query {
                manufacturer (manufacturerId: "' . self::ACTIVE_MANUFACTURER . '") {
                    id
                    active
                    icon
                    title
                    shortdesc
                    timestamp
                    seo {
                      description
                      keywords
                      url
                    }
                }
            }'
        );

        $manufacturer = $result['body']['data']['manufacturer'];

        $this->assertSame(self::ACTIVE_MANUFACTURER, $manufacturer['id']);
        $this->assertSame(true, $manufacturer['active']);
        $this->assertMatchesRegularExpression('@https?://.*oreilly_1_mico.png$@', $manufacturer['icon']);
        $this->assertStringContainsString('O\'Reilly', htmlspecialchars_decode($manufacturer['title'], ENT_QUOTES));
        $this->assertSame('', $manufacturer['shortdesc']);
        $this->assertMatchesRegularExpression('@https?://.*Nach-Hersteller/O-Reilly/$@', $manufacturer['seo']['url']);
        $this->assertEquals('german manufacturer seo description', $manufacturer['seo']['description']);
        $this->assertEquals('german manufacturer seo keywords', $manufacturer['seo']['keywords']);

        $this->assertEmpty(
            array_diff(array_keys($manufacturer), [
                'id',
                'active',
                'icon',
                'title',
                'shortdesc',
                'timestamp',
                'seo',
            ])
        );
    }

    public function testGet401ForSingleInactiveManufacturer(): void
    {
        $result = $this->query(
            'query {
                manufacturer (manufacturerId: "' . self::INACTIVE_MANUFACTURER . '") {
                    id
                    active
                    icon
                    title
                    shortdesc
                    timestamp
                }
            }'
        );
        $this->assertSame(
            'Unauthorized',
            $result['body']['errors'][0]['message']
        );
    }

    public function testGet404ForSingleNonExistingManufacturer(): void
    {
        $result = $this->query(
            'query {
                manufacturer (manufacturerId: "DOES-NOT-EXIST") {
                    id
                    active
                    icon
                    title
                    shortdesc
                    timestamp
                }
            }'
        );
        $this->assertSame(
            'Manufacturer was not found by id: DOES-NOT-EXIST',
            $result['body']['errors'][0]['message']
        );
    }

    public function testGetManufacturerListWithoutFilter(): void
    {
        $result = $this->query(
            'query{
                manufacturers {
                    id
                    active
                    icon
                    title
                    shortdesc
                    timestamp
                }
            }'
        );

        // fixtures have 11 active manufacturers
        $this->assertEquals(
            11,
            count($result['body']['data']['manufacturers'])
        );
    }

    public function testGetManufacturerListWithFilter(): void
    {
        $result = $this->query(
            'query{
                manufacturers(filter: {
                    title: {
                        contains: "l"
                    }
                }){
                    id
                }
            }'
        );

        // fixtures have 3 active manufacturers with lowercase l and 3 inactive
        $this->assertEquals(
            3,
            count($result['body']['data']['manufacturers'])
        );
    }

    public function testGetEmptyManufacturerListWithFilter(): void
    {
        $result = $this->query(
            'query{
                manufacturers(filter: {
                    title: {
                        beginsWith: "Fly"
                    }
                }){
                    id
                }
            }'
        );

        // fixtures have 2 inactive manufacturers starting with Fly
        $this->assertEquals(
            0,
            count($result['body']['data']['manufacturers'])
        );
    }

    public function testGetEmptyManufacturerListWithExactMatchFilter(): void
    {
        $result = $this->query(
            'query{
                manufacturers(filter: {
                    title: {
                        equals: "DOES-NOT-EXIST"
                    }
                }){
                    id
                }
            }'
        );

        // fixtures have 0 manufacturers matching title DOES-NOT-EXIST
        $this->assertEquals(
            0,
            count($result['body']['data']['manufacturers'])
        );
    }

    public function testGetManufacturerWithoutProducts(): void
    {
        $result = $this->query(
            'query {
                manufacturer (manufacturerId: "' . self::ACTIVE_MANUFACTURER_WITHOUT_PRODUCTS . '") {
                    id
                    products
                    {
                      id
                    }
                }
            }'
        );

        $this->assertEquals(
            [],
            $result['body']['data']['manufacturer']['products']
        );
    }

    public function testGetManuacturerWithProducts(): void
    {
        $result = $this->query(
            'query {
                manufacturer (manufacturerId: "' . self::ACTIVE_MANUFACTURER . '") {
                    id
                    products(pagination: {limit: 1})
                    {
                      id
                    }
                }
            }'
        );

        $this->assertEquals(
            [
                'id' => self::PRODUCT_RELATED_TO_ACTIVE_MANUFACTURER,
            ],
            $result['body']['data']['manufacturer']['products'][0]
        );
    }

    public static function getManufacturerProductDataProvider()
    {
        return [
            [
                'withToken' => false,
                'productCount' => 1,
                'active' => true,
            ],
            [
                'withToken' => true,
                'productCount' => 2,
                'active' => false,
            ],
        ];
    }

    /**
     * @dataProvider getManufacturerProductDataProvider
     *
     * @param mixed $withToken
     * @param mixed $productCount
     * @param mixed $active
     */
    public function testManufacturerProducts($withToken, $productCount, $active): void
    {
        $queryBuilderFactory = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class);
        $queryBuilder = $queryBuilderFactory->create();

        // set product to inactive
        $queryBuilder
            ->update('oxarticles')
            ->set('oxactive', 0)
            ->where('OXID = :OXID')
            ->setParameter(':OXID', self::PRODUCT_RELATED_TO_ACTIVE_MANUFACTURER)
            ->execute();

        if ($withToken) {
            $this->prepareToken();
        }

        $result = $this->query(
            'query {
                manufacturer(manufacturerId: "' . self::ACTIVE_MANUFACTURER . '") {
                    id
                    products {
                        active
                    }
                }
            }'
        );

        $this->assertCount(
            $productCount,
            $result['body']['data']['manufacturer']['products']
        );

        $productStatus = $result['body']['data']['manufacturer']['products'][0]['active'];
        $this->assertSame(
            $active,
            $productStatus
        );

        // set product back to active
        $queryBuilder
            ->update('oxarticles')
            ->set('oxactive', 1)
            ->where('OXID = :OXID')
            ->setParameter(':OXID', self::PRODUCT_RELATED_TO_ACTIVE_MANUFACTURER)
            ->execute();
    }

    public static function providerGetManufacturerProducts()
    {
        return [
            [
                'offset' => 1,
                'limit' => null,
                'numberOfExpectedProducts' => 6,
            ],
            [
                'offset' => 5,
                'limit' => null,
                'numberOfExpectedProducts' => 2,
            ],
            [
                'offset' => null,
                'limit' => 1,
                'numberOfExpectedProducts' => 1,
            ],
            [
                'offset' => 1,
                'limit' => 2,
                'numberOfExpectedProducts' => 2,
            ],
            [
                'offset' => 9,
                'limit' => 9,
                'numberOfExpectedProducts' => 0,
            ],
        ];
    }

    /**
     * @dataProvider providerGetManufacturerProducts
     */
    public function testGetManufacturerProducts(?int $offset, ?int $limit, ?int $numberOfExpectedProducts): void
    {
        $result = $this->query(
            'query ($offset: Int, $limit: Int) {
                manufacturer (manufacturerId: "' . self::ACTIVE_MULTILANGUAGE_MANUFACTURER . '") {
                    id
                    products(pagination: {offset: $offset, limit: $limit})
                    {
                      id
                    }
                }
            }',
            [
                'offset' => $offset,
                'limit' => $limit,
            ]
        );

        $this->assertEquals(
            $numberOfExpectedProducts,
            count($result['body']['data']['manufacturer']['products'])
        );
    }

    public function testManufacturerSortedProductList(): void
    {
        $result = $this->query(
            'query {
              manufacturer (manufacturerId: "' . self::ACTIVE_MANUFACTURER . '") {
                id
                products (
                    sort: {
                        title: "ASC"
                    }
                ){
                    id
                    title
                }
              }
            }'
        );

        $titles = [];

        foreach ($result['body']['data']['manufacturer']['products'] as $product) {
            $titles[$product['id']] = $product['title'];
        }

        $expected = $titles;
        asort($expected, SORT_FLAG_CASE | SORT_STRING);
        $this->assertSame($expected, $titles);
    }

    public static function dataProviderSortedManufacturersList()
    {
        return [
            'title_asc' => [
                'sortQuery' => '
                    sort: {
                        title: "ASC"
                    }
                ',
                'method' => 'asort',
                'field' => 'title',
            ],
            'title_desc' => [
                'sortQuery' => '
                    sort: {
                        title: "DESC"
                    }
                ',
                'method' => 'arsort',
                'field' => 'title',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSortedManufacturersList
     */
    public function testSortedManufacturers(
        string $sortQuery,
        string $method,
        string $field
    ): void {
        $result = $this->query(
            'query {
                manufacturers(
                    ' . $sortQuery . '
                ) {
                    id
                    title
                }
            }'
        );

        $sortedManufacturers = [];

        foreach ($result['body']['data']['manufacturers'] as $manufacturer) {
            $sortedManufacturers[$manufacturer['id']] = $manufacturer[$field];
        }

        $expected = $sortedManufacturers;

        $method($expected, SORT_STRING | SORT_FLAG_CASE);

        $this->assertSame($expected, $sortedManufacturers);
    }

    public function testManufacturerPictures(): void
    {
        $this->copyAssets();

        $result = $this->query(
            'query {
              manufacturer (manufacturerId: "' . self::ACTIVE_MANUFACTURER . '") {
                id
                title
                icon
                images {
                    alt
                    picture
                    promotion
                    thumbnail
                }
              }
            }'
        );

        $manufacturer = $result['body']['data']['manufacturer'];

        $this->assertSame(self::ACTIVE_MANUFACTURER, $manufacturer['id']);
        $this->assertStringContainsString(
            'O\'Reilly',
            htmlspecialchars_decode($manufacturer['title'], ENT_QUOTES)
        );
        $this->checkImages([
            'icon'      => $manufacturer['icon'],
            'alt'       => $manufacturer['images']['alt'],
            'picture'   => $manufacturer['images']['picture'],
            'promotion' => $manufacturer['images']['promotion'],
            'thumbnail' => $manufacturer['images']['thumbnail'],
        ]);
    }

    public function testManufacturersPictures(): void
    {
        $this->copyAssets();

        $result = $this->query(
            'query {
                manufacturers(filter: {
                    title: {
                        contains: "l"
                    }
                }) {
                id
                title
                icon
                images {
                    alt
                    picture
                    promotion
                    thumbnail
                }
              }
            }'
        );

        $manufacturers = $result['body']['data']['manufacturers'];

        //Check manufacturer with only icon
        $this->assertSame(self::ACTIVE_MULTILANGUAGE_MANUFACTURER, $manufacturers[1]['id']);
        $this->assertStringContainsString(
            'Liquid Force',
            htmlspecialchars_decode($manufacturers[1]['title'], ENT_QUOTES)
        );
        $this->assertMatchesRegularExpression('@https?://.*lf_kite_logo_1_mico.png$@', $manufacturers[1]['icon']);
        $this->assertSame('', $manufacturers[1]['images']['alt']);
        $this->assertSame('', $manufacturers[1]['images']['picture']);
        $this->assertSame('', $manufacturers[1]['images']['promotion']);
        $this->assertSame('', $manufacturers[1]['images']['thumbnail']);

        //Check manufacturer with all images set
        $this->assertSame(self::ACTIVE_MANUFACTURER, $manufacturers[2]['id']);
        $this->assertStringContainsString(
            'O\'Reilly',
            htmlspecialchars_decode($manufacturers[2]['title'], ENT_QUOTES)
        );
        $this->checkImages([
            'icon'      => $manufacturers[2]['icon'],
            'alt'       => $manufacturers[2]['images']['alt'],
            'picture'   => $manufacturers[2]['images']['picture'],
            'promotion' => $manufacturers[2]['images']['promotion'],
            'thumbnail' => $manufacturers[2]['images']['thumbnail'],
        ]);
    }

    private function checkImages(array $manufacturerData = []): void
    {
        $this->assertMatchesRegularExpression('@https?://.*oreilly_1_mico.png$@', $manufacturerData['icon']);
        $this->assertMatchesRegularExpression('@https?://.*oreilly_1_alt_icon.png$@', $manufacturerData['alt']);
        $this->assertMatchesRegularExpression('@https?://.*oreilly_1_picture.png$@', $manufacturerData['picture']);
        $this->assertMatchesRegularExpression('@https?://.*oreilly_1_promo.png$@', $manufacturerData['promotion']);
        $this->assertMatchesRegularExpression('@https?://.*oreilly_1_thumbnail.png$@', $manufacturerData['thumbnail']);
    }
}
