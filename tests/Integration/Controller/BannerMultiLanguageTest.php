<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Base\Tests\Integration\TokenTestCase;

final class BannerMultiLanguageTest extends TokenTestCase
{
    private const ACTIVE_BANNER_WITH_PRODUCT = 'b5639c6431b26687321f6ce654878fa5';

    private const ACTIVE_BANNER_WITHOUT_PRODUCT = 'cb34f86f56162d0c95890b5985693710';

    public function testGetSingleActiveBannerWithProductMultilanguage(): void
    {
        $query = 'query {
            banner(id: "' . self::ACTIVE_BANNER_WITH_PRODUCT . '") {
                title
                link
                product{
                  id
                  title
                }
            }
        }';

        $this->setGETRequestParameter(
            'lang',
            '1'
        );

        $result = $this->query($query);

        $banner = $result['body']['data']['banner'];
        $this->assertSame('Banner 1 en', $banner['title']);
        $this->assertMatchesRegularExpression(
            '@https?://.*/en/Gear/Sportswear/Neoprene/Suits/Wetsuit-NPX-ASSASSIN.html$@',
            $banner['link']
        );

        $this->assertSame(
            [
                'id'    => 'f4fc98f99e3660bd2ecd7450f832c41a',
                'title' => 'Wetsuit NPX ASSASSIN',
            ],
            $banner['product']
        );
    }

    public function testGetSingleActiveBannerWithoutProductMultilanguage(): void
    {
        $query = 'query {
            banner(id: "' . self::ACTIVE_BANNER_WITHOUT_PRODUCT . '") {
                title
                link
                product{
                  id
                  title
                }
            }
        }';

        $this->setGETRequestParameter(
            'lang',
            '1'
        );

        $result = $this->query($query);

        $banner = $result['body']['data']['banner'];
        $this->assertSame('Banner 4 en', $banner['title']);
        $this->assertMatchesRegularExpression(
            '@https?://.*/Wakeboarding/Wakeboards/.*?$@',
            $banner['link']
        );

        $this->assertNull($banner['product']);
    }
}
