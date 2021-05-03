<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\DataType;

use OxidEsales\Eshop\Application\Model\Article as EshopProduct;
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
            ],
            'en_seo_active' => [
                'languageId'  => '1',
                'description' => 'english seo description',
                'keywords'    => 'english seo keywords',
                'url'         => 'Kiteboarding/Kiteboards/Kiteboard-CABRINHA-CALIBER-2011.html',
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
     */
    public function testProductSeo($languageId, $description, $keywords, $url): void
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
        $this->assertStringContainsString($url, $seo->getURL());
    }
}
