<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use DateTimeImmutable;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\Tests\Integration\TokenTestCase;
use TheCodingMachine\GraphQLite\Types\DateTimeType;

final class VendorTest extends TokenTestCase
{
    private const ACTIVE_VENDOR = 'a57c56e3ba710eafb2225e98f058d989';

    private const INACTIVE_VENDOR  = '05833e961f65616e55a2208c2ed7c6b8';

    private const PRODUCT_RELATED_TO_ACTIVE_VENDOR  = '531b537118f5f4d7a427cdb825440922';

    public function testGetSingleActiveVendor(): void
    {
        $result = $this->query('query {
            vendor (vendorId: "' . self::ACTIVE_VENDOR . '") {
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
                products {
                    id
                }
            }
        }');

        $vendor = $result['body']['data']['vendor'];
        $this->assertSame(self::ACTIVE_VENDOR, $vendor['id']);
        $this->assertTrue($vendor['active']);
        $this->assertNull($vendor['icon']);
        $this->assertEquals('www.true-fashion.com', $vendor['title']);
        $this->assertSame('Ethical style outlet', $vendor['shortdesc']);
        $this->assertRegExp('@https?://.*/Nach-Lieferant/www-true-fashion-com/$@', $vendor['seo']['url']);
        $this->assertEquals('', $vendor['seo']['description']);
        $this->assertEquals('', $vendor['seo']['keywords']);

        $dateTimeType = new DateTimeType();
        //Fixture timestamp can have few seconds difference
        $this->assertLessThanOrEqual(
            $dateTimeType->serialize(new DateTimeImmutable('now')),
            $vendor['timestamp']
        );

        $this->assertCount(
            13,
            $vendor['products']
        );

        $this->assertEmpty(array_diff(array_keys($vendor), [
            'id',
            'active',
            'icon',
            'title',
            'shortdesc',
            'timestamp',
            'seo',
            'products',
        ]));
    }

    public function testGetSingleInactiveVendorWithoutToken(): void
    {
        $result = $this->query('query {
            vendor (vendorId: "' . self::INACTIVE_VENDOR . '") {
                id
                active
                icon
                title
                shortdesc
                seo {
                  description
                  keywords
                  url
                }
            }
        }');

        $this->assertSame(
            'Unauthorized',
            $result['body']['errors'][0]['message']
        );
    }

    public function testGetSingleInactiveVendorWithToken(): void
    {
        $this->prepareToken();

        $result = $this->query('query {
            vendor (vendorId: "' . self::INACTIVE_VENDOR . '") {
                id
            }
        }');

        $this->assertEquals(
            [
                'id' => self::INACTIVE_VENDOR,
            ],
            $result['body']['data']['vendor']
        );
    }

    public function testGetSingleNonExistingVendor(): void
    {
        $result = $this->query('query {
            vendor (vendorId: "DOES-NOT-EXIST") {
                id
            }
        }');

        $this->assertSame(
            'Vendor was not found by id: DOES-NOT-EXIST',
            $result['body']['errors'][0]['message']
        );
    }

    public function testGetVendorListWithoutFilter(): void
    {
        $result = $this->query('query {
            vendors {
                id
            }
        }');

        $this->assertCount(
            2,
            $result['body']['data']['vendors']
        );
        $this->assertSame(
            [
                [
                    'id'        => 'a57c56e3ba710eafb2225e98f058d989',
                ],
                [
                    'id'        => 'fe07958b49de225bd1dbc7594fb9a6b0',
                ],
            ],
            $result['body']['data']['vendors']
        );
    }

    public function testGetVendorListWithAdminToken(): void
    {
        $this->prepareToken();

        $result = $this->query('query {
            vendors {
                id
            }
        }');

        $this->assertEquals(
            [
                [
                    'id' => '05833e961f65616e55a2208c2ed7c6b8',
                ],
                [
                    'id' => 'a57c56e3ba710eafb2225e98f058d989',
                ],
                [
                    'id' => 'fe07958b49de225bd1dbc7594fb9a6b0',
                ],
            ],
            $result['body']['data']['vendors']
        );
    }

    public function testGetVendorListWithExactFilter(): void
    {
        $result = $this->query('query {
            vendors (filter: {
                title: {
                    equals: "www.true-fashion.com"
                }
            }) {
                id
            }
        }');

        $this->assertEquals(
            [
                [
                    'id' => 'a57c56e3ba710eafb2225e98f058d989',
                ],
            ],
            $result['body']['data']['vendors']
        );
    }

