<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Integration\Controller;

use OxidEsales\GraphQL\Base\Tests\Integration\TokenTestCase;

final class ActionMultiLanguageTest extends TokenTestCase
{
    private const ACTIVE_ACTION_WITH_PRODUCTS = 'oxbargain';

    /**
     * @dataProvider providerGetActionWithFilterMultiLanguage
     *
     * @param string $languageId
     * @param array  $action
     */
    public function testGetSingleActiveActionMultilanguage($languageId, $action): void
    {
        $query = 'query {
            action(id: "' . self::ACTIVE_ACTION_WITH_PRODUCTS . '") {
                title
                products {
                  title
                }
            }
        }';

        $this->setGETRequestParameter('lang', $languageId);

        $result = $this->query($query);
        $this->assertResponseStatus(
            200,
            $result
        );

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
                'result'     => [
                    'title'    => 'Angebot der Woche',
                    'products' => [
                        [
                            'title' => 'Kuyichi Ledergürtel JEVER',
                        ],
                    ],
                ],
            ],
            'en' => [
                'languageId' => '1',
                'result'     => [
                    'title'    => 'Week&#039;s Special',
                    'products' => [
                        [
                            'title' => 'Kuyichi leather belt JEVER',
                        ],
                    ],
                ],
            ],
        ];
    }
}
