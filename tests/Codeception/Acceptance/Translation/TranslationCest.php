<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Translation;

use Codeception\Example;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group translation
 * @group oe_graphql_storefront
 * @group other_1
 */
final class TranslationCest extends BaseCest
{
    public function testTranslationsQueryDiffersByLanguage(AcceptanceTester $I): void
    {
        $I->wantToTest('Translations query is not the same for different language');

        $firstLanguage = $this->getTranslations($I, 0);
        $secondLanguage = $this->getTranslations($I, 1);

        $I->assertTrue(count($firstLanguage) > 500);
        $I->assertTrue(count($secondLanguage) > 500);

        $I->assertNotSame($firstLanguage, $secondLanguage);
    }

    /**
     * @dataProvider translationQueryDataProvider
     */
    public function testTranslationQuery(AcceptanceTester $I, Example $data): void
    {
        $I->wantToTest('Translation query gives correct results');

        $I->sendGQLQuery(
            'query($key: String!) {
                translation(key: $key) {
                    key
                    value
                }
            }',
            [
                'key' => $data['key'],
            ],
            $data['language']
        );
        $result = $I->grabJsonResponseAsArray();

        $expected = [
            'key' => $data['key'],
            'value' => $data['value'],
        ];
        $I->assertEquals($expected, $result['data']['translation']);
    }

    protected function translationQueryDataProvider(): array
    {
        return [
            [
                'language' => 1,
                'key' => 'ACCOUNT_INFORMATION',
                'value' => 'Account information',
            ],
            [
                'language' => 0,
                'key' => 'ACCOUNT_INFORMATION',
                'value' => 'Kontoinformationen',
            ],
        ];
    }

    private function getTranslations(AcceptanceTester $I, int $language): array
    {
        $I->sendGQLQuery(
            'query {
                translations {
                    key
                    value
                }
            }',
            null,
            $language
        );
        $result = $I->grabJsonResponseAsArray();

        return $result['data']['translations'];
    }
}
