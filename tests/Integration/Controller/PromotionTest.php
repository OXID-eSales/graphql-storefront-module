<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Base\Tests\Integration\TokenTestCase;

final class PromotionTest extends TokenTestCase
{
    private const ACTIVE_PROMOTION = 'test_active_promotion_1';

    private const INACTIVE_PROMOTION  = 'test_inactive_promotion_1';

    protected function setUp(): void
    {
        parent::setUp();

        $this->setGETRequestParameter(
            'lang',
            '1'
        );
    }

    public function testGetSingleActivePromotion(): void
    {
        $result = $this->query('query {
            promotion (promotionId: "' . self::ACTIVE_PROMOTION . '") {
                id
                active
                title
                text
            }
        }');

        $promotion = $result['body']['data']['promotion'];

        $this->assertSame(self::ACTIVE_PROMOTION, $promotion['id']);
        $this->assertSame(true, $promotion['active']);
        $this->assertSame('Current Promotion 1 EN', $promotion['title']);
        $this->assertSame('Long description 1 EN', $promotion['text']);

        $this->assertEmpty(array_diff(array_keys($promotion), [
            'id',
            'active',
            'title',
            'text',
        ]));
    }

    public function testGet401ForSingleInactivePromotion(): void
    {
        $result = $this->query('query {
            promotion (promotionId: "' . self::INACTIVE_PROMOTION . '") {
                id
                active
                title
                text
            }
        }');

        $this->assertSame(
            'Unauthorized',
            $result['body']['errors'][0]['message']
        );
    }

    public function testGetSingleInactivePromotionWithToken(): void
    {
        $this->prepareToken();

        $result = $this->query('query {
            promotion (promotionId: "' . self::INACTIVE_PROMOTION . '") {
                id
                active
                title
                text
            }
        }');

        $this->assertEquals(
            [
                'id'     => self::INACTIVE_PROMOTION,
                'active' => false,
                'title'  => 'Upcoming promotion EN',
                'text'   => 'Long description 3 EN',
            ],
            $result['body']['data']['promotion']
        );
    }

    public function testGet404ForSingleNonExistingPromotion(): void
    {
        $result = $this->query('query {
            promotion (promotionId: "DOES-NOT-EXIST") {
                id
                active
                title
                text
            }
        }');

        $this->assertSame(
            'Promotion was not found by id: DOES-NOT-EXIST',
            $result['body']['errors'][0]['message']
        );
    }

    public function testGetPromotionListWithoutFilter(): void
    {
        $result = $this->query('query{
            promotions {
                id
                active
                title
                text
            }
        }');

        // fixtures have 2 active promotions
        $this->assertEquals(
            2,
            count($result['body']['data']['promotions'])
        );
    }

    public function testGetPromotionListWithToken(): void
    {
        $this->prepareToken();

        $result = $this->query('query{
            promotions {
                id
            }
        }');

        // TODO: Fixtures have 2 active and 4 inactive promotions
        //       These should be visible when using a valid token, for a total of 6
        $this->assertCount(
            2,
            $result['body']['data']['promotions']
        );
    }

    public function providerGetPromotionMultilanguage()
    {
        return [
            'de' => [
                'languageId'  => '0',
                'title'       => 'Current Promotion 1 DE',
            ],
            'en' => [
                'languageId'  => '1',
                'title'       => 'Current Promotion 1 EN',
            ],
        ];
    }

    /**
     * @dataProvider providerGetPromotionMultilanguage
     */
    public function testGetPromotionMultilanguage(string $languageId, string $title): void
    {
        $query = 'query {
            promotion (promotionId: "' . self::ACTIVE_PROMOTION . '") {
                id
                title
            }
        }';

        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $result = $this->query($query);

        $this->assertEquals(
            [
                'id'    => self::ACTIVE_PROMOTION,
                'title' => $title,
            ],
            $result['body']['data']['promotion']
        );
    }
}
