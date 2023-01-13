<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Storefront\Tests\Integration\MultiLanguageTestCase;

final class ManufacturerMultiLanguageTest extends MultiLanguageTestCase
{
    private const ACTIVE_MULTILANGUAGE_MANUFACTURER = 'adc6df0977329923a6330cc8f3c0a906';

    public function providerGetManufacturerMultilanguage()
    {
        return [
            'de' => [
                'languageId' => '0',
                'title' => 'Liquid Force',
                'url' => 'Nach-Hersteller/Liquid-Force/',
                'productDescription' => 'Bewährte Qualität in neuem Design',
            ],
            'en' => [
                'languageId' => '1',
                'title' => 'Liquid Force Kite',
                'url' => 'en/By-Manufacturer/Liquid-Force-Kite/',
                'productDescription' => 'Proven quality in a new design',
            ],
        ];
    }

    /**
     * @dataProvider providerGetManufacturerMultilanguage
     */
    public function testGetManufacturerMultilanguage(
        string $languageId,
        string $title,
        string $seoUrl,
        string $productDescription
    ): void {
        $query = 'query {
            manufacturer (manufacturerId: "' . self::ACTIVE_MULTILANGUAGE_MANUFACTURER . '") {
                id
                title
                seo {
                    url
                },
                 products(pagination: {limit: 1})
                {
                  shortDescription
                }
            }
        }';

        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $result = $this->query($query);
        $manufacturer = $result['body']['data']['manufacturer'];

        $this->assertSame(self::ACTIVE_MULTILANGUAGE_MANUFACTURER, $manufacturer['id']);
        $this->assertEquals($title, $manufacturer['title']);
        $this->assertMatchesRegularExpression('@https?://.*' . $seoUrl . '$@', $manufacturer['seo']['url']);
        $this->assertSame($productDescription, $manufacturer['products'][0]['shortDescription']);
    }

    public function providerGetManufacturerListWithFilterMultilanguage()
    {
        return [
            'de' => [
                'languageId' => '0',
                'count' => 0,
            ],
            'en' => [
                'languageId' => '1',
                'count' => 1,
            ],
        ];
    }

    /**
     * @dataProvider providerGetManufacturerListWithFilterMultilanguage
     */
    public function testGetManufacturerListWithFilterMultilanguage(string $languageId, int $count): void
    {
        $query = 'query{
            manufacturers(filter: {
                title: {
                    contains: "Force Kite"
                }
            }){
                id
            }
        }';

        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $result = $this->query($query);

        $this->assertCount(
            $count,
            $result['body']['data']['manufacturers']
        );
    }

    public function providerGetManufacturersMultilanguage()
    {
        return [
            'de' => [
                'languageId' => '0',
            ],
            'en' => [
                'languageId' => '1',
            ],
        ];
    }

    /**
     * @dataProvider providerGetManufacturersMultilanguage
     */
    public function testSortedManufacturersList(string $languageId): void
    {
        $this->setGETRequestParameter('lang', $languageId);

        $result = $this->query(
            'query {
            manufacturers(
                sort: {
                    title: "ASC"
                }
            ) {
                id
                title
            }
        }'
        );

        $sortedManufacturers = [];

        foreach ($result['body']['data']['manufacturers'] as $manufacturer) {
            $sortedManufacturers[$manufacturer['id']] = $manufacturer['title'];
        }

        $expected = $sortedManufacturers;

        asort($expected, SORT_STRING | SORT_FLAG_CASE);

        $this->assertSame($expected, $sortedManufacturers);
    }
}
