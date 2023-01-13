<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\DataType;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\Tests\Integration\TokenTestCase;
use OxidEsales\GraphQL\Storefront\Tests\Integration\DemoData;

/**
 * @covers OxidEsales\GraphQL\Storefront\Product\Service\RelationService
 */
final class ProductRelationServiceTest extends TokenTestCase
{
    use DemoData;

    private const ACTIVE_PRODUCT = '058e613db53d782adfc9f2ccb43c45fe';

    private const ACTIVE_PRODUCT_WITH_ACCESSORIES = '05848170643ab0deb9914566391c0c63';

    private const ACTIVE_PRODUCT_WITH_UNITNAME = 'f33d5bcc7135908fd36fc736c643aa1c';

    private const ACTIVE_PRODUCT_WITHOUT_CROSSSELLING = 'f33d5bcc7135908fd36fc736c643aa1c';

    private const INACTIVE_CROSSSELLING_PRODUCT = 'b5685a5230f5050475f214b4bb0e239b';

    private const ACTIVE_PRODUCT_WITH_SELECTION_LISTS = '058de8224773a1d5fd54d523f0c823e0';

    private const ACTIVE_PRODUCT_WITH_RESTOCK_DATE = 'f4fe754e1692b9f79f2a7b1a01bb8dee';

    private const ACTIVE_PRODUCT_WITH_SCALE_PRICES = 'dc53d3c0ca2ae7c38bf51f3410da0bf8';

    private const ACTIVE_PRODUCT_WITH_BUNDLE_ITEM = 'dc53d3c0ca2ae7c38bf51f3410da0bf8';

    private const ACTIVE_PRODUCT_WITHOUT_MANUFACTURER = 'f33d5bcc7135908fd36fc736c643aa1c';

    private const INACTIVE_PRODUCT = '09602cddb5af0aba745293d08ae6bcf6';

    public function testGetAccessoriesRelation(): void
    {
        $result = $this->query(
            'query {
                product (productId: "' . self::ACTIVE_PRODUCT_WITH_ACCESSORIES . '") {
                    id
                    accessories {
                        id
                    }
                }
            }'
        );

        $product = $result['body']['data']['product'];

