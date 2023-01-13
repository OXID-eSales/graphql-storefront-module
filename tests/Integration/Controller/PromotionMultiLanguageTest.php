<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Storefront\Tests\Integration\MultiLanguageTestCase;

final class PromotionMultiLanguageTest extends MultiLanguageTestCase
{
    private const PROMOTION_ID = 'test_active_promotion_1';

    /**
     * @return array
     */
    public function providerGetPromotionMultilanguage()
    {
        return [
            'shop_1_de' => [
                'shopId' => '1',
                'languageId' => '0',
                'title' => 'Current Promotion 1 DE',
            ],
            'shop_1_en' => [
                'shopId' => '1',
                'languageId' => '1',
                'title' => 'Current Promotion 1 EN',
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

        $result = $this->query(
            'query {
                promotion (promotionId: "' . self::PROMOTION_ID . '") {
                    id
                    title
                }
            }'
        );

        $this->assertEquals(
            [
                'id' => self::PROMOTION_ID,
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

        $result = $this->query(
            'query {
            promotion (promotionId: "' . self::PROMOTION_ID . '") {
                id
                title
            }
        }'
        );

        $this->assertEquals(
            [
                'id' => self::PROMOTION_ID,
                'title' => $title,
            ],
            $result['body']['data']['promotion']
        );
    }
}
