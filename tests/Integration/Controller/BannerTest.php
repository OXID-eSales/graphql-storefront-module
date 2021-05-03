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

final class BannerTest extends BaseTestCase
{
    private const ACTIVE_BANNER_WITH_PRODUCT = 'b5639c6431b26687321f6ce654878fa5';

    private const ACTIVE_BANNER_WITHOUT_PRODUCT = 'cb34f86f56162d0c95890b5985693710';

    private const INACTIVE_BANNER = 'b56a097dedf5db44e20ed56ac6defaa8';

    private const INACTIVE_BANNER_WITH_INTERVAL = '_test_active_interval';

    private const WRONG_TYPE_ACTION = 'd51545e80843be666a9326783a73e91d';

    private const ACTIVE_BANNER_PRODUCT = 'f4fc98f99e3660bd2ecd7450f832c41a';

    /**
     * If product assigned, link is pointing to product
     */
    public function testGetSingleActiveBannerWithProduct(): void
    {
        $result = $this->query('query {
            banner(bannerId: "' . self::ACTIVE_BANNER_WITH_PRODUCT . '") {
                id
                active
                title
                picture
                link
                sorting
                product{
                  id
                  title
                }
            }
        }');

        $banner = $result['body']['data']['banner'];

        $this->assertSame(self::ACTIVE_BANNER_WITH_PRODUCT, $banner['id']);
        $this->assertTrue($banner['active']);
        $this->assertSame(4, $banner['sorting']);
        $this->assertSame([
            'id'    => self::ACTIVE_BANNER_PRODUCT,
            'title' => 'Neoprenanzug NPX ASSASSIN',
        ], $banner['product']);
        $this->assertEquals('Banner 1', $banner['title']);

        $this->assertMatchesRegularExpression(
            '@https?://.*/Bekleidung/Sportswear/Neopren/Anzuege/Neoprenanzug-NPX-ASSASSIN.html$@',
            $banner['link']
        );

        $this->assertMatchesRegularExpression(
            '@https?://.*/out/pictures/promo/surfer_wave_promo.jpg$@',
            $banner['picture']
        );
    }

    /**
     * This case will checks different link generation process, its not a link to product anymore
     */
    public function testGetSingleActiveBannerWithoutProduct(): void
    {
        $result = $this->query('query {
            banner(bannerId: "' . self::ACTIVE_BANNER_WITHOUT_PRODUCT . '") {
                id
                active
                title
                picture
                link
                sorting
                product{
                  id
                  title
                }
            }
        }');

        $banner = $result['body']['data']['banner'];

        $this->assertSame(self::ACTIVE_BANNER_WITHOUT_PRODUCT, $banner['id']);
        $this->assertTrue($banner['active']);
        $this->assertSame(1, $banner['sorting']);
        $this->assertNull($banner['product']);
        $this->assertEquals('Banner 4', $banner['title']);

        $this->assertMatchesRegularExpression(
            '@https?://.*/Wakeboarding/Wakeboards/.*?$@',
            $banner['link']
        );

        $this->assertMatchesRegularExpression(
            '@https?://.*/out/pictures/promo/banner4de\(1\)_promo.jpg$@',
            $banner['picture']
        );
    }

    public function testInactive(): void
    {
        $result = $this->query('query {
            banner (bannerId: "' . self::INACTIVE_BANNER . '") {
                id
                active
            }
        }');

        $this->assertSame(
            'Unauthorized',
            $result['body']['errors'][0]['message']
        );
    }

    public function testNotExisting(): void
    {
        $result = $this->query('query {
            banner (bannerId: "wrong_id") {
                id
                active
            }
        }');

        $this->assertSame(
            'Banner was not found by id: wrong_id',
            $result['body']['errors'][0]['message']
        );
    }

    public function testWrongType(): void
    {
        $result = $this->query('query {
            banner (bannerId: "' . self::WRONG_TYPE_ACTION . '") {
                id
                active
            }
        }');

        $this->assertSame(
            'Banner was not found by id: ' . self::WRONG_TYPE_ACTION,
            $result['body']['errors'][0]['message']
        );
    }

    public function testInactiveButActiveInterval(): void
    {
        $result = $this->query('query {
            banner (bannerId: "' . self::INACTIVE_BANNER_WITH_INTERVAL . '") {
                id
                active
            }
        }');

        $this->assertEquals(
            [
                'id'     => self::INACTIVE_BANNER_WITH_INTERVAL,
                'active' => true,
            ],
            $result['body']['data']['banner']
        );
    }

    public function testGetBannersList(): void
    {
        $result = $this->query('query {
            banners {
                id,
                sorting
            }
        }');

        $this->assertSame([
            [
                'id'      => 'cb34f86f56162d0c95890b5985693710',
                'sorting' => 1,
            ],
            [
                'id'      => 'b56efaf6c93664b6dca5b1cee1f87057',
                'sorting' => 2,
            ],
            [
                'id'      => 'b5639c6431b26687321f6ce654878fa5',
                'sorting' => 4,
            ],
            [
                'id'      => '_test_active_interval',
                'sorting' => 5,
            ],
        ], $result['body']['data']['banners']);
    }

