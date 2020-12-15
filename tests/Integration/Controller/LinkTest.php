<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use DateTimeImmutable;
use DateTimeInterface;
use OxidEsales\GraphQL\Base\Tests\Integration\TokenTestCase;

final class LinkTest extends TokenTestCase
{
    private const ACTIVE_LINK = 'test_active';

    private const INACTIVE_LINK  = 'test_inactive';

    protected function setUp(): void
    {
        parent::setUp();

        $this->setGETRequestParameter(
            'lang',
            '1'
        );
    }

    public function testGetSingleActiveLink(): void
    {
        $result = $this->query('query {
            link (id: "' . self::ACTIVE_LINK . '") {
                id
                active
                timestamp
                description
                url
                creationDate
            }
        }');

        $this->assertResponseStatus(
            200,
            $result
        );

        $link = $result['body']['data']['link'];

        $this->assertSame(self::ACTIVE_LINK, $link['id']);
        $this->assertSame(true, $link['active']);
        $this->assertInstanceOf(
            DateTimeInterface::class,
            new DateTimeImmutable($link['timestamp'])
        );
        $this->assertSame('<p>English Description active</p>', $link['description']);
        $this->assertSame('http://www.oxid-esales.com', $link['url']);
        $this->assertSame('2012-06-04T07:04:54+02:00', $link['creationDate']);

        $this->assertEmpty(array_diff(array_keys($link), [
            'id',
            'active',
            'timestamp',
            'description',
            'url',
            'creationDate',
        ]));
    }

    public function testInactiveLink(): void
    {
        $result = $this->query('query {
            link (id: "' . self::INACTIVE_LINK . '") {
                id
                active
            }
        }');
        $this->assertResponseStatus(
            401,
            $result
        );
    }

    public function testInactiveLinkWithToken(): void
    {
        $this->prepareToken();

        $result = $this->query('query {
            link (id: "' . self::INACTIVE_LINK . '") {
                id
                active
            }
        }');

        $this->assertResponseStatus(
            200,
            $result
        );

        $this->assertEquals(
            [
                'id'     => 'test_inactive',
                'active' => false,
            ],
            $result['body']['data']['link']
        );
    }

    public function testGet404ForSingleNonExistingLink(): void
    {
        $result = $this->query('query {
            link (id: "DOES-NOT-EXIST") {
                id
                active
                timestamp
                description
                url
                creationDate
            }
        }');
        $this->assertResponseStatus(
            404,
            $result
        );
    }

    public function testGetLinkListWithoutFilter(): void
    {
        $result = $this->query('query{
            links {
                id
                active
                timestamp
                description
                url
                creationDate
            }
        }');
        $this->assertResponseStatus(
            200,
            $result
        );
        // fixtures have 2 active links
        $this->assertEquals(
            2,
            count($result['body']['data']['links'])
        );
    }

    public function testGetLinkListWithFilter(): void
    {
        $result = $this->query('query{
            links(filter: {
                description: {
                    contains: "a"
                }
            }){
                id
            }
        }');
        $this->assertResponseStatus(
            200,
            $result
        );
        // fixtures have 2 active links with lowercase a
        $this->assertEquals(
            2,
            count($result['body']['data']['links'])
        );
    }

    public function testGetEmptyLinkListWithFilter(): void
    {
        $result = $this->query('query{
            links(filter: {
                description: {
                    beginsWith: "inactive"
                }
            }){
                id
            }
        }');
        $this->assertResponseStatus(
            200,
            $result
        );
        // fixtures have 2 inactive links starting with inactive
        $this->assertEquals(
            0,
            count($result['body']['data']['links'])
        );
    }

    public function testGetEmptyLinkListWithExactMatchFilter(): void
    {
        $result = $this->query('query{
            links(filter: {
                description: {
                    equals: "DOES-NOT-EXIST"
                }
            }){
                id
            }
        }');
        $this->assertResponseStatus(
            200,
            $result
        );
        // fixtures have 0 links with description matching DOES-NOT-EXIST
        $this->assertEquals(
            0,
            count($result['body']['data']['links'])
        );
    }

    public function providerGetLinkMultilanguage()
    {
        return [
            'de' => [
                'languageId'  => '0',
                'description' => '<p>Deutsche Beschreibung aktiv</p>',
            ],
            'en' => [
                'languageId'  => '1',
                'description' => '<p>English Description active</p>',
            ],
        ];
    }

    /**
     * @dataProvider providerGetLinkMultilanguage
     */
    public function testGetLinkMultilanguage(string $languageId, string $title): void
    {
        $query = 'query {
            link (id: "' . self::ACTIVE_LINK . '") {
                id
                description
            }
        }';

        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $result = $this->query($query);
        $this->assertResponseStatus(
            200,
            $result
        );

        $this->assertEquals(
            [
                'id'          => self::ACTIVE_LINK,
                'description' => $title,
            ],
            $result['body']['data']['link']
        );
    }

    public function providerGetLinkListWithFilterMultilanguage()
    {
        return [
            'de' => [
                'languageId' => '0',
                'count'      => 0,
            ],
            'en' => [
                'languageId' => '1',
                'count'      => 1,
            ],
        ];
    }

    /**
     * @dataProvider providerGetLinkListWithFilterMultilanguage
     */
    public function testGetLinkListWithFilterMultilanguage(string $languageId, int $count): void
    {
        $query = 'query{
            links(filter: {
                description: {
                    contains: "English"
                }
            }){
                id
            }
        }';

        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $result = $this->query($query);
        $this->assertResponseStatus(
            200,
            $result
        );

        $this->assertEquals(
            $count,
            count($result['body']['data']['links'])
        );
    }

    public function testGetLinkListForAdminGroupUser(): void
    {
        $this->prepareToken();

        $result = $this->query('query{
            links {
                id
                active
            }
        }');

        $this->assertResponseStatus(
            200,
            $result
        );

        $this->assertEquals(
            [
                [
                    'id'     => 'ce342e8acb69f1748.25672556',
                    'active' => false,
                ],
                [
                    'id'     => 'test_active',
                    'active' => true,
                ],
                [
                    'id'     => 'test_active_2',
                    'active' => true,
                ],
                [
                    'id'     => 'test_inactive',
                    'active' => false,
                ],
                [
                    'id'     => 'test_inactive_2',
                    'active' => false,
                ],
            ],
            $result['body']['data']['links']
        );
    }
}
