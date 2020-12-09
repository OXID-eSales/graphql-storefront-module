<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Integration\Controller;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;

final class CategoryMultiLanguageTest extends TestCase
{
    public const SORTING_DESC = 1;

    public const SORTING_ASC  = 0;

    private const ACTIVE_CATEGORY = 'd86fdf0d67bf76dc427aabd2e53e0a97';

    private const CATEGORY_WITH_PRODUCTS = 'fad4d7e2b47d87bb6a2773d93d4ae9be';

    /**
     * @dataProvider providerGetCategoryMultiLanguage
     */
    public function testGetCategoryMultiLanguage(string $languageId, string $title): void
    {
        $query = 'query {
            category (id: "' . self::ACTIVE_CATEGORY . '") {
                id
                title
            }
        }';

        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $result = $this->query($query);

        $this->assertResponseStatus(
            200,
            $result
        );

        $this->assertEquals(
            [
                'id'    => self::ACTIVE_CATEGORY,
                'title' => $title,
            ],
            $result['body']['data']['category']
        );
    }

    public function providerGetCategoryMultiLanguage()
    {
        return [
            'de' => [
                'languageId' => '0',
                'title'      => 'Schuhe',
            ],
            'en' => [
                'languageId' => '1',
                'title'      => 'Shoes',
            ],
        ];
    }

    /**
     * @dataProvider providerGetCategoryProductsMultiLanguage
     */
    public function testGetCategoryProductsMultiLanguage(string $languageId, array $products): void
    {
        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $result = $this->query('query {
            category (id: "' . self::CATEGORY_WITH_PRODUCTS . '") {
                title
                products {
                    title
                }
            }
        }');

        $this->assertResponseStatus(
            200,
            $result
        );

        $this->assertEquals(
            $products,
            $result['body']['data']['category']['products']
        );
    }

    public function providerGetCategoryProductsMultiLanguage()
    {
        return [
            'de' => [
                'languageId' => '0',
                'products'   => [
                    [
                        'title' => 'Kuyichi Gürtel JUNO',
                    ],
                    [
                        'title' => 'Kuyichi Ledergürtel JEVER',
                    ],
                    [
                        'title' => 'Sonnenbrille TRIGGERNAUT AGENT ORANGE',
                    ],
                ],
            ],
            'en' => [
                'languageId' => '1',
                'products'   => [
                    [
                        'title' => 'Kuyichi belt JUNO',
                    ],
                    [
                        'title' => 'Kuyichi leather belt JEVER',
                    ],
                    [
                        'title' => 'Sun glasses TRIGGERNAUT AGENT ORANGE',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerGetCategoryListWithFilterMultiLanguage
     */
    public function testGetCategoryListWithFilterMultiLanguage(
        string $languageId,
        string $contains,
        int $count
    ): void {
        $query = 'query{
            categories(filter: {
                title: {
                    contains: "' . $contains . '"
                }
            }){
                id
            }
        }';

        $this->setGETRequestParameter('lang', $languageId);

        $result = $this->query($query);

        $this->assertResponseStatus(
            200,
            $result
        );

        $this->assertCount(
            $count,
            $result['body']['data']['categories']
        );
    }

    public function providerGetCategoryListWithFilterMultiLanguage(): array
    {
        return [
            'de' => [
                'languageId' => '0',
                'contains'   => 'Sch',
                'count'      => 1,
            ],
            'en' => [
                'languageId' => '1',
                'contains'   => 'Sho',
                'count'      => 1,
            ],
        ];
    }

    /**
     * @dataProvider providerGetCategoryListWithFilterMultiLanguage
     */
    public function testSortedCategoriesListByTitle(string $languageId): void
    {
        $this->setGETRequestParameter('lang', $languageId);

        $result = $this->query('query {
            categories(
                sort: {
                    position: ""
                    title: "ASC"
                }
            ) {
                id
                title
            }
        }');

        $this->assertResponseStatus(
            200,
            $result
        );

        $titles = [];

        foreach ($result['body']['data']['categories'] as $category) {
            $titles[$category['id']] = $category['title'];
        }

        $expected = $titles;
        asort($expected, SORT_STRING);
        $this->assertSame($expected, $titles);
    }

    /**
     * @dataProvider dataProviderCategoryProductListFastSorting
     */
    public function testMultiLanguageCategoryProductListFastSorting(string $sorting, int $sortMode, array $expectedProducts): void
    {
        $categoryId = '0f4fb00809cec9aa0910aa9c8fe36751';

        $this->setGETRequestParameter('lang', '2');

        // set category fast sorting
        $queryBuilderFactory = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class);
        $queryBuilder = $queryBuilderFactory->create();

        $queryBuilder
            ->update('oxcategories')
            ->set('OXDEFSORT', ':sort')
            ->set('OXDEFSORTMODE', ':sortMode')
            ->where('OXID = :OXID')
            ->setParameters([
                ':OXID'          => $categoryId,
                ':sort'          => $sorting,
                ':sortMode'      => $sortMode,
            ])
            ->execute();

        $result = $this->query('query {
          category (id: "' . $categoryId . '") {
            id
            products {
                id
            }
          }
        }');

        $products = $result['body']['data']['category']['products'];

        $this->assertResponseStatus(
            200,
            $result
        );

        $this->assertCount(
            12,
            $products
        );

        $this->assertSame(
            $expectedProducts,
            array_slice($products, 0, 3)
        );
    }

    public function dataProviderCategoryProductListFastSorting(): array
    {
        return [
            'price_desc' => [
                'oxprice',
                self::SORTING_DESC,
                [
                    ['id' => 'dc5b9cfeb5bd96fdbd9b4e43974661a1'],
                    ['id' => 'fad21eb148918c8f4d9f0077fedff1ba'],
                    ['id' => 'fadc492a5807c56eb80b0507accd756b'],
                ],
            ],
            'title_asc'  => [
                'oxtitle',
                self::SORTING_ASC,
                [
                    ['id' => 'f4fe052346b4ec271011e25c052682c5'],
                    ['id' => 'b56369b1fc9d7b97f9c5fc343b349ece'],
                    ['id' => 'b5666b6d4bcb67c61dee4887bfba8351'],
                ],
            ],
        ];
    }
}
