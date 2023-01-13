<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Content;

use Codeception\Example;
use Codeception\Scenario;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group content
 * @group oe_graphql_storefront
 * @group other
 */
final class ContentMultishopCest extends MultishopBaseCest
{
    private const CONTENT_ID = '1074279e67a85f5b1.96907412';

    private const CONTENT_ACTIVE_LOADID_SHOP_2 = 'oxorderinfo';

    private const CONTENT_INACTIVE_LOADID_FOR_SHOP_2 = 'oxcredits';

    private string $activeSubshopContentId = '';

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $this->activeSubshopContentId = $I->grabFromDatabase(
            'oxcontents',
            'oxid',
            [
                'oxloadid' => self::CONTENT_ACTIVE_LOADID_SHOP_2,
                'oxshopid' => 2,
            ]
        );
    }

    public function testGetActiveContentFromSameSubshopIsOk(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                content (contentId: "' . $this->activeSubshopContentId . '") {
                    id
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            $this->activeSubshopContentId,
            $result['data']['content']['id']
        );
    }

    public function testGetActiveContentFromSecondShopWillFail(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                content (contentId: "' . $this->activeSubshopContentId . '") {
                    id
                }
            }'
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Content was not found by id: ' . $this->activeSubshopContentId,
            $response['errors'][0]['message']
        );
    }

    public function testGetActiveContentFromFirstShopWillFail(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                content (contentId: "' . self::CONTENT_ID . '") {
                    id
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Content was not found by id: ' . self::CONTENT_ID,
            $response['errors'][0]['message']
        );
    }

    public function testGetContentListOfNotMainShop(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query{
                contents {
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
            48,
            $response['data']['contents']
        );
    }

    public function testGetEmptyContentListOfNotMainShop(AcceptanceTester $I): void
    {
        $this->deactivateSubshopContent($I);

        $I->sendGQLQuery(
            'query{
                contents {
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
            0,
            $response['data']['contents']
        );

        $this->restoreSubshopContent($I);
    }

    /**
     * @dataProvider providerGetContentMultilanguage
     */
    public function testGetSingleInShopActiveContentWillWork(AcceptanceTester $I, Example $data): void
    {
        $contentId = $data['shopId'] === 1 ? self::CONTENT_ID : $this->activeSubshopContentId;
        $I->sendGQLQuery(
            'query {
                content (contentId: "' . $contentId . '") {
                    id
                    title
                }
            }',
            null,
            $data['languageId'],
            $data['shopId']
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'id' => $contentId,
                'title' => $data['title'],
            ],
            $response['data']['content']
        );
    }

    protected function providerGetContentMultilanguage(): array
    {
        return [
            'shop_2_de' => [
                'shopId' => 2,
                'languageId' => 0,
                'title' => 'Wie bestellen?',
            ],
            'shop_2_en' => [
                'shopId' => 2,
                'languageId' => 1,
                'title' => 'How to order?',
            ],
            'shop_1_de' => [
                'shopId' => 1,
                'languageId' => 0,
                'title' => 'Wie bestellen?',
            ],
            'shop_1_en' => [
                'shopId' => 1,
                'languageId' => 1,
                'title' => 'How to order?',
            ],
        ];
    }

    private function deactivateSubshopContent(AcceptanceTester $I): void
    {
        $I->updateInDatabase(
            'oxcontents',
            ['oxactive' => 0],
            ['oxshopid' => 2]
        );
    }

    private function restoreSubshopContent(AcceptanceTester $I): void
    {
        $I->updateInDatabase(
            'oxcontents',
            ['oxactive' => 1],
            ['oxshopid' => 2]
        );

        $I->updateInDatabase(
            'oxcontents',
            ['oxactive' => 0],
            ['oxloadid' => self::CONTENT_INACTIVE_LOADID_FOR_SHOP_2]
        );
    }
}
