<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;

final class ReviewMultiLanguageTest extends TestCase
{
    /**
     * @param $languageId
     * @param $expectedLanguage
     *
     * @dataProvider multipleLanguageReviewsDataProvider
     */
    public function testMultipleLanguageReviews($languageId, $expectedLanguage): void
    {
        // Ensure we dont have shop and lang params affecting our review data
        $this->setGETRequestParameter('shp', '2');
        $this->setGETRequestParameter('lang', '1');

        $result = $this->query(
            'query {
            review(reviewId: "_test_lang_' . $languageId . '_review") {
                id
                language {
                    id
                    code
                    language
                }
            }
        }'
        );

        $review = $result['body']['data']['review'];

        $this->assertSame($expectedLanguage, $review['language']);
    }

    public function multipleLanguageReviewsDataProvider()
    {
        return [
            [
                'languageId' => 0,
                'expectedLanguage' => [
                    'id' => '0',
                    'code' => 'de',
                    'language' => 'Deutsch',
                ],
            ],
            [
                'languageId' => 1,
                'expectedLanguage' => [
                    'id' => '1',
                    'code' => 'en',
                    'language' => 'English',
                ],
            ],
        ];
    }
}