    public function testInactiveWithToken(): void
    {
        $this->prepareToken();

        $result = $this->query('query {
            banner (bannerId: "' . self::INACTIVE_BANNER . '") {
                id
                active
            }
        }');

        $this->assertEquals(
            [
                'id'     => self::INACTIVE_BANNER,
                'active' => false,
            ],
            $result['body']['data']['banner']
        );
    }

    public function testGetBannersListForAdminGroupUser(): void
    {
        $this->prepareToken();

        $result = $this->query('query {
            banners {
                id,
                sorting
            }
        }');

        $this->assertSame([
            [
                'id'      => 'cb34f86f56162d0c95890b5985693710',
                'sorting' => 1,
            ],
            [
                'id'      => 'b56efaf6c93664b6dca5b1cee1f87057',
                'sorting' => 2,
            ],
            [
                'id'      => 'b5639c6431b26687321f6ce654878fa5',
                'sorting' => 4,
            ],
            [
                'id'      => '_test_active_interval',
                'sorting' => 5,
            ],
            [
                'id'      => '_test_group_banner',
                'sorting' => 6,
            ],
        ], $result['body']['data']['banners']);
    }

    public function bannerProductWithTokenProvider()
    {
        return [
            [
                'isProductActive' => false,
                'withToken'       => false,
                'expectedProduct' => null,
            ],
            [
                'isProductActive' => false,
                'withToken'       => true,
                'expectedProduct' => [
                    'id'     => self::ACTIVE_BANNER_PRODUCT,
                    'active' => false,
                ],
            ],
            [
                'isProductActive' => true,
                'withToken'       => false,
                'expectedProduct' => [
                    'id'     => self::ACTIVE_BANNER_PRODUCT,
                    'active' => true,
                ],
            ],
            [
                'isProductActive' => true,
                'withToken'       => true,
                'expectedProduct' => [
                    'id'     => self::ACTIVE_BANNER_PRODUCT,
                    'active' => true,
                ],
            ],
        ];
    }

    /**
     * @dataProvider bannerProductWithTokenProvider
     *
     * @param mixed $isProductActive
     * @param mixed $withToken
     * @param mixed $expectedProduct
     */
    public function testGetBannerProduct($isProductActive, $withToken, $expectedProduct): void
    {
        $queryBuilderFactory = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class);
        $queryBuilder = $queryBuilderFactory->create();

        $oxactive = $isProductActive ? 1 : 0;
        $queryBuilder
            ->update('oxarticles')
            ->set('oxactive', $oxactive)
            ->where('OXID = :OXID')
            ->setParameter(':OXID', self::ACTIVE_BANNER_PRODUCT)
            ->execute();

        if ($withToken) {
            $this->prepareToken();
        }

        $result = $this->query('query {
            banner(bannerId: "' . self::ACTIVE_BANNER_WITH_PRODUCT . '") {
                id
                product{
                  id
                  active
                }
            }
        }');

        $bannerProduct = $result['body']['data']['banner']['product'];
        $this->assertSame($expectedProduct, $bannerProduct);
    }

    public function bannersProductWithTokenProvider()
    {
        return [
            [
                'isProductActive' => false,
                'withToken'       => false,
                'expectedBanners' => [],
            ],
            [
                'isProductActive' => false,
                'withToken'       => true,
                'expectedBanners' => [
                    [
                        'id'      => self::ACTIVE_BANNER_WITH_PRODUCT,
                        'product' => [
                            'id'     => self::ACTIVE_BANNER_PRODUCT,
                            'active' => false,
                        ],
                    ],
                ],
            ],
            [
                'isProductActive' => true,
                'withToken'       => false,
                'expectedBanners' => [
                    [
                        'id'      => self::ACTIVE_BANNER_WITH_PRODUCT,
                        'product' => [
                            'id'     => self::ACTIVE_BANNER_PRODUCT,
                            'active' => true,
                        ],
                    ],
                ],
            ],
            [
                'isProductActive' => true,
                'withToken'       => true,
                'expectedBanners' => [
                    [
                        'id'      => self::ACTIVE_BANNER_WITH_PRODUCT,
                        'product' => [
                            'id'     => self::ACTIVE_BANNER_PRODUCT,
                            'active' => true,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider bannersProductWithTokenProvider
     *
     * @param mixed $isProductActive
     * @param mixed $withToken
     * @param mixed $expectedBanners
     */
    public function testGetBannersProduct($isProductActive, $withToken, $expectedBanners): void
    {
        $queryBuilderFactory = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class);
        $queryBuilder = $queryBuilderFactory->create();

        $oxactive = $isProductActive ? 1 : 0;
        $queryBuilder
            ->update('oxarticles')
            ->set('oxactive', $oxactive)
            ->where('OXID = :OXID')
            ->setParameter(':OXID', self::ACTIVE_BANNER_PRODUCT)
            ->execute();

        if ($withToken) {
            $this->prepareToken();
        }

        $result = $this->query('query {
            banners {
                id
                product {
                  id
                  active
                }
            }
        }');

        $banners = $result['body']['data']['banners'];

        $filteredBanners = array_values(
            array_filter($banners, function ($banner) {
                return $banner['product'] && $banner['product']['id'] === self::ACTIVE_BANNER_PRODUCT;
            })
        );

        $this->assertSame($expectedBanners, $filteredBanners);
    }
}
