<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Base\Tests\Integration\MultishopTestCase;

final class PromotionEnterpriseMultilanguageTest extends MultishopTestCase
{
    private const PROMOTION_ID = 'test_active_promotion_1';

    private const PROMOTION_SUB_SHOP_ID = 'test_active_sub_shop_promotion_1';

    /**
     * @return array
     */
    public function providerGetPromotionMultilanguage()
    {
        return [
            'shop_1_de' => [
                'shopId'     => '1',
                'languageId' => '0',
                'title'      => 'Current Promotion 1 DE',
            ],
            'shop_1_en' => [
                'shopId'     => '1',
                'languageId' => '1',
                'title'      => 'Current Promotion 1 EN',
            ],
            'shop_2_de' => [
                'shopId'     => '2',
                'languageId' => '0',
                'title'      => 'Current sub shop Promotion 1 DE',
            ],
            'shop_2_en' => [
                'shopId'     => '2',
                'languageId' => '1',
                'title'      => 'Current sub shop Promotion 1 EN',
            ],
        ];
    }

    /**
     * Check multishop multilanguage data is accessible
     *
     * @dataProvider providerGetPromotionMultilanguage
     *
     * @param mixed $shopId
     * @param mixed $languageId
     * @param mixed $title
     */
    public function testGetSingleTranslatedSecondShopPromotion($shopId, $languageId, $title): void
    {
        $this->setGETRequestParameter('shp', $shopId);
        $this->setGETRequestParameter('lang', $languageId);

        $promotionId = $this->getPromotionIdForShop($shopId);

        $result = $this->query('query {
            promotion (id: "' . $promotionId . '") {
                id
                title
            }
        }');

        $this->assertEquals(
            [
                'id'    => $promotionId,
                'title' => $title,
            ],
            $result['body']['data']['promotion']
        );
    }

    /**
     * Check multishop multilanguage data is accessible
     *
     * @dataProvider providerGetPromotionMultilanguage
     *
     * @param mixed $shopId
     * @param mixed $languageId
     * @param mixed $title
     */
    public function testGetListTranslatedSecondShopPromotions($shopId, $languageId, $title): void
    {
        $this->setGETRequestParameter('shp', $shopId);
        $this->setGETRequestParameter('lang', $languageId);

        $promotionId = $this->getPromotionIdForShop($shopId);

        $result = $this->query('query {
            promotion (id: "' . $promotionId . '") {
                id
                title
            }
        }');

        $this->assertEquals(
            [
                'id'    => $promotionId,
                'title' => $title,
            ],
            $result['body']['data']['promotion']
        );
    }

    private function getPromotionIdForShop($shopId)
    {
        return $shopId == 1 ? self::PROMOTION_ID : self::PROMOTION_SUB_SHOP_ID;
    }
}