        $this->assertSame(
            [
                ['id' => 'adcb9deae73557006a8ac748f45288b4'],
                ['id' => 'd86236918e1533cccb679208628eda32'],
            ],
            $product['accessories']
        );
    }

    public function testGetAccessoriesRelationWithToken(): void
    {
        $queryBuilderFactory = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class);
        $queryBuilder = $queryBuilderFactory->create();

        $queryBuilder
            ->update('oxarticles')
            ->set('oxactive', 0)
            ->where('OXID = :OXID')
            ->setParameter(':OXID', 'd86236918e1533cccb679208628eda32')
            ->execute();

        $this->prepareToken();

        $result = $this->query(
            'query {
                product (productId: "' . self::ACTIVE_PRODUCT_WITH_ACCESSORIES . '") {
                    id
                    accessories {
                        id
                    }
                }
            }'
        );

        $product = $result['body']['data']['product'];

        $this->assertSame(
            [
                ['id' => 'adcb9deae73557006a8ac748f45288b4'],
                // TODO Inactive accessories should be visible with a valid token
                // ['id' => 'd86236918e1533cccb679208628eda32'],
            ],
            $product['accessories']
        );
    }

    /**
     * @dataProvider productWithATtributesProvider
     */
    public function testGetProductAttributesRelation(string $productId, array $expected): void
    {
        $result = $this->query(
            '
            query{
                product(productId: "' . $productId . '" ){
                    attributes {
                        value
                        attribute {
                          title
                        }
                    }
                }
            }
        '
        );

        $this->assertSame(
            sort($expected),
            sort($result['body']['data']['product']['attributes'])
        );
    }

    public function productWithAttributesProvider(): array
    {
        return [
            [
                'product' => 'b56369b1fc9d7b97f9c5fc343b349ece',
                'expected' => [
                    [
                        'value' => 'Kite, Backpack, Reparaturset',
                        'attribute' => ['title' => 'Lieferumfang'],
                    ],
                    [
                        'value' => 'Allround',
                        'attribute' => ['title' => 'Einsatzbereich'],
                    ],
                ],
            ],
            [
                'product' => 'f4f0cb3606e231c3fdb34fcaee2d6d04',
                'expected' => [
                    [
                        'value' => 'Allround',
                        'attribute' => ['title' => 'Einsatzbereich'],
                    ],
                    [
                        'value' => 'Kite, Tasche, CPR Control System, Pumpe',
                        'attribute' => ['title' => 'Lieferumfang'],
                    ],
                ],
            ],
            [
                'product' => '058de8224773a1d5fd54d523f0c823e0',
                'expected' => [],
            ],
        ];
    }

    /**
     * @covers OxidEsales\GraphQL\Storefront\Product\DataType\Selection
     * @covers OxidEsales\GraphQL\Storefront\Product\DataType\SelectionList
     * @covers OxidEsales\GraphQL\Storefront\Product\Service\RelationService
     */
    public function testGetSelectionListsRelation(): void
    {
        $result = $this->query(
            'query {
                product (productId: "' . self::ACTIVE_PRODUCT_WITH_SELECTION_LISTS . '") {
                    id
                    selectionLists {
                        title
                        fields {
                            value
                        }
                    }
                }
            }'
        );

        $product = $result['body']['data']['product'];

        $this->assertCount(
            1,
            $product['selectionLists']
        );

        $this->assertSame(
            [
                'title' => 'test selection list [DE] šÄßüл',
                'fields' => [
                    [
                        'value' => 'selvar1 [DE]',
                    ],
                    [
                        'value' => 'selvar2 [DE]',
                    ],
                    [
                        'value' => 'selvar3 [DE]',
                    ],
                    [
                        'value' => 'selvar4 [DE]',
                    ],
                ],
            ],
            $product['selectionLists'][0]
        );
    }

    /**
     * @dataProvider getReviewsConfigDataProvider
     *
     * @param mixed $configValue
     * @param mixed $expectedIds
     */
    public function testGetReviewsRelation($configValue, $expectedIds): void
    {
        Registry::getConfig()->saveShopConfVar('bool', 'blGBModerate', $configValue);

        $result = $this->query(
            'query {
                product (productId: "' . self::ACTIVE_PRODUCT . '") {
                    id
                    reviews {
                        id
                    }
                }
            }'
        );

        $product = $result['body']['data']['product'];

        $this->assertSame(
            $expectedIds,
            $product['reviews']
        );
    }

    public function getReviewsConfigDataProvider()
    {
        return [
            [
                true,
                [
                    ['id' => '_test_real_product_1'],
                    ['id' => '_test_real_product_2'],
                ],
            ],
            [
                false,
                [
                    ['id' => '_test_real_product_1'],
                    ['id' => '_test_real_product_2'],
                    ['id' => '_test_real_product_inactive'],
                ],
            ],
        ];
    }

    /**
     * Get inactive product reviews with token
     * even when blGBModerate is active.
     */
    public function testGetReviewsRelationWithToken(): void
    {
        $this->prepareToken();

        Registry::getConfig()->saveShopConfVar('bool', 'blGBModerate', true);

        $result = $this->query(
            'query {
                product (productId: "' . self::ACTIVE_PRODUCT . '") {
                    id
                    reviews {
                        id
                    }
                }
            }'
        );

        $product = $result['body']['data']['product'];

        $this->assertSame(
            [
                ['id' => '_test_real_product_1'],
                ['id' => '_test_real_product_2'],
                // TODO: Inactive products should be visible when using a valid token
                // ['id' => '_test_real_product_inactive'],
            ],
            $product['reviews']
        );
    }

    public function testGetNoReviewsRelation(): void
    {
        $result = $this->query(
            'query {
                product (productId: "' . self::ACTIVE_PRODUCT_WITH_ACCESSORIES . '") {
                    id
                    reviews {
                        id
                    }
                }
            }'
        );

        $this->assertCount(
            0,
            $result['body']['data']['product']['reviews']
        );
    }

    /**
     * @covers OxidEsales\GraphQL\Storefront\Product\DataType\ProductUnit
     * @covers OxidEsales\GraphQL\Storefront\Product\Service\RelationService
     */
    public function testGetUnitNameAndPriceRelation(): void
    {
        $result = $this->query(
            'query {
            product (productId: "' . self::ACTIVE_PRODUCT_WITH_UNITNAME . '") {
                id
                unit {
                    name
                    price {
                        price
                    }
                }
            }
        }'
        );

        $this->assertSame('g', $result['body']['data']['product']['unit']['name']);
        $this->assertSame(0.42, $result['body']['data']['product']['unit']['price']['price']);
    }

    /**
     * @covers OxidEsales\GraphQL\Storefront\Product\DataType\ProductStock
     * @covers OxidEsales\GraphQL\Storefront\Product\Service\RelationService
     */
    public function testGetRestockDateRelation(): void
    {
        $result = $this->query(
            'query {
            product (productId: "' . self::ACTIVE_PRODUCT_WITH_RESTOCK_DATE . '") {
                id
                stock {
                    restockDate
                }
            }
        }'
        );

        $this->assertSame('2999-12-31T00:00:00+01:00', $result['body']['data']['product']['stock']['restockDate']);
    }

    public function testGetProductVendorRelation(): void
    {
        $result = $this->query(
            'query {
            product (productId: "6b63456b3abeeeccd9b085a76ffba1a3") {
                id
                vendor {
                    id
                }
            }
        }'
        );

        $this->assertSame(
            'a57c56e3ba710eafb2225e98f058d989',
            $result['body']['data']['product']['vendor']['id']
        );
    }

    public function testGetCrossSellingRelation(): void
    {
        $result = $this->query(
            'query {
            product (productId: "' . self::ACTIVE_PRODUCT . '") {
                id
                crossSelling {
                    id
                    active
                }
            }
        }'
        );

        $this->assertCount(
            3,
            $result['body']['data']['product']['crossSelling']
        );

        $this->assertSame(
            [
                'id' => self::INACTIVE_CROSSSELLING_PRODUCT,
                'active' => true,
            ],
            $result['body']['data']['product']['crossSelling'][0]
        );
    }

    public function testGetNoCrossSellingRelation(): void
    {
        $result = $this->query(
            'query {
            product (productId: "' . self::ACTIVE_PRODUCT_WITHOUT_CROSSSELLING . '") {
                id
                crossSelling {
                    id
                }
            }
        }'
        );

        $this->assertSame(
            [],
            $result['body']['data']['product']['crossSelling']
        );
    }

    public function testGetProductManufacturerRelation(): void
    {
        $result = $this->query(
            'query {
            product (productId: "6b63456b3abeeeccd9b085a76ffba1a3") {
                id
                manufacturer {
                    id
                }
            }
        }'
        );

        $this->assertSame(
            '9434afb379a46d6c141de9c9e5b94fcf',
            $result['body']['data']['product']['manufacturer']['id']
        );
    }

    public function testGetProductWithoutManufacturerRelation(): void
    {
        $result = $this->query(
            'query {
            product (productId: "' . self::ACTIVE_PRODUCT_WITHOUT_MANUFACTURER . '") {
                id
                manufacturer {
                    id
                }
            }
        }'
        );

        $this->assertNull($result['body']['data']['product']['manufacturer']);
    }

    public function testGetNoProductBundleItemRelation(): void
    {
        $config = Registry::getConfig();
        $oldParam = $config->getConfigParam('bl_perfLoadAccessoires');
        $config->saveShopConfVar('bool', 'bl_perfLoadAccessoires', false);

        $result = $this->query(
            'query {
            product (productId: "' . self::ACTIVE_PRODUCT_WITH_BUNDLE_ITEM . '") {
                id
                bundleProduct {
                    id
                }
            }
        }'
        );

        $this->assertNull($result['body']['data']['product']['bundleProduct']);

        $config->saveShopConfVar('bool', 'bl_perfLoadAccessoires', $oldParam);
    }

    public function testGetNoNonExistingProductBundleItemRelation(): void
    {
        $queryBuilderFactory = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class);

        $queryBuilder = $queryBuilderFactory->create();
        $queryBuilder->update('oxarticles')
            ->set('oxbundleid', ':BUNDLEID')
            ->where('OXID = :OXID')
            ->setParameter(':OXID', self::ACTIVE_PRODUCT_WITH_BUNDLE_ITEM)
            ->setParameter(':BUNDLEID', 'THIS-IS-INVALID')
            ->execute();

        $result = $this->query(
            'query {
            product (productId: "' . self::ACTIVE_PRODUCT_WITH_BUNDLE_ITEM . '") {
                id
                bundleProduct {
                    id
                }
            }
        }'
        );

        $this->assertNull($result['body']['data']['product']['bundleProduct']);

        $queryBuilder = $queryBuilderFactory->create();
        $queryBuilder->update('oxarticles')
            ->set('oxbundleid', ':BUNDLEID')
            ->where('OXID = :OXID')
            ->setParameter(':OXID', self::ACTIVE_PRODUCT_WITH_BUNDLE_ITEM)
            ->setParameter(':BUNDLEID', '')
            ->execute();
    }

    public function testGetNoInvisibleProductBundleItemRelation(): void
    {
        $queryBuilderFactory = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class);

        $queryBuilder = $queryBuilderFactory->create();
        $queryBuilder->update('oxarticles')
            ->set('oxbundleid', ':BUNDLEID')
            ->where('OXID = :OXID')
            ->setParameter(':OXID', self::ACTIVE_PRODUCT_WITH_BUNDLE_ITEM)
            ->setParameter(':BUNDLEID', self::INACTIVE_PRODUCT)
            ->execute();

        $result = $this->query(
            'query {
            product (productId: "' . self::ACTIVE_PRODUCT_WITH_BUNDLE_ITEM . '") {
                id
                bundleProduct {
                    id
                }
            }
        }'
        );

        $this->assertNull($result['body']['data']['product']['bundleProduct']);

        $queryBuilder = $queryBuilderFactory->create();
        $queryBuilder->update('oxarticles')
            ->set('oxbundleid', ':BUNDLEID')
            ->where('OXID = :OXID')
            ->setParameter(':OXID', self::ACTIVE_PRODUCT_WITH_BUNDLE_ITEM)
            ->setParameter(':BUNDLEID', '')
            ->execute();
    }

    public function testGetExistingProductBundleItemRelation(): void
    {
        $queryBuilderFactory = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class);

        $queryBuilder = $queryBuilderFactory->create();
        $queryBuilder->update('oxarticles')
            ->set('oxbundleid', ':BUNDLEID')
            ->where('OXID = :OXID')
            ->setParameter(':OXID', self::ACTIVE_PRODUCT_WITH_BUNDLE_ITEM)
            ->setParameter(':BUNDLEID', self::ACTIVE_PRODUCT_WITH_BUNDLE_ITEM)
            ->execute();

        $result = $this->query(
            'query {
            product (productId: "' . self::ACTIVE_PRODUCT_WITH_BUNDLE_ITEM . '") {
                id
                bundleProduct {
                    id
                }
            }
        }'
        );

        $this->assertSame(
            self::ACTIVE_PRODUCT_WITH_BUNDLE_ITEM,
            $result['body']['data']['product']['bundleProduct']['id']
        );

        $queryBuilder = $queryBuilderFactory->create();
        $queryBuilder->update('oxarticles')
            ->set('oxbundleid', ':BUNDLEID')
            ->where('OXID = :OXID')
            ->setParameter(':OXID', self::ACTIVE_PRODUCT_WITH_BUNDLE_ITEM)
            ->setParameter(':BUNDLEID', '')
            ->execute();
    }

    /**
     * @covers OxidEsales\GraphQL\Storefront\Product\DataType\ProductScalePrice
     * @covers OxidEsales\GraphQL\Storefront\Product\Service\RelationService
     */
    public function testGetScalePricesRelation(): void
    {
        $result = $this->query(
            'query {
            product (productId: "' . self::ACTIVE_PRODUCT_WITH_SCALE_PRICES . '") {
                id
                scalePrices {
                    absoluteScalePrice
                    absolutePrice
                    discount
                    amountFrom
                    amountTo
                }
            }
        }'
        );

        $this->assertCount(
            3,
            $result['body']['data']['product']['scalePrices']
        );
        $this->assertSame(
            [
                'absoluteScalePrice' => true,
                'absolutePrice' => 27.9,
                'discount' => null,
                'amountFrom' => 5,
                'amountTo' => 9,
            ],
            $result['body']['data']['product']['scalePrices'][0]
        );
        $this->assertSame(
            [
                'absoluteScalePrice' => true,
                'absolutePrice' => 25.9,
                'discount' => null,
                'amountFrom' => 10,
                'amountTo' => 19,
            ],
            $result['body']['data']['product']['scalePrices'][1]
        );
        $this->assertSame(
            [
                'absoluteScalePrice' => true,
                'absolutePrice' => 21.9,
                'discount' => null,
                'amountFrom' => 20,
                'amountTo' => 99,
            ],
            $result['body']['data']['product']['scalePrices'][2]
        );
    }

    public function testGetProductCategoryRelation(): void
    {
        $result = $this->query(
            'query {
            product (productId: "' . self::ACTIVE_PRODUCT . '") {
                id
                categories {
                    id
                }
            }
        }'
        );

        $this->assertSame(
            '0f40c6a077b68c21f164767c4a903fd2',
            $result['body']['data']['product']['categories'][0]['id']
        );
    }
}
