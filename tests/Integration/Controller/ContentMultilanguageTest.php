<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;

final class ContentMultiLanguageTest extends TestCase
{
    private const ACTIVE_CONTENT = 'e6fc3fe89d5da58da9bfcfba451fd365';

    /**
     * @dataProvider providerGetContentMultiLanguage
     */
    public function testGetContentMultiLanguage(
        string $languageId,
        string $title,
        string $categoryTitle,
        string $seo
    ): void {
        $query = 'query {
            content (id: "' . self::ACTIVE_CONTENT . '") {
                id
                title
                seo {
                    url
                }
                category {
                    title
                }
            }
        }';

        $this->setGETRequestParameter(
            'lang',
            $languageId
        );

        $result = $this->query($query);
        $this->assertResponseStatus(200, $result);

        $content = $result['body']['data']['content'];
        $this->assertEquals($content['id'], self::ACTIVE_CONTENT);
        $this->assertEquals($content['title'], $title);
        $this->assertEquals($content['category']['title'], $categoryTitle);
        $this->assertRegExp('@https?://.*/' . $seo . '/$@', $content['seo']['url']);
    }

    public function providerGetContentMultiLanguage()
    {
        return [
            'de' => [
                'languageId'    => '0',
                'title'         => 'GraphQL content with category DE',
                'categoryTitle' => 'Kites',
                'seo'           => 'GraphQL-content-with-category-DE',
            ],
            'en' => [
                'languageId'    => '1',
                'title'         => 'GraphQL content with category EN',
                'categoryTitle' => 'Kites',
                'seo'           => 'GraphQL-content-with-category-EN',
            ],
        ];
    }
}
