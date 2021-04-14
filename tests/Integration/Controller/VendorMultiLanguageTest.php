<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;

final class VendorMultiLanguageTest extends TestCase
{
    private const ACTIVE_VENDOR = 'a57c56e3ba710eafb2225e98f058d989';

    /**
     * @dataProvider providerGetVendorListWithFilterMultiLanguage
     */
    public function testGetVendorListWithFilterMultiLanguage(
        string $languageId,
        string $contains,
        int $count,
        array $expectedVendors
    ): void {
        $query = 'query{
            vendors(filter: {
                title: {
                    contains: "' . $contains . '"
                }
            }){
                title
                seo {
                   url
                }
            }
        }';

        $this->setGETRequestParameter('lang', $languageId);

        $result  = $this->query($query);
        $vendors = $result['body']['data']['vendors'];

        $this->assertCount(
            $count,
            $vendors
        );

        $expectedVendor = $expectedVendors[0];
        $vendor         = $vendors[0];

        $this->assertNotFalse(
            parse_url($vendor['seo']['url'])
        );

        $this->assertEquals(
            $expectedVendor['title'],
            $vendor['title']
        );

        $this->assertEquals(
            $expectedVendor['url'],
            parse_url($vendor['seo']['url'])['path']
        );
    }

    public function providerGetVendorListWithFilterMultiLanguage(): array
    {
        return [
            'de' => [
                'languageId' => '0',
                'contains'   => 'de',
                'count'      => 1,
                'result'     => [
                    [
                        'title' => 'https://fashioncity.com/de',
                        'url'   => '/Nach-Lieferant/https-fashioncity-com-de/',
                    ],
                ],
            ],
            'en' => [
                'languageId' => '1',
                'contains'   => 'en',
                'count'      => 1,
                'result'     => [
                    [
                        'title' => 'https://fashioncity.com/en',
                        'url'   => '/en/By-distributor/https-fashioncity-com-en/',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerGetVendorMultiLanguage
     */
    public function testGetVendorMultiLanguage(string $languageId, string $title, string $productShortDescription): void
    {
        $query = 'query {
            vendor (vendorId: "' . self::ACTIVE_VENDOR . '") {
                id
                title
                products(pagination: {limit: 1}) {
                    shortDescription
                }
            }
        }';

        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $result = $this->query($query);

        $this->assertEquals(
            [
                'id'       => self::ACTIVE_VENDOR,
                'title'    => $title,
                'products' => [
                    ['shortDescription' => $productShortDescription],
                ],
            ],
            $result['body']['data']['vendor']
        );
    }

    public function providerGetVendorMultiLanguage()
    {
        return [
            'de' => [
                'languageId'              => '0',
                'title'                   => 'www.true-fashion.com',
                'productShortDescription' => 'Lässige Damenjeans von Kuyichi',
            ],
            'en' => [
                'languageId'              => '1',
                'title'                   => 'www.true-fashion.com',
                'productShortDescription' => 'Cool lady jeans by Kuyichi',
            ],
        ];
    }

    public function providerGetVendorListMultilanguage()
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
     *  @dataProvider providerGetVendorListMultilanguage
     */
    public function testSortedVendorList(string $languageId): void
    {
        $this->setGETRequestParameter('lang', $languageId);

        $result = $this->query('query {
            vendors(
                sort: {
                    title: "ASC"
                }
            ) {
                title
            }
        }');

        $sortedVendors = $result['body']['data']['vendors'];
        $expected      = $sortedVendors;

        asort($expected, SORT_STRING | SORT_FLAG_CASE);

        $this->assertSame($expected, $sortedVendors);
    }
}
