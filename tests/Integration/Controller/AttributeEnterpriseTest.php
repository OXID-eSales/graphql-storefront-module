<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Integration\Controller;

use OxidEsales\Eshop\Core\Element2ShopRelations;
use OxidEsales\GraphQL\Base\Tests\Integration\MultishopTestCase;

/**
 * Class AttributeEnterpriseTest
 */
final class AttributeEnterpriseTest extends MultishopTestCase
{
    private const ATTRIBUTE_ID = '6cf89d2d73e666457d167cebfc3eb492';

    /**
     * Check if attribute from shop 1 is not accessible for
     * shop 2 if its not yet related to shop 2
     */
    public function testGetSingleNotInShopAttributeWillFail(): void
    {
        $this->setGETRequestParameter('shp', '2');

        $result = $this->query('query {
            attribute (id: "' . self::ATTRIBUTE_ID . '") {
                title
            }
        }');

        $this->assertEquals(
            404,
            $result['status']
        );
    }

    /**
     * Check if attribute from shop 1 is accessible for
     * shop 2 if its related to shop 2
     */
    public function testGetSingleInShopAttributeWillWork(): void
    {
        $this->setGETRequestParameter('shp', '2');
        $this->addAttributeToShops([2]);

        $result = $this->query('query {
            attribute (id: "' . self::ATTRIBUTE_ID . '") {
                title
            }
        }');

        $this->assertEquals(
            200,
            $result['status']
        );

        $this->assertEquals(
            [
                'title' => 'Lieferumfang',
            ],
            $result['body']['data']['attribute']
        );
    }

    public function providerGetAttributeMultilanguage(): array
    {
        return [
            'shop_1_de' => [
                'shopId'     => '1',
                'languageId' => '0',
                'title'      => 'Lieferumfang',
            ],
            'shop_1_en' => [
                'shopId'     => '1',
                'languageId' => '1',
                'title'      => 'Included in delivery',
            ],
            'shop_2_de' => [
                'shopId'     => '2',
                'languageId' => '0',
                'title'      => 'Lieferumfang',
            ],
            'shop_2_en' => [
                'shopId'     => '2',
                'languageId' => '1',
                'title'      => 'Included in delivery',
            ],
        ];
    }

    /**
     * Check multishop multilanguage data is accessible
     *
     * @dataProvider providerGetAttributeMultilanguage
     */
    public function testGetSingleTranslatedSecondShopAttribute(string $shopId, string $languageId, string $title): void
    {
        $this->setGETRequestParameter('shp', $shopId);
        $this->setGETRequestParameter('lang', $languageId);
        $this->addAttributeToShops([2]);

        $result = $this->query('query {
            attribute (id: "' . self::ATTRIBUTE_ID . '") {
                title
            }
        }');

        $this->assertEquals(
            200,
            $result['status']
        );

        $this->assertEquals(
            [
                'title' => $title,
            ],
            $result['body']['data']['attribute']
        );
    }

    /**
     * @dataProvider providerGetAttributeMultishop
     *
     * @param string $languageId
     * @param array  $attributes
     */
    public function testAttributeListMultishop(string $shopId, $languageId, $attributes): void
    {
        $this->setGETRequestParameter('shp', $shopId);
        $this->setGETRequestParameter('lang', $languageId);
        $this->addAttributesToShops([2]);

        $result = $this->query('query {
            attributes {
                title
            }
        }');
        $this->assertResponseStatus(
            200,
            $result
        );

        foreach ($attributes as $key => $attribute) {
            $this->assertSame(
                $attribute,
                $result['body']['data']['attributes'][$key]['title']
            );
        }
    }

    public function providerGetAttributeMultishop(): array
    {
        return [
            'shop_1_de' => [
                'shopId'     => '1',
                'languageId' => '0',
                'attributes' => [
                    'EU-Größe',
                    'Washing',
                    'Lieferumfang',
                ],
            ],
            'shop_1_en' => [
                'shopId'     => '1',
                'languageId' => '1',
                'attributes' => [
                    'EU-Size',
                    'Washing',
                    'Included in delivery',
                ],
            ],
            'shop_2_de' => [
                'shopId'     => '2',
                'languageId' => '0',
                'attributes' => [
                    'EU-Größe',
                    'Washing',
                    'Lieferumfang',
                ],
            ],
            'shop_2_en' => [
                'shopId'     => '2',
                'languageId' => '1',
                'attributes' => [
                    'EU-Size',
                    'Washing',
                    'Included in delivery',
                ],
            ],
        ];
    }

    /**
     * @param int[] $shops
     */
    private function addAttributeToShops(array $shops): void
    {
        $oElement2ShopRelations = oxNew(Element2ShopRelations::class, 'oxattribute');
        $oElement2ShopRelations->setShopIds($shops);
        $oElement2ShopRelations->addToShop(self::ATTRIBUTE_ID);
    }

    /**
     * @param int[] $shops
     */
    private function addAttributesToShops(array $shops): void
    {
        $attributes = [
            '6b6bc9f9ab8b153d9bebc2ad6ca2aa13',
            '6b6e77de7a04de54f1aa63cfeca2f487',
            '6cf89d2d73e666457d167cebfc3eb492',
        ];

        $oElement2ShopRelations = oxNew(Element2ShopRelations::class, 'oxattribute');
        $oElement2ShopRelations->setShopIds($shops);

        foreach ($attributes as $attribute) {
            $oElement2ShopRelations->addToShop($attribute);
        }
    }
}
