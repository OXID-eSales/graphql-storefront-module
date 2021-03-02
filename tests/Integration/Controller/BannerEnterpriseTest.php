<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Base\Tests\Integration\MultishopTestCase;

final class BannerEnterpriseTest extends MultishopTestCase
{
    public function testGetBannersList(): void
    {
        $this->setGETRequestParameter('shp', '2');

        $result = $this->query('query {
            banners {
                id,
                title,
                sorting
            }
        }');

        $this->assertCount(2, $result['body']['data']['banners']);

        $this->assertSame([
            [
                'id'      => '_test_second_shop_banner_2',
                'title'   => 'subshop banner 2',
                'sorting' => 1,
            ],
            [
                'id'      => '_test_second_shop_banner_1',
                'title'   => 'subshop banner 1',
                'sorting' => 2,
            ],
        ], $result['body']['data']['banners']);
    }
}
