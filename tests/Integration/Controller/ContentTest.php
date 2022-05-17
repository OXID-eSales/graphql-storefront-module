<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Storefront\Tests\Integration\BaseTestCase;

final class ContentTest extends BaseTestCase
{
    private const ACTIVE_CONTENT = 'e6fc3fe89d5da58da9bfcfba451fd365';

    private const INACTIVE_CONTENT = '67c5bcf75ee346bd9566bce6c8'; // credits

    private const ACTIVE_CONTENT_AGB = '2eb4676806a3d2e87.06076523'; //agb

    private const CATEGORY_RELATED_TO_ACTIVE_CONTENT = '0f4fb00809cec9aa0910aa9c8fe36751';

    public function testGetSingleActiveContent(): void
    {
        $result = $this->query(
            'query {
                content (contentId: "' . self::ACTIVE_CONTENT . '") {
                    id
                    active
                    title
                    content
                    rawContent
                    folder
                    version
                    seo {
                      url
                    }
                    category {
                      id
                      title
                    }
                }
            }'
        );

        $content = $result['body']['data']['content'];
        $this->assertSame(self::ACTIVE_CONTENT, $content['id']);
        $this->assertTrue($content['active']);
        $this->assertEquals('GraphQL content with category DE', $content['title']);
        $this->assertEquals('CMSFOLDER_CATEGORY', $content['folder']);
        $this->assertEmpty($content['version']);
        $this->assertEquals($content['category']['id'], '0f4fb00809cec9aa0910aa9c8fe36751');
        $this->assertEquals($content['category']['title'], 'Kites');
        $this->assertRegExp(
            '@https?://.*/GraphQL-content-with-category-DE/$@',
            $content['seo']['url']
        );
        $this->assertStringContainsString('Content DE', $content['content']);
        $this->assertStringContainsString('Content DE', $content['rawContent']);

