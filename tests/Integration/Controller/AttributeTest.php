<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;

/**
 * Class AttributeTest
 */
final class AttributeTest extends TestCase
{
    private const ATTRIBUTE_ID = '6cf89d2d73e666457d167cebfc3eb492';

    protected function setUp(): void
    {
        parent::setUp();

        $this->setGETRequestParameter(
            'lang',
            '0'
        );
    }

    public function testGetSingleAttribute(): void
    {
        $result = $this->query('query {
            attribute (id: "' . self::ATTRIBUTE_ID . '") {
                title
            }
        }');

        $attribute = $result['body']['data']['attribute'];

        $this->assertEquals(
            'Lieferumfang',
            $attribute['title']
        );
    }

    public function testGet404ForSingleNonExistingAttribute(): void
    {
        $result = $this->query('query {
            attribute (id: "DOES-NOT-EXIST") {
                title
            }
        }');

        $this->assertSame(
            'Attribute was not found by id: DOES-NOT-EXIST',
            $result['body']['errors'][0]['message']
        );
    }

    public function providerGetAttributeMultilanguage(): array
    {
        return [
            'de' => [
                'languageId' => '0',
                'title'      => 'Lieferumfang',
            ],
            'en' => [
                'languageId' => '1',
                'title'      => 'Included in delivery',
            ],
        ];
    }

    /**
     * @dataProvider providerGetAttributeMultilanguage
     */
    public function testGetAttributeMultilanguage(string $languageId, string $title): void
    {
        $query = 'query {
            attribute (id: "' . self::ATTRIBUTE_ID . '") {
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
                'title' => $title,
            ],
            $result['body']['data']['attribute']
        );
    }

    public function testAttributeList(): void
    {
        $result = $this->query('query {
            attributes {
                title
            }
        }');

        $this->assertCount(
            12,
            $result['body']['data']['attributes']
        );
    }

    public function testAttributeListWithFilter(): void
    {
        $result = $this->query('query {
            attributes(filter: {
                title: {
                    beginsWith: "a"
                }
            }) {
                title
            }
        }');

        $this->assertCount(
            1,
            $result['body']['data']['attributes']
        );
    }

    /**
     * @dataProvider providerGetAttributesMultilanguage
     *
     * @param string $languageId
     * @param array  $attributes
     */
    public function testAttributeListMultilanguage($languageId, $attributes): void
    {
        $this->setGETRequestParameter('lang', $languageId);

        $result = $this->query('query {
            attributes {
                title
            }
        }');

        foreach ($attributes as $key => $attribute) {
            $this->assertSame(
                $attribute,
                $result['body']['data']['attributes'][$key]['title']
            );
        }
    }

    public function providerGetAttributesMultilanguage(): array
    {
        return [
            'de' => [
                'languageId' => '0',
                'attributes' => [
                    'EU-Größe',
                    'Washing',
                    'Lieferumfang',
                ],
            ],
            'en' => [
                'languageId' => '1',
                'attributes' => [
                    'EU-Size',
                    'Washing',
                    'Included in delivery',
                ],
            ],
        ];
    }
}
