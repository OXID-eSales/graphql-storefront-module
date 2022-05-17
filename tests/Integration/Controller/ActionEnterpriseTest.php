<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Base\Tests\Integration\MultishopTestCase;

final class ActionEnterpriseTest extends MultishopTestCase
{
    public function testGetActionsList(): void
    {
        $this->setGETRequestParameter('shp', '2');

        $result = $this->query(
            'query {
                actions {
                    id,
                    title
                }
            }'
        );

        $this->assertCount(6, $result['body']['data']['actions']);

        $this->assertSame([
            [
                'id' => 'oxbargain',
                'title' => 'Angebot der Woche',
            ],
            [
                'id' => 'oxcatoffer',
                'title' => 'Kategorien-Topangebot',
            ],
            [
                'id' => 'oxnewest',
                'title' => 'Frisch eingetroffen',
            ],
            [
                'id' => 'oxnewsletter',
                'title' => 'Newsletter',
            ],
            [
                'id' => 'oxtop5',
                'title' => 'Topseller',
            ],
            [
                'id' => 'oxtopstart',
                'title' => 'Topangebot Startseite',
            ],
        ], $result['body']['data']['actions']);
    }
}
