<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;

final class ProductMultilanguageTest extends TestCase
{
    private const ACTIVE_MULTILANGUAGE_PRODUCT = '058e613db53d782adfc9f2ccb43c45fe';

    private const ACTIVE_PRODUCT_WITH_VARIANTS = '531b537118f5f4d7a427cdb825440922';

    public function providerGetProductMultilanguage()
    {
        return [
            'de' => [
                'languageId' => '0',
                'title'      => 'Bindung O&#039;BRIEN DECADE CT 2010',
                'url'        => 'Wakeboarding/Bindungen/',
            ],
            'en' => [
                'languageId' => '1',
                'title'      => 'Binding O&#039;BRIEN DECADE CT 2010',
                'url'        => 'en/Wakeboarding/Bindings',
            ],
        ];
    }

    /**
     * @dataProvider providerGetProductMultilanguage
     */
    public function testGetProductMultilanguage(string $languageId, string $title, string $seoUrl): void
    {
        $query = 'query {
            product (id: "' . self::ACTIVE_MULTILANGUAGE_PRODUCT . '") {
                id
                title
                seo {
                   url
                }
            }
        }';

        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $result  = $this->query($query);
        $product = $result['body']['data']['product'];

        $this->assertSame(self::ACTIVE_MULTILANGUAGE_PRODUCT, $product['id']);
        $this->assertEquals($title, $product['title']);
        $this->assertRegExp('@https?://.*' . $seoUrl . '.*@', $product['seo']['url']);
    }

    public function providerGetProductListWithFilterMultilanguage()
    {
        return [
            'de' => [
                'languageId' => '0',
                'count'      => 0,
            ],
            'en' => [
                'languageId' => '1',
                'count'      => 1,
            ],
        ];
    }

    /**
     *  @dataProvider providerGetProductVariantsMultilanguage
     */
    public function testGetProductVariantsMultilanguage(
        string $languageId,
        array $expectedLabels,
        array $expectedVariants
    ): void {
        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $query = 'query {
            product (id: "' . self::ACTIVE_PRODUCT_WITH_VARIANTS . '") {
                variantLabels
                variants {
                    id
                    variantValues
                }
            }
        }';

        $result = $this->query($query);

        $actualVariants = $result['body']['data']['product']['variants'][0];

        $this->assertSame(
            $result['body']['data']['product']['variantLabels'],
            $expectedLabels
        );

        $this->assertSame(
            $actualVariants,
            $expectedVariants
        );
    }

    public function providerGetProductVariantsMultilanguage()
    {
        return [
            'de' => [
                'languageId'    => '0',
                'labels'        => [
                    'Größe',
                    'Farbe',
                ],
                'variants' => [
                    'id'            => '6b6efaa522be53c3e86fdb41f0542a8a',
                    'variantValues' => [
                        'W 30/L 30',
                        'Blau',
                    ],
                ],
            ],
            'en' => [
                'languageId' => '1',
                'labels'     => [
                    'Size',
                    'Color',
                ],
                'values' => [
                    'id'            => '6b6efaa522be53c3e86fdb41f0542a8a',
                    'variantValues' => [
                        'W 30/L 30',
                        'Blue ',
                    ],
                ],
            ],
        ];
    }

    /**
     *  @dataProvider providerGetProductVariantsMultilanguage
     */
    public function testSortedProductListByTitle(string $languageId): void
    {
        $this->setGETRequestParameter('lang', $languageId);

        $result = $this->query('query {
            products(
                sort: {
                    position: ""
                    title: "ASC"
                }
            ) {
                id
                title
            }
        }');

        $titles = [];

        foreach ($result['body']['data']['products'] as $product) {
            $titles[$product['id']] = $product['title'];
        }

        $expected = $titles;
        asort($expected, SORT_FLAG_CASE | SORT_STRING);
        $this->assertSame($expected, $titles);
    }
}