        $this->assertEmpty(
            array_diff(array_keys($content), [
                'id',
                'active',
                'title',
                'content',
                'rawContent',
                'folder',
                'version',
                'seo',
                'category',
            ])
        );
    }

    public function testGetSingleActiveContentWithVersion(): void
    {
        $result = $this->query(
            'query {
                content (contentId: "' . self::ACTIVE_CONTENT_AGB . '") {
                    id
                    version
                }
            }'
        );

        $content = $result['body']['data']['content'];
        $this->assertSame(self::ACTIVE_CONTENT_AGB, $content['id']);
        $this->assertEquals(1, $content['version']);

        $this->assertEmpty(
            array_diff(array_keys($content), [
                'id',
                'version',
            ])
        );
    }

    public function testGetSingleInactiveContentWithoutToken(): void
    {
        $result = $this->query(
            'query {
                content (contentId: "' . self::INACTIVE_CONTENT . '") {
                    id
                    active
                    title
                    content
                    rawContent
                    folder
                    version
                    seo {
                      url
                    }
                    category {
                      id
                      title
                    }
                }
            }'
        );

        $this->assertSame(
            'Unauthorized',
            $result['body']['errors'][0]['message']
        );
    }

    public function testGetSingleInactiveContentWithToken(): void
    {
        $this->prepareToken();

        $result = $this->query(
            'query {
                content (contentId: "' . self::INACTIVE_CONTENT . '") {
                    id
                }
            }'
        );

        $this->assertEquals(
            [
                'id' => self::INACTIVE_CONTENT,
            ],
            $result['body']['data']['content']
        );
    }

    public function testGetSingleNonExistingContent(): void
    {
        $result = $this->query(
            'query {
                content (contentId: "DOES-NOT-EXIST") {
                    id
                }
            }'
        );

        $this->assertSame(
            'Content was not found by id: DOES-NOT-EXIST',
            $result['body']['errors'][0]['message']
        );
    }

    /**
     * @group allowed_to_fail_with_b2b
     */
    public function testGetContentListWithoutFilter(): void
    {
        $result = $this->query(
            'query {
                contents {
                    id
                }
            }'
        );

        $this->assertCount(
            49,
            $result['body']['data']['contents']
        );
    }

    /**
     * @group allowed_to_fail_with_b2b
     */
    public function testGetContentListWithAdminToken(): void
    {
        $this->prepareToken();

        $result = $this->query(
            'query {
                contents {
                    id
                }
            }'
        );

        $this->assertCount(
            50,
            $result['body']['data']['contents']
        ); //for admin token we get the inactive one as well
    }

    public function testGetContentListWithExactFilter(): void
    {
        $result = $this->query(
            'query {
                contents (filter: {
                    folder: {
                        equals: "CMSFOLDER_EMAILS"
                    }
                }) {
                    id
                }
            }'
        );

        $this->assertCount(
            25,
            $result['body']['data']['contents']
        );
    }

    public function testGetContentListWithPartialFilter(): void
    {
        $result = $this->query(
            'query {
                contents (filter: {
                    folder: {
                        contains: "FOLDER"
                    }
                }) {
                    id
                }
            }'
        );

        $this->assertCount(
            43,
            $result['body']['data']['contents']
        );
    }

    public function testGetEmptyContentListWithFilter(): void
    {
        $result = $this->query(
            'query {
                contents (filter: {
                    folder: {
                        contains: "DOES-NOT-EXIST"
                    }
                }) {
                    id
                }
            }'
        );

        $this->assertEquals(
            0,
            count($result['body']['data']['contents'])
        );
    }

    public function useTokenDataProvider()
    {
        return [
            ['withToken' => false],
            ['withToken' => true],
        ];
    }

    /**
     * @dataProvider useTokenDataProvider
     *
     * @param mixed $withToken
     */
    public function testContentCategory($withToken): void
    {
        // set category to inactive
        $this->changeCategoryActiveStatus(self::CATEGORY_RELATED_TO_ACTIVE_CONTENT, 0);

        if ($withToken) {
            $this->prepareToken();
        }

        $result = $this->query(
            'query {
                content(contentId: "' . self::ACTIVE_CONTENT . '") {
                    id
                    category {
                        active
                    }
                }
            }'
        );

        $category = $result['body']['data']['content']['category'];

        if (!$withToken) {
            $this->assertNull($category);
        } else {
            $this->assertSame(
                [
                    'active' => false,
                ],
                $category
            );
        }

        // set category back to active
        $this->changeCategoryActiveStatus(self::CATEGORY_RELATED_TO_ACTIVE_CONTENT, 1);
    }

    /**
     * @dataProvider useTokenDataProvider
     *
     * @param mixed $withToken
     */
    public function testContentsCategory($withToken): void
    {
        // set category to inactive
        $this->changeCategoryActiveStatus(self::CATEGORY_RELATED_TO_ACTIVE_CONTENT, 0);

        if ($withToken) {
            $this->prepareToken();
        }

        $result = $this->query(
            'query {
                contents (filter: {
                    folder: {
                        equals: "CMSFOLDER_CATEGORY"
                    }
                }) {
                    id
                    category {
                        id
                        active
                    }
                }
            }'
        );

        $contentCategory = $result['body']['data']['contents'][0]['category'];

        if (!$withToken) {
            $this->assertNull($contentCategory);
        } else {
            $this->assertSame(
                [
                    'id' => self::CATEGORY_RELATED_TO_ACTIVE_CONTENT,
                    'active' => false,
                ],
                $contentCategory
            );
        }

        // set category back to active
        $this->changeCategoryActiveStatus(self::CATEGORY_RELATED_TO_ACTIVE_CONTENT, 1);
    }

    private function changeCategoryActiveStatus($categoryId, $active): void
    {
        $queryBuilderFactory = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class);
        $queryBuilder = $queryBuilderFactory->create();

        $queryBuilder
            ->update('oxcategories')
            ->set('oxactive', $active)
            ->where('OXID = :OXID')
            ->setParameter(':OXID', $categoryId)
            ->execute();
    }
}
