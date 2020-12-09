<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Integration\Controller;

use OxidEsales\Eshop\Application\Model\Content as EshopContent;
use OxidEsales\Eshop\Core\Registry as EshopRegistry;
use OxidEsales\GraphQL\Base\Tests\Integration\MultishopTestCase;

/**
 * Class ContentEnterpriseTest
 */
final class ContentMultishopTest extends MultishopTestCase
{
    private const CONTENT_ID = '1074279e67a85f5b1.96907412';

    private const CONTENT_ID_FOR_SHOP_2 = '_subshop_2';

    protected function tearDown(): void
    {
        $this->cleanUpTable('oxcontents', 'oxid');

        parent::tearDown();
    }

    /**
     * Active content from shop 2 isaccessible for shop 2
     * We are in shop 2.
     */
    public function testGetActiveContentFromSameSubshopIsOk(): void
    {
        $this->addContentToShops([2]);
        $this->setGETRequestParameter('shp', '2');

        $result = $this->query('query {
            content (id: "' . self::CONTENT_ID_FOR_SHOP_2 . '") {
                id
            }
        }');

        $this->assertResponseStatus(200, $result);
    }

    /**
     * Active content from shop 1 is not accessible for shop 2
     * We are in shop 2.
     */
    public function testGetActiveContentFromOtherShopWillFail(): void
    {
        $this->setGETRequestParameter('shp', '2');

        $result = $this->query('query {
            content (id: "' . self::CONTENT_ID . '") {
                id
            }
        }');

        $this->assertResponseStatus(404, $result);
    }

    /**
     * Check if no contents available while they are not related to the shop 2
     */
    public function testGetEmptyContentListOfNotMainShop(): void
    {
        $this->setGETRequestParameter('shp', '2');

        $result = $this->query('query{
            contents {
                id
            }
        }');

        $this->assertResponseStatus(200, $result);
        $this->assertCount(
            0,
            $result['body']['data']['contents']
        );
    }

    /**
     * Check if active content from shop 1 is accessible for
     * shop 2 if its related to shop 2
     */
    public function testGetSingleInShopActiveContentWillWork(): void
    {
        $this->setGETRequestParameter('shp', '2');
        $this->setGETRequestParameter('lang', '0');
        $this->addContentToShops([2]);

        $result = $this->query('query {
            content (id: "' . self::CONTENT_ID_FOR_SHOP_2 . '") {
                id,
                title
            }
        }');

        $this->assertResponseStatus(200, $result);
        $this->assertEquals(
            [
                'id'    => self::CONTENT_ID_FOR_SHOP_2,
                'title' => 'Wie bestellen?',
            ],
            $result['body']['data']['content']
        );
    }

    /**
     * Check if only one, related to the shop 2 content is available in list
     */
    public function testGetOneContentInListOfNotMainShop(): void
    {
        $this->setGETRequestParameter('shp', '2');
        $this->addContentToShops([2]);

        $result = $this->query('query{
            contents {
                id
            }
        }');

        $this->assertResponseStatus(200, $result);
        $this->assertCount(
            1,
            $result['body']['data']['contents']
        );
    }

    /**
     * @return array
     */
    public function providerGetContentMultilanguage()
    {
        return [
            'shop_2_de' => [
                'shopId'     => '2',
                'languageId' => '0',
                'title'      => 'Wie bestellen?',
                'id'         => self::CONTENT_ID_FOR_SHOP_2,
            ],
            'shop_2_en' => [
                'shopId'     => '2',
                'languageId' => '1',
                'title'      => 'How to order?',
                'id'         => self::CONTENT_ID_FOR_SHOP_2,
            ],
            'shop_1_de' => [
                'shopId'     => '1',
                'languageId' => '0',
                'title'      => 'Wie bestellen?',
                'id'         => self::CONTENT_ID,
            ],
            'shop_1_en' => [
                'shopId'     => '1',
                'languageId' => '1',
                'title'      => 'How to order?',
                'id'         => self::CONTENT_ID,
            ],
        ];
    }

    /**
     * Check multishop multilanguage data is accessible
     *
     * @dataProvider providerGetContentMultilanguage
     *
     * @param mixed $shopId
     * @param mixed $languageId
     * @param mixed $title
     * @param mixed $id
     */
    public function testGetSingleTranslatedSecondShopContent($shopId, $languageId, $title, $id): void
    {
        EshopRegistry::getConfig()->setShopId($shopId);
        $this->setGETRequestParameter('shp', $shopId);
        $this->setGETRequestParameter('lang', $languageId);
        $this->addContentToShops([2]);

        $result = $this->query('query {
            content (id: "' . $id . '") {
                id
                title
            }
        }');

        $this->assertResponseStatus(200, $result);

        $this->assertEquals(
            [
                'id'    => $id,
                'title' => $title,
            ],
            $result['body']['data']['content']
        );
    }

    /**
     * Check multishop multilanguage data is accessible
     *
     * @dataProvider providerGetContentMultilanguage
     *
     * @param mixed $shopId
     * @param mixed $languageId
     * @param mixed $title
     * @param mixed $id
     */
    public function testGetListTranslatedSecondShopContents($shopId, $languageId, $title, $id): void
    {
        EshopRegistry::getConfig()->setShopId($shopId);
        $this->setGETRequestParameter('shp', $shopId);
        $this->setGETRequestParameter('lang', $languageId);
        $this->addContentToShops([2]);

        $result = $this->query('query {
            contents(filter: {
                folder: {
                    equals: "CMSFOLDER_USERINFO"
                }
            }) {
                id,
                title
            }
        }');

        $this->assertResponseStatus(200, $result);

        $this->assertEquals(
            [
                'id'    => $id,
                'title' => $title,
            ],
            $result['body']['data']['contents'][0]
        );
    }

    private function addContentToShops($shops): void
    {
        $content = oxNew(EshopContent::class);
        $content->load(self::CONTENT_ID);

        foreach ($shops as $shopId) {
            $content->setId(self::CONTENT_ID_FOR_SHOP_2);
            $content->assign([
                'oxshopid' => $shopId,
                'oxactive' => 1,
            ]);
            $content->save();
        }
    }
}
