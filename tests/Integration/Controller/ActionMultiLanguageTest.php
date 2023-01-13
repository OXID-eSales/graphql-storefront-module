<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Storefront\Tests\Integration\MultiLanguageTestCase;

final class ActionMultiLanguageTest extends MultiLanguageTestCase
{
    private const ACTIVE_ACTION_WITH_PRODUCTS = 'oxbargain';

    /**
     * @dataProvider providerGetActionWithFilterMultiLanguage
     *
     * @param string $languageId
     * @param array $action
     */
    public function testGetSingleActiveActionMultilanguage($languageId, $action): void
    {
        $query = 'query {
            action(actionId: "' . self::ACTIVE_ACTION_WITH_PRODUCTS . '") {
                title
                products {
                  title
                }
            }
        }';

        $this->setGETRequestParameter('lang', $languageId);

        $result = $this->query($query);

        $this->assertEquals(
            $action['title'],
            $result['body']['data']['action']['title']
        );

        $this->assertEquals(
            $action['products'],
            $result['body']['data']['action']['products']
        );
    }

    public function providerGetActionWithFilterMultiLanguage(): array
    {
        return [
            'de' => [
                'languageId' => '0',
                'result' => [
                    'title' => 'Angebote der Woche',
                    'products' => [
                        [
                            'title' => 'Kite CORE GT',
                        ],
                        [
                            'title' => 'Kiteboard CABRINHA CALIBER'
                        ],
                        [
                            'title' => 'Wakeboard LIQUID FORCE GROOVE'
                        ],
                        [
                            'title' => 'Neoprenanzug NPX ASSASSIN'
                        ],
                    ],
                ],
            ],
            'en' => [
                'languageId' => '1',
                'result' => [
                    'title' => 'Week\'s Special',
                    'products' => [
                        [
                            'title' => 'Kite CORE GT',
                        ],
                        [
                            'title' => 'Kiteboard CABRINHA CALIBER'
                        ],
                        [
                            'title' => 'Wakeboard GROOVE'
                        ],
                        [
                            'title' => 'Wetsuit NPX ASSASSIN'
                        ],
                    ],
                ],
            ],
        ];
    }
}
