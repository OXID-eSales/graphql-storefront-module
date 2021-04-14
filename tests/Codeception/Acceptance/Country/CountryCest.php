<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Country;

use Codeception\Example;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group country
 */
final class CountryCest extends BaseCest
{
    private const USERNAME = 'admin';

    private const PASSWORD = 'admin';

    private const ACTIVE_COUNTRY = 'a7c40f631fc920687.20179984';

    private const INACTIVE_COUNTRY  = 'a7c40f633038cd578.22975442';

    private const COUNTRY_WITH_STATES  = '8f241f11096877ac0.98748826';

    private const DOES_NOT_EXIST  = 'DOES-NOT-EXIST';

    public function testGetSingleActiveCountry(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {
            country (countryId: "' . self::ACTIVE_COUNTRY . '") {
                id
                position
                active
                title
                isoAlpha2
                isoAlpha3
                isoNumeric
                shortDescription
                description
                creationDate
                states {
                    id
                }
            }
        }');

        $I->seeResponseIsJson();
        $result      = $I->grabJsonResponseAsArray();
        $countryData = $result['data']['country'];

        $I->assertSame('T', substr($countryData['creationDate'], 10, 1));
        unset($countryData['creationDate']);

        $I->assertEquals(
            [
                'id'               => self::ACTIVE_COUNTRY,
                'active'           => true,
                'title'            => 'Deutschland',
                'states'           => [],
                'position'         => 1,
                'isoAlpha2'        => 'DE',
                'isoAlpha3'        => 'DEU',
                'isoNumeric'       => '276',
                'shortDescription' => 'EU1',
                'description'      => '',
            ],
            $countryData
        );
    }

    public function testGetSingleInactiveCountryWithoutToken(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {
            country (countryId: "' . self::INACTIVE_COUNTRY . '") {
                id
                active
            }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Unauthorized',
            $result['errors'][0]['message']
        );
    }

    public function testGetSingleInactiveCountryWithToken(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('query {
            country (countryId: "' . self::INACTIVE_COUNTRY . '") {
                id
                active
            }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'id'     => self::INACTIVE_COUNTRY,
                'active' => false,
            ],
            $result['data']['country']
        );
    }

    public function testGetSingleNonExistingCountry(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {
            country (countryId: "' . self::DOES_NOT_EXIST . '") {
                id
            }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('Country was not found by id: ' . self::DOES_NOT_EXIST, $result['errors']['0']['message']);
    }

    public function testGetCountryListWithoutFilter(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
            countries {
                title
                position
            }
        }',
            [],
            1
        );

        $I->seeResponseIsJson();
        $result    = $I->grabJsonResponseAsArray();
        $countries = $result['data']['countries'];

        $I->assertCount(5, $countries);

        // Test default sorting for countries
        $I->assertEquals(
            [
                ['title' => 'Germany',        'position' => 1],
                ['title' => 'United States',  'position' => 2],
                ['title' => 'Switzerland',    'position' => 3],
                ['title' => 'Austria',        'position' => 4],
                ['title' => 'United Kingdom', 'position' => 5],
            ],
            $countries
        );
    }

    public function testGetCountryListWithPartialFilter(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {
            countries(filter: {
                title: {
                    contains: "sch"
                }
            }) {
                id
                title
                position
            }
        }', [], 0);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                ['id' => 'a7c40f631fc920687.20179984', 'title' => 'Deutschland', 'position' => 1],
                ['id' => 'a7c40f6321c6f6109.43859248', 'title' => 'Schweiz',     'position' => 3],
            ],
            $result['data']['countries']
        );
    }

    public function testGetCountryListWithExactFilter(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {
            countries(filter: {
                title: {
                    equals: "Deutschland"
                }
            }) {
                id,
                title
            }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            [
                [
                    'id'    => self::ACTIVE_COUNTRY,
                    'title' => 'Deutschland',
                ],
            ],
            $result['data']['countries']
        );
    }

    public function testGetEmptyCountryListWithFilter(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {
            countries(filter: {
                title: {
                    contains: "' . self::DOES_NOT_EXIST . '"
                }
            }) {
                id
            }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertCount(
            0,
            $result['data']['countries']
        );
    }

    public function testGetCountryListWithReversePositionSorting(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {
            countries(sort: {position: "DESC"}) {
                id
            }
        }');

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
        $I->sendGQLQuery('query {
            countries(
                sort: {
                    position: ""
                    title: "ASC"
                }
            ) {
                id
            }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                ['id' => 'a7c40f631fc920687.20179984'],
                ['id' => 'a7c40f6320aeb2ec2.72885259'],
                ['id' => 'a7c40f6321c6f6109.43859248'],
                ['id' => '8f241f11096877ac0.98748826'],
                ['id' => 'a7c40f632a0804ab5.18804076'],
            ],
            $result['data']['countries']
        );
    }

    public function testGetStates(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {
            country (countryId: "' . self::COUNTRY_WITH_STATES . '") {
                states {
                    id
                    title
                }
            }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $states = $result['data']['country']['states'];

        $I->assertContains(
            [
                'id'     => 'KY',
                'title'  => 'Kentucky',
            ],
            $states
        );

        $I->assertContains(
            [
                'id'     => 'PA',
                'title'  => 'Pennsylvania',
            ],
            $states
        );
    }

    /**
     * @dataProvider dataProviderSortedStates
     */
    public function testGetStatesListWithTitleSorting(AcceptanceTester $I, Example $data): void
    {
        $sort   = $data['sortquery'];
        $method = $data['method'];

        $I->sendGQLQuery(
            'query {
                country (countryId: "' . self::COUNTRY_WITH_STATES . '") {
                    states(sort: {
                        title: "' . $sort . '"
                    }) {
                        id
                        title
                    }
                }
            }',
            [],
            1
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $sortedStates = [];

        foreach ($result['data']['country']['states'] as $state) {
            $sortedStates[$state['id']] = $state['title'];
        }

        $expected = $sortedStates;

        $method($expected, SORT_FLAG_CASE | SORT_STRING);

        $I->assertSame(
            $expected,
            $sortedStates
        );
    }

    public function testGetCountriesStates(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {
            countries {
                states {
                    title
                }
            }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertGreaterThan(
            1,
            $result['data']['countries']
        );

        $I->assertGreaterThan(
            62,
            $result['data']['countries'][0]['states']
        );
    }

    public function testCountryStatesMultilanguage(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {
            country (countryId: "' . self::COUNTRY_WITH_STATES . '") {
                states {
                    id
                    title
                }
            }
        }', [], 0);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $states = $result['data']['country']['states'];

        $I->assertContains(
            [
                'id'     => 'AS',
                'title'  => 'Amerikanisch-Samoa',
            ],
            $states
        );

        $I->assertContains(
            [
                'id'     => 'VI',
                'title'  => 'Jungferninseln',
            ],
            $states
        );
    }

    public function testGetCountriesStatesMultilanguage(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {
            countries {
                states {
                    id
                    title
                }
            }
        }', [], 0);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertGreaterThan(
            1,
            $result['data']['countries']
        );

        $I->assertContains(
            [
                'id'    => 'MP',
                'title' => 'Nördlichen Marianen',
            ],
            $result['data']['countries'][1]['states']
        );
    }

    private function dataProviderSortedStates()
    {
        return [
            'title_asc'  => [
                'sortquery' => 'ASC',
                'method'    => 'asort',
            ],
            'title_desc' => [
                'sortquery' => 'DESC',
                'method'    => 'arsort',
            ],
        ];
    }
}
