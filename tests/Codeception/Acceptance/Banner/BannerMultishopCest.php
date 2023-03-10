<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Banner;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group banner
 * @group other
 * @group oe_graphql_storefront
 */
final class BannerMultishopCest extends MultishopBaseCest
{
    public function testGetBannersList(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                banners {
                    id,
                    title,
                    sorting
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertSame(
            [
                [
                    'id' => '_test_second_shop_banner_2',
                    'title' => 'subshop banner 2',
                    'sorting' => 1,
                ],
                [
                    'id' => '_test_second_shop_banner_1',
                    'title' => 'subshop banner 1',
                    'sorting' => 2,
                ],
            ],
            $response['data']['banners']
        );
    }
}
