<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Country;

use Codeception\Example;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group country
 * @group oe_graphql_storefront
 * @group other
 */
final class CountryEnterpriseCest extends MultishopBaseCest
{
    private const ACTIVE_COUNTRY = 'a7c40f631fc920687.20179984';

    /**
     * Check multishop multilanguage data is accessible
     *
     * @dataProvider providerGetCountryMultilanguage
     */
    public function testCountryPerShopAndLanguage(AcceptanceTester $I, Example $data): void
    {
        $shopId = (int)$data['shopId'];
        $languageId = (int)$data['languageId'];
        $title = $data['title'];

        $I->sendGQLQuery(
            'query{
                country (countryId: "' . self::ACTIVE_COUNTRY . '") {
                    id
                    title
                }
            }',
            null,
            $languageId,
            $shopId
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'id' => self::ACTIVE_COUNTRY,
                'title' => $title,
            ],
            $result['data']['country']
        );
    }

    public function testCountryListForShop2(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query{
                countries {
                    title
                    position
                }
            }',
            null,
            1,
            2
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $countries = $result['data']['countries'];
        $I->assertCount(5, $countries);

        // Test default sorting for countries
        $I->assertEquals(
            [
                ['title' => 'Germany', 'position' => 1],
                ['title' => 'United States', 'position' => 2],
                ['title' => 'Switzerland', 'position' => 3],
                ['title' => 'Austria', 'position' => 4],
                ['title' => 'United Kingdom', 'position' => 5],
            ],
            $countries
        );
    }

    public function testGetCountryListWithReversePositionSorting(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
            countries(sort: {position: "DESC"}) {
                id
            }
        }',
            null,
            1,
            2
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                ['id' => 'a7c40f632a0804ab5.18804076'],
                ['id' => 'a7c40f6320aeb2ec2.72885259'],
                ['id' => 'a7c40f6321c6f6109.43859248'],
                ['id' => '8f241f11096877ac0.98748826'],
                ['id' => 'a7c40f631fc920687.20179984'],
            ],
            $result['data']['countries']
        );
    }

    public function testGetCountryListWithTitleSorting(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
            countries(sort: {position: "", title: "ASC"}) {
                title
                position
            }
        }',
            null,
            1,
            2
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                ['title' => 'Austria', 'position' => 4],
                ['title' => 'Germany', 'position' => 1],
                ['title' => 'Switzerland', 'position' => 3],
                ['title' => 'United Kingdom', 'position' => 5],
                ['title' => 'United States', 'position' => 2],
            ],
            $result['data']['countries']
        );
    }

    protected function providerGetCountryMultilanguage()
    {
        return [
            'shop_1_de' => [
                'shopId' => '1',
                'languageId' => '0',
                'title' => 'Deutschland',
            ],
            'shop_1_en' => [
                'shopId' => '1',
                'languageId' => '1',
                'title' => 'Germany',
            ],
            'shop_2_de' => [
                'shopId' => '2',
                'languageId' => '0',
                'title' => 'Deutschland',
            ],
            'shop_2_en' => [
                'shopId' => '2',
                'languageId' => '1',
                'title' => 'Germany',
            ],
        ];
    }
}
