<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;

final class LinkMultilanguageTest extends TestCase
{
    private const ACTIVE_MULTILANGUAGE_LINK = 'test_active';

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
    public function testGetLinkMultilanguage(string $languageId, string $description): void
    {
        $query = 'query {
            link (id: "' . self::ACTIVE_MULTILANGUAGE_LINK . '") {
                id
                active
                timestamp
                description
                url
                creationDate
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

        $link = $result['body']['data']['link'];

        $this->assertSame(self::ACTIVE_MULTILANGUAGE_LINK, $link['id']);
        $this->assertEquals($description, $link['description']);
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

        $this->assertCount(
            $count,
            $result['body']['data']['links']
        );
    }
}
