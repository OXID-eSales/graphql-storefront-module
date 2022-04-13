<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\DataType;

use OxidEsales\Eshop\Application\Model\Article as EshopProduct;
use OxidEsales\Eshop\Application\Model\Category as EshopCategory;
use OxidEsales\Eshop\Application\Model\Manufacturer as EshopManufacturer;
use OxidEsales\Eshop\Application\Model\Vendor as EshopVendor;
use OxidEsales\Eshop\Core\Language as EshopLanguage;
use OxidEsales\Eshop\Core\Registry as EshopRegistry;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Seo;
use OxidEsales\GraphQL\Storefront\Tests\Integration\BaseTestCase;

/**
 * @covers OxidEsales\GraphQL\Storefront\Shared\DataType\Seo
 */
final class SeoTest extends BaseTestCase
{
    private const PRODUCT_ID = '058de8224773a1d5fd54d523f0c823e0';

    private const CATEGORY_ID = 'fc7e7bd8403448f00a363f60f44da8f2';

    private const MANUFACTURER_ID = 'adc6df0977329923a6330cc8f3c0a906';

    private const VENDOR_ID = 'a57c56e3ba710eafb2225e98f058d989';

    protected function setUp(): void
    {
        parent::setUp();

        EshopRegistry::set(EshopLanguage::class, oxNew(EshopLanguage::class));
    }

    public function providerProductSeo()
    {
        return [
            'de_seo_active' => [
                'languageId'  => '0',
                'description' => 'german seo description',
                'keywords'    => 'german seo keywords',
                'url'         => 'Kiteboarding/Kiteboards/Kiteboard-CABRINHA-CALIBER-2011.html',
                'path'        => '/Kiteboarding/Kiteboards/',
                'slug'        => 'Kiteboard-CABRINHA-CALIBER-2011',
            ],
            'en_seo_active' => [
                'languageId'  => '1',
                'description' => 'english seo description',
                'keywords'    => 'english seo keywords',
                'url'         => 'Kiteboarding/Kiteboards/Kiteboard-CABRINHA-CALIBER-2011.html',
                'path'        => '/en/Kiteboarding/Kiteboards/',
                'slug'        => 'Kiteboard-CABRINHA-CALIBER-2011',
            ],
        ];
    }

    /**
     * @dataProvider providerProductSeo
     *
     * @param mixed $languageId
     * @param mixed $description
     * @param mixed $keywords
     * @param mixed $url
     * @param mixed $path
     * @param mixed $slug
     */
    public function testProductSeo($languageId, $description, $keywords, $url, $path, $slug): void
    {
        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $product = oxNew(EshopProduct::class);
        $product->load(self::PRODUCT_ID);
        $seo = new Seo($product);

        $this->assertEquals($description, $seo->getDescription());
        $this->assertEquals($keywords, $seo->getKeywords());
        $this->assertEquals($path, $seo->getPath());
        $this->assertEquals(strtolower($slug), $seo->getSlug());
        $this->assertStringContainsString($url, $seo->getURL());
    }

    public function providerCategorySeo()
    {
        return [
            'de_seo_active' => [
                'languageId'  => '0',
                'path'        => '/Kiteboarding/Zubehoer/',
                'slug'        => 'zubehoer',
            ],
            'en_seo_active' => [
                'languageId'  => '1',
                'path'        => '/en/Kiteboarding/Supplies/',
                'slug'        => 'supplies',
            ],
        ];
    }

    /**
     * @dataProvider providerCategorySeo
     *
     * @param mixed $languageId
     * @param mixed $path
     * @param mixed $slug
     */
    public function testCategorySeo($languageId, $path, $slug): void
    {
        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $category = oxNew(EshopCategory::class);
        $category->load(self::CATEGORY_ID);
        $seo = new Seo($category);

        $this->assertEquals($path, $seo->getPath());
        $this->assertEquals(strtolower($slug), $seo->getSlug());
        $this->assertStringContainsString($path, $seo->getURL());
    }

    public function providerManufacturerSeo()
    {
        return [
            'de_seo_active' => [
                'languageId'  => '0',
                'path'        => '/Nach-Hersteller/Liquid-Force/',
                'slug'        => 'liquid-force',
            ],
            'en_seo_active' => [
                'languageId'  => '1',
                'path'        => '/en/By-manufacturer/Liquid-Force-Kite/',
                'slug'        => 'liquid-force-kite',
            ],
        ];
    }

    /**
     * @dataProvider providerManufacturerSeo
     *
     * @param mixed $languageId
     * @param mixed $path
     * @param mixed $slug
     */
    public function testManufacturerSeo($languageId, $path, $slug): void
    {
        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $manufacturer = oxNew(EshopManufacturer::class);
        $manufacturer->load(self::MANUFACTURER_ID);
        $seo = new Seo($manufacturer);

        $this->assertEquals($path, $seo->getPath());
        $this->assertEquals(strtolower($slug), $seo->getSlug());
        $this->assertStringContainsString($path, $seo->getURL());
    }

    public function providerVendorSeo()
    {
        return [
            'de_seo_active' => [
                'languageId'  => '0',
                'path'        => '/Nach-Lieferant/www-true-fashion-com/',
                'slug'        => 'www-true-fashion-com',
            ],
            'en_seo_active' => [
                'languageId'  => '1',
                'path'        => '/en/By-distributor/www-true-fashion-com/',
                'slug'        => 'www-true-fashion-com',
            ],
        ];
    }

    /**
     * @dataProvider providerVendorSeo
     *
     * @param mixed $languageId
     * @param mixed $path
     * @param mixed $slug
     */
    public function testVendorSeo($languageId, $path, $slug): void
    {
        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $vendor = oxNew(EshopVendor::class);
        $vendor->load(self::VENDOR_ID);
        $seo = new Seo($vendor);

        $this->assertEquals($path, $seo->getPath());
        $this->assertEquals(strtolower($slug), $seo->getSlug());
        $this->assertStringContainsString($path, $seo->getURL());
    }
}
