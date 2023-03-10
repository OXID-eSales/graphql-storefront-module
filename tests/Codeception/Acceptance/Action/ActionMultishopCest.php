<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Action;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group action
 * @group other
 * @group oe_graphql_storefront
 */
final class ActionMultishopCest extends MultishopBaseCest
{
    public function testGetActionsList(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                actions {
                    id,
                    title
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            [
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
            ],
            $result['data']['actions']
        );
    }
}
