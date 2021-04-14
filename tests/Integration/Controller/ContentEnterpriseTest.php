<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\Eshop\Application\Model\Content as EshopContent;
use OxidEsales\GraphQL\Base\Tests\Integration\EnterpriseTestCase;

/**
 * Class ContentEnterpriseTest
 */
final class ContentEnterpriseTest extends EnterpriseTestCase
{
    private const CONTENT_ID = '1074279e67a85f5b1.96907412';

    private const CONTENT_ID_FOR_SHOP_2 = '_subshop_2';

    /**
     * Active content from shop 1 is accessible for shop 1.
     * We are in shop 1.
     */
    public function testGetActiveContentFromSameShopIsOk(): void
    {
        $this->setGETRequestParameter('shp', '1');

        $result = $this->query('query {
            content (contentId: "' . self::CONTENT_ID . '") {
                id
            }
        }');

        $this->assertSame(
            self::CONTENT_ID,
            $result['body']['data']['content']['id']
        );
    }

    /**
     * Active content from shop 2 is not accessible for shop 1
     * We are in shop 1.
     */
    public function testGetActiveContentFromOtherShopWillFail(): void
    {
        //we have no shop 2 in this test but only database entry is needed for content
        $this->addContentToShops([2]);

        $this->setGETRequestParameter('shp', '1');

        $result = $this->query('query {
            content (contentId: "' . self::CONTENT_ID_FOR_SHOP_2 . '") {
                id
            }
        }');

        $this->assertSame(
            'Content was not found by id: ' . self::CONTENT_ID_FOR_SHOP_2,
            $result['body']['errors'][0]['message']
        );
    }

    /**
     * @return array
     */
    public function providerGetContentMultilanguage()
    {
        return [
            'shop_1_de' => [
                'languageId' => '0',
                'title'      => 'Wie bestellen?',
                'id'         => self::CONTENT_ID,
            ],
            'shop_1_en' => [
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
     * @param mixed $languageId
     * @param mixed $title
     * @param mixed $id
     */
    public function testGetSingleTranslatedContent($languageId, $title, $id): void
    {
        $this->setGETRequestParameter('shp', '1');
        $this->setGETRequestParameter('lang', $languageId);
        $this->addContentToShops([2]);

        $result = $this->query('query {
            content (contentId: "' . $id . '") {
                id
                title
            }
        }');

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
     * @param mixed $languageId
     * @param mixed $title
     * @param mixed $id
     */
    public function testGetListTranslatedContents($languageId, $title, $id): void
    {
        $this->setGETRequestParameter('shp', '1');
        $this->setGETRequestParameter('lang', $languageId);

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
