<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\Eshop\Core\Element2ShopRelations;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\Tests\Integration\MultishopTestCase;

/**
 * Class ProductEnterpriseTest
 */
final class ProductEnterpriseTest extends MultishopTestCase
{
    private const PRODUCT_ID = '058e613db53d782adfc9f2ccb43c45fe';

    private const ACTIVE_PRODUCT_WITH_VARIANTS = '531b537118f5f4d7a427cdb825440922';

    protected function setUp(): void
    {
        parent::setUp();

        $this->setActiveState(self::PRODUCT_ID, 'oxarticles');
    }

    /**
     * Check if active product from shop 1 is not accessible for
     * shop 2 if its not yet related to shop 2
     */
    public function testGetSingleNotInShopActiveProductWillFail(): void
    {
        $this->setGETRequestParameter('shp', '2');

        $result = $this->query(
            'query {
            product (productId: "' . self::PRODUCT_ID . '") {
                id
            }
        }'
        );

        $this->assertSame(
            'Product was not found by id: ' . self::PRODUCT_ID,
            $result['body']['errors'][0]['message']
        );
    }

    /**
     * Check if active product from shop 1 is accessible for
     * shop 2 if its related to shop 2
     */
    public function testGetSingleInShopActiveProductWillWork(): void
    {
        $this->setGETRequestParameter('shp', '2');
        $this->setGETRequestParameter('lang', '0');
        $this->addProductToShops([2], self::PRODUCT_ID);

        $result = $this->query(
            'query {
            product (productId: "' . self::PRODUCT_ID . '") {
                id,
                title
            }
        }'
        );

        $this->assertEquals(
            [
                'id' => self::PRODUCT_ID,
                'title' => 'Bindung O\'BRIEN DECADE CT 2010',
            ],
            $result['body']['data']['product']
        );
    }

    /**
     * @return array
     */
    public function providerGetProductMultilanguage()
    {
        return [
            'shop_1_de' => [
                'shopId' => '1',
                'languageId' => '0',
                'title' => 'Bindung O\'BRIEN DECADE CT 2010',
            ],
            'shop_1_en' => [
                'shopId' => '1',
                'languageId' => '1',
                'title' => 'Binding O\'BRIEN DECADE CT 2010',
            ],
            'shop_2_de' => [
                'shopId' => '2',
                'languageId' => '0',
                'title' => 'Bindung O\'BRIEN DECADE CT 2010',
            ],
            'shop_2_en' => [
                'shopId' => '2',
                'languageId' => '1',
                'title' => 'Binding O\'BRIEN DECADE CT 2010',
            ],
        ];
    }

    /**
     * Check multishop multilanguage data is accessible
     *
     * @dataProvider providerGetProductMultilanguage
     *
     * @param mixed $shopId
     * @param mixed $languageId
     * @param mixed $title
     */
    public function testGetSingleTranslatedSecondShopProduct($shopId, $languageId, $title): void
    {
        $this->setGETRequestParameter('shp', $shopId);
        $this->setGETRequestParameter('lang', $languageId);
        $this->addProductToShops([2], self::PRODUCT_ID);

        $result = $this->query(
            'query {
            product (productId: "' . self::PRODUCT_ID . '") {
                id
                title
            }
        }'
        );

        $this->assertEquals(
            [
                'id' => self::PRODUCT_ID,
                'title' => $title,
            ],
            $result['body']['data']['product']
        );
    }

    /**
     * @dataProvider providerGetProductVariantsSubshop
     */
    public function testGetProductVariantsSubshop(
        string $shopId,
        string $languageId,
        array $expectedLabels,
        array $expectedVariants
    ): void {
        $this->setGETRequestParameter('shp', $shopId);
        $this->setGETRequestParameter('lang', $languageId);
        $this->addProductToShops([2], self::ACTIVE_PRODUCT_WITH_VARIANTS);

        $result = $this->query(
            'query {
            product (productId: "' . self::ACTIVE_PRODUCT_WITH_VARIANTS . '") {
                variantLabels
                variants {
                    id
                    variantValues
                }
            }
        }'
        );

        $this->assertSame(
            $result['body']['data']['product']['variantLabels'],
            $expectedLabels
        );

        $this->assertSame(
            $result['body']['data']['product']['variants'][0] ?? $result['body']['data']['product']['variants'],
            $expectedVariants
        );
    }

    public function providerGetProductVariantsSubshop()
    {
        return [
            'shop_1_de' => [
                'shopId' => '1',
                'languageId' => '0',
                'labels' => [
                    'Größe',
                    'Farbe',
                ],
                'variants' => [
                    'id' => '6b6efaa522be53c3e86fdb41f0542a8a',
                    'variantValues' => [
                        'W 30/L 30',
                        'Blau',
                    ],
                ],
            ],
            'shop_1_en' => [
                'shopId' => '1',
                'languageId' => '1',
                'labels' => [
                    'Size',
                    'Color',
                ],
                'values' => [
                    'id' => '6b6efaa522be53c3e86fdb41f0542a8a',
                    'variantValues' => [
                        'W 30/L 30',
                        'Blue ',
                    ],
                ],
            ],
            'shop_2_de' => [
                'shopId' => '2',
                'languageId' => '0',
                'labels' => [
                    'Größe',
                    'Farbe',
                ],
                'variants' => [],
            ],
            'shop_2_en' => [
                'shopId' => '2',
                'languageId' => '1',
                'labels' => [
                    'Size',
                    'Color',
                ],
                'variants' => [],
            ],
        ];
    }

    private function addProductToShops(array $shops, string $productId): void
    {
        $oElement2ShopRelations = oxNew(Element2ShopRelations::class, 'oxarticles');
        $oElement2ShopRelations->setShopIds($shops);
        $oElement2ShopRelations->addToShop($productId);
    }

    private function setActiveState(string $id, string $table = 'oxarticles', int $active = 1): void
    {
        $queryBuilderFactory = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class);
        $queryBuilder = $queryBuilderFactory->create();

        // set product to inactive
        $queryBuilder
            ->update($table)
            ->set('oxactive', $active)
            ->where('OXID = :OXID')
            ->setParameter(':OXID', $id)
            ->execute();
    }
}
