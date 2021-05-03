<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\Eshop\Core\Element2ShopRelations;
use OxidEsales\GraphQL\Base\Tests\Integration\MultishopTestCase;

/**
 * Class LinkEnterpriseTest
 */
final class LinkEnterpriseTest extends MultishopTestCase
{
    private const LINK_ID = 'test_active';

    /**
     * Check if active link from shop 1 is not accessible for
     * shop 2 if its not yet related to shop 2
     */
    public function testGetSingleNotInShopActiveLinkWillFail(): void
    {
        $this->setGETRequestParameter('shp', '2');

        $result = $this->query('query {
            link (linkId: "' . self::LINK_ID . '") {
                id
            }
        }');

        $this->assertSame(
            'Link was not found by id: ' . self::LINK_ID,
            $result['body']['errors'][0]['message']
        );
    }

    /**
     * Check if no links available while they are not related to the shop 2
     */
    public function testGetEmptyLinkListOfNotMainShop(): void
    {
        $this->setGETRequestParameter('shp', '2');

        $result = $this->query('query{
            links {
                id
            }
        }');

        // fixtures have 2 active links
        $this->assertCount(
            0,
            $result['body']['data']['links']
        );
    }

    /**
     * Check if active link from shop 1 is accessible for
     * shop 2 if its related to shop 2
     */
    public function testGetSingleInShopActiveLinkWillWork(): void
    {
        $this->setGETRequestParameter('shp', '2');
        $this->setGETRequestParameter('lang', '1');
        $this->addLinkToShops([2]);

        $result = $this->query('query {
            link (linkId: "' . self::LINK_ID . '") {
                id,
                description
            }
        }');

        $this->assertEquals(
            [
                'id'          => self::LINK_ID,
                'description' => '<p>English Description active</p>',
            ],
            $result['body']['data']['link']
        );
    }

    /**
     * Check if only one, related to the shop 2 link is available in list
     */
    public function testGetOneLinkInListOfNotMainShop(): void
    {
        $this->setGETRequestParameter('shp', '2');
        $this->addLinkToShops([2]);

        $result = $this->query('query{
            links {
                id
            }
        }');

        // fixtures have 1 active link for shop 2
        $this->assertCount(
            1,
            $result['body']['data']['links']
        );
    }

    /**
     * @return array
     */
    public function providerGetLinkMultilanguage()
    {
        return [
            'shop_1_de' => [
                'shopId'      => '1',
                'languageId'  => '0',
                'description' => '<p>Deutsche Beschreibung aktiv</p>',
            ],
            'shop_1_en' => [
                'shopId'      => '1',
                'languageId'  => '1',
                'description' => '<p>English Description active</p>',
            ],
            'shop_2_de' => [
                'shopId'      => '2',
                'languageId'  => '0',
                'description' => '<p>Deutsche Beschreibung aktiv</p>',
            ],
            'shop_2_en' => [
                'shopId'      => '2',
                'languageId'  => '1',
                'description' => '<p>English Description active</p>',
            ],
        ];
    }

    /**
     * Check multishop multilanguage data is accessible
     *
     * @dataProvider providerGetLinkMultilanguage
     *
     * @param mixed $shopId
     * @param mixed $languageId
     * @param mixed $description
     */
    public function testGetSingleTranslatedSecondShopLink($shopId, $languageId, $description): void
    {
        $this->setGETRequestParameter('shp', $shopId);
        $this->setGETRequestParameter('lang', $languageId);

        $this->addLinkToShops([$shopId]);

        $result = $this->query('query {
            link (linkId: "' . self::LINK_ID . '") {
                id
                description
            }
        }');

        $this->assertEquals(
            [
                'id'          => self::LINK_ID,
                'description' => $description,
            ],
            $result['body']['data']['link']
        );
    }

    /**
     * Check multishop multilanguage data is accessible
     *
     * @dataProvider providerGetLinkMultilanguage
     *
     * @param mixed $shopId
     * @param mixed $languageId
     * @param mixed $description
     */
    public function testGetListTranslatedSecondShopLinks($shopId, $languageId, $description): void
    {
        $this->setGETRequestParameter('shp', $shopId);
        $this->setGETRequestParameter('lang', $languageId);
        $this->addLinkToShops([2]);

        $result = $this->query('query {
            links(filter: {
                description: {
                    equals: "' . $description . '"
                }
            }) {
                id,
                description
            }
        }');

        $this->assertEquals(
            [
                'id'          => self::LINK_ID,
                'description' => $description,
            ],
            $result['body']['data']['links'][0]
        );
    }

    private function addLinkToShops($shops): void
    {
        $oElement2ShopRelations = oxNew(Element2ShopRelations::class, 'oxlinks');
        $oElement2ShopRelations->setShopIds($shops);
        $oElement2ShopRelations->addToShop(self::LINK_ID);
    }
}
