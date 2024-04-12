<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Promotion;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group promotion
 * @group other_1
 * @group oe_graphql_storefront
 */
final class PromotionMultishopCest extends MultishopBaseCest
{
    private const PROMOTION_SUB_SHOP_ID = 'test_active_sub_shop_promotion_1';

    public function testGetPromotionFromSubShop(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                promotion (promotionId: "' . self::PROMOTION_SUB_SHOP_ID . '") {
                    id
                    active
                    title
                    text
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $promotion = $I->grabJsonResponseAsArray()['data']['promotion'];

        $I->assertSame(self::PROMOTION_SUB_SHOP_ID, $promotion['id']);
        $I->assertSame(true, $promotion['active']);
        $I->assertSame('Current sub shop Promotion 1 DE', $promotion['title']);
        $I->assertSame('Long description 1 DE', $promotion['text']);

        $I->assertEmpty(
            array_diff(array_keys($promotion), [
                'id',
                'active',
                'title',
                'text',
            ])
        );
    }

    public function testGetPromotionListFromSubShop(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query{
                promotions {
                    id
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertCount(
            2,
            $response['data']['promotions']
        );
    }
}