    public function testGetVendorListWithPartialFilter(): void
    {
        $result = $this->query('query {
            vendors (filter: {
                title: {
                    contains: "city"
                }
            }) {
                id
            }
        }');

        $this->assertEquals(
            [
                [
                    'id' => 'fe07958b49de225bd1dbc7594fb9a6b0',
                ],
            ],
            $result['body']['data']['vendors']
        );
    }

    public function testGetEmptyVendorListWithFilter(): void
    {
        $result = $this->query('query {
            vendors (filter: {
                title: {
                    contains: "DOES-NOT-EXIST"
                }
            }) {
                id
            }
        }');

        $this->assertEquals(
            0,
            count($result['body']['data']['vendors'])
        );
    }

    public function dataProviderSortedVendorList()
    {
        return  [
            'title_asc' => [
                'sortquery' => '
                    sort: {
                        title: "ASC"
                    }
                ',
                'method'    => 'asort',
            ],
            'title_desc' => [
                'sortquery' => '
                    sort: {
                        title: "DESC"
                    }
                ',
                'method'    => 'arsort',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSortedVendorList
     */
    public function testSortedVendorList(
        string $sortQuery,
        string $method
    ): void {
        $result = $this->query('query {
            vendors(
                ' . $sortQuery . '
            ) {
                title
            }
        }');

        $sortedVendors = $result['body']['data']['vendors'];
        $expected      = $sortedVendors;

        $method($expected, SORT_STRING | SORT_FLAG_CASE);

        $this->assertSame($expected, $sortedVendors);
    }

    public function testVendorProductsWithOffsetAndLimit(): void
    {
        $result = $this->query('query {
            vendor (vendorId: "' . self::ACTIVE_VENDOR . '") {
                products(pagination: {limit: 1, offset: 1}) {
                    title
                }
            }
        }');

        $products = $result['body']['data']['vendor']['products'];

        $this->assertEquals(
            [
                ['title' => 'Kuyichi Jeans KYLE'],
            ],
            $products
        );
    }

    public function getVendorProductsDataProvider()
    {
        return [
            [
                'withToken'             => false,
                'expectedProductsCount' => 12,
                'active'                => true,
            ], [
                'withToken'             => true,
                'expectedProductsCount' => 13,
                'active'                => false,
            ],
        ];
    }

    /**
     * @dataProvider getVendorProductsDataProvider
     *
     * @param mixed $withToken
     * @param mixed $productCount
     * @param mixed $active
     */
    public function testVendorProducts($withToken, $productCount, $active): void
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
            ->setParameter(':OXID', self::PRODUCT_RELATED_TO_ACTIVE_VENDOR)
            ->execute();

        if ($withToken) {
            $this->prepareToken();
        }

        $result = $this->query('query {
            vendor(vendorId: "' . self::ACTIVE_VENDOR . '") {
                id
                products {
                    active
                }
            }
        }');

        $this->assertCount(
            $productCount,
            $result['body']['data']['vendor']['products']
        );

        $productStatus = $result['body']['data']['vendor']['products'][0]['active'];
        $this->assertSame(
            $active,
            $productStatus
        );
    }

    public function getVendorsProductListWithToken()
    {
        return [
            [
                'withToken'             => false,
                'expectedProductsCount' => 12,
                'active'                => true,
            ], [
                'withToken'             => true,
                'expectedProductsCount' => 13,
                'active'                => false,
            ],
        ];
    }

    /**
     * @dataProvider getVendorsProductListWithToken
     *
     * @param mixed $withToken
     * @param mixed $productCount
     * @param mixed $active
     */
    public function testVendorsProductList($withToken, $productCount, $active): void
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
            ->setParameter(':OXID', self::PRODUCT_RELATED_TO_ACTIVE_VENDOR)
            ->execute();

        if ($withToken) {
            $this->prepareToken();
        }

        $result = $this->query('query {
          vendors(filter: {
            title: {
              contains: "fashion"
            }
              }) {
                id
                products {
                    active
                }
            }
        }');

        $this->assertCount(
            $productCount,
            $result['body']['data']['vendors'][0]['products']
        );

        $productStatus = $result['body']['data']['vendors'][0]['products'][0]['active'];
        $this->assertSame(
            $active,
            $productStatus
        );
    }

    public function testVendorSortedProductList(): void
    {
        $result = $this->query('query {
          vendor (vendorId: "' . self::ACTIVE_VENDOR . '") {
            id
            products (
                sort: {
                    position: ""
                    title: "ASC"
                }
            ){
                id
                title
            }
          }
        }');

        $titles = [];

        foreach ($result['body']['data']['vendor']['products'] as $product) {
            $titles[$product['id']] = $product['title'];
        }

        $expected = $titles;
        asort($expected, SORT_FLAG_CASE | SORT_STRING);
        $this->assertSame($expected, $titles);
    }
}
