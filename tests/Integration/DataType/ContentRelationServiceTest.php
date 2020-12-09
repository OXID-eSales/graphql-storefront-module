<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\DataType;

use OxidEsales\GraphQL\Base\Tests\Integration\TokenTestCase;

/**
 * @covers OxidEsales\GraphQL\Storefront\Content\Service\RelationService
 */
final class ContentRelationServiceTest extends TokenTestCase
{
    private const ACTIVE_CONTENT_WITH_SEO = '1074279e67a85f5b1.96907412';

    private const ACTIVE_CONTENT_WITH_CATEGORY = 'e6fc3fe89d5da58da9bfcfba451fd365';

    private const ACTIVE_CONTENT_WITHOUT_CATEGORY_1 = '2e0f674a78622c5796f9bb36f13078e2';

    private const ACTIVE_CONTENT_WITHOUT_CATEGORY_2 = 'e3ab0a5f8598f24dbb3a56b30c472844';

    public function testGetContentSeoRelation(): void
    {
        $result = $this->query('query {
            content (id: "' . self::ACTIVE_CONTENT_WITH_SEO . '") {
                id
                seo {
                    url
                }
            }
        }');

        $this->assertResponseStatus(200, $result);

        $content = $result['body']['data']['content'];
        $this->assertCount(1, $content['seo']);
        $this->assertRegExp('@https?://.*/Wie-bestellen/@', $content['seo']['url']);
    }

    public function testGetContentCategoryRelation(): void
    {
        $result = $this->query('
            query{
                content(id: "' . self::ACTIVE_CONTENT_WITH_CATEGORY . '" ){
                    category {
                        title
                    }
                }
            }
        ');

        $this->assertResponseStatus(200, $result);

        $content = $result['body']['data']['content'];
        $this->assertSame('Kites', $content['category']['title']);
    }

    public function contentIdsWithoutCategoryProvider()
    {
        return [
            [self::ACTIVE_CONTENT_WITHOUT_CATEGORY_1],
            [self::ACTIVE_CONTENT_WITHOUT_CATEGORY_2],
        ];
    }

    /**
     * @dataProvider contentIdsWithoutCategoryProvider
     */
    public function testGetContentCategoryRelationWithoutCategory(string $categoryId): void
    {
        $result = $this->query('query {
            content (id: "' . $categoryId . '") {
                category {
                    title
                }
            }
        }');

        $this->assertResponseStatus(200, $result);

        $content = $result['body']['data']['content'];
        $this->assertNull($content['category']);
    }
}
