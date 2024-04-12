<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Promotion;

use Codeception\Example;
use Codeception\Scenario;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group promotion
 * @group other_1
 * @group oe_graphql_storefront
 */
final class PromotionCest extends BaseCest
{
    private const LANGUAGE = 1;

    private const ACTIVE_PROMOTION = 'd51dbdafb1e51b869f5d8ac233e97814';

    private const INACTIVE_PROMOTION = 'd51545e80843be666a9326783a73e91d';

    private const ADMIN_USER = 'admin';

    private const ADMIN_PASS = 'admin';

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        $I->updateInDatabase(
            'oxactions',
            [
                'oxactive' => 1,
                'oxactiveto' => '2111-10-10 00:00:00',
                'oxtitle' => 'Current Promotion DE',
                'oxtitle_1' => 'Current Promotion EN',
            ],
            [
                'oxid' => self::ACTIVE_PROMOTION,
            ]
        );
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->updateInDatabase(
            'oxactions',
            [
                'oxactive' => 0,
                'oxactiveto' => '2011-10-10 00:00:00',
                'oxtitle' => 'Current Promotion',
                'oxtitle_1' => 'Current Promotion',
            ],
            [
                'oxid' => self::ACTIVE_PROMOTION,
            ]
        );
    }

    public function testGetSingleActivePromotion(AcceptanceTester $I): void
    {
        $query =
        'query {
            promotion (promotionId: "' . self::ACTIVE_PROMOTION . '") {
                id
                active
                title
                text
            }
        }';

        $I->sendGQLQuery($query, null, self::LANGUAGE);

        $I->seeResponseIsJson();

        $response = $I->grabJsonResponseAsArray();
        $promotion = $response['data']['promotion'];

        $I->assertEmpty(
            array_diff(array_keys($promotion), [
                'id',
                'active',
                'title',
                'text',
            ])
        );

        $I->assertSame(self::ACTIVE_PROMOTION, $promotion['id']);
        $I->assertSame(true, $promotion['active']);
        $I->assertSame('Current Promotion EN', $promotion['title']);
        $I->assertNotEmpty($promotion['text']);
    }

    public function testGet401ForSingleInactivePromotion(AcceptanceTester $I): void
    {
        $query =
        'query {
            promotion (promotionId: "' . self::INACTIVE_PROMOTION . '") {
                id
                active
                title
                text
            }
        }';

        $I->sendGQLQuery($query, null, self::LANGUAGE);

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertSame('Unauthorized', $response['errors'][0]['message']);
    }

    public function testGetSingleInactivePromotionWithToken(AcceptanceTester $I): void
    {
        $query =
        'query {
            promotion (promotionId: "' . self::INACTIVE_PROMOTION . '") {
                id
                active
                title
            }
        }';

        $I->login(self::ADMIN_USER, self::ADMIN_PASS);
        $I->sendGQLQuery($query, null, self::LANGUAGE);

        $I->seeResponseIsJson();

        $response = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'id' => self::INACTIVE_PROMOTION,
                'active' => false,
                'title' => 'Upcoming Promotion',
            ],
            $response['data']['promotion']
        );
    }

    public function testGet404ForSingleNonExistingPromotion(AcceptanceTester $I): void
    {
        $query =
        'query {
            promotion (promotionId: "DOES-NOT-EXIST") {
                id
                active
                title
                text
            }
        }';
        $I->sendGQLQuery($query, null, self::LANGUAGE);

        $I->seeResponseIsJson();

        $response = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Promotion was not found by id: DOES-NOT-EXIST',
            $response['errors'][0]['message'],
        );
    }

    public function testGetPromotionListWithoutFilter(AcceptanceTester $I): void
    {
        $query =
        'query{
            promotions {
                id
                active
                title
                text
            }
        }';
        $I->sendGQLQuery($query, null, self::LANGUAGE);

        $I->seeResponseIsJson();

        $response = $I->grabJsonResponseAsArray();

        // TODO: Inactive promotions should probably be visible with valid token
        // At the moment they are not visible with any type of token.
        $I->assertCount(
            1,
            $response['data']['promotions']
        );
    }

    public function testGetPromotionListWithToken(AcceptanceTester $I): void
    {
        $query =
        'query{
            promotions {
                id
            }
        }';
        $I->sendGQLQuery($query, null, self::LANGUAGE);

        $I->seeResponseIsJson();

        $response = $I->grabJsonResponseAsArray();

        $I->assertCount(
            1,
            $response['data']['promotions']
        );
    }


    protected function dataProviderGetPromotionMultilanguage(): array
    {
        return [
            'de' => [
                'languageId' => 0,
                'title' => 'Current Promotion DE',
            ],
            'en' => [
                'languageId' => 1,
                'title' => 'Current Promotion EN',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderGetPromotionMultilanguage
     */
    public function testGetPromotionMultilanguage(AcceptanceTester $I, Example $data): void
    {
        $query =
        'query {
            promotion (promotionId: "' . self::ACTIVE_PROMOTION . '") {
                id
                title
            }
        }';
        $I->sendGQLQuery($query, null, $data['languageId']);

        $I->seeResponseIsJson();

        $response = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'id' => self::ACTIVE_PROMOTION,
                'title' => $data['title'],
            ],
            $response['data']['promotion']
        );
    }
}
