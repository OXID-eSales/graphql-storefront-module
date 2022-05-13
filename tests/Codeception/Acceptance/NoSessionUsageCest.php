<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group graphql_session
 * @group oe_graphql_storefront
 * @group other
 */
final class NoSessionUsageCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const PUBLIC_BASKET = '_test_basket_public';

    public function _after(AcceptanceTester $I): void
    {
        $I->logout();
    }

    public function testAccidentalSessionUsageForLoggedInUserNoSkipSession(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->queryBasket($I, false);

        //User is logged in and sends the token, so we expect to see the basket
        $I->assertEquals(self::PUBLIC_BASKET, $result['data']['basket']['id']);

        //We did not send the skipSession GET parameter but graphql makes sure no cookies are sent in response
        $sid = $I->extractSidFromResponseCookies();
        $I->assertEmpty($sid);
    }

    public function testAccidentalSessionUsageForLoggedInUserSkipSession(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->queryBasket($I);

        //User is logged in and sends the token, so we expect to see delivery costs
        $I->assertEquals(self::PUBLIC_BASKET, $result['data']['basket']['id']);

        //no cookie header
        $I->dontSeeHttpHeader('Set-Cookie');
    }

    public function testAccidentalSessionUsageForFormerlyLoggedInUser(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);
        $I->logout(); // remove the token header

        $result = $this->queryBasket($I);

        //User does not send the token, so we expect to see zero delivery costs
        $I->assertStringStartsWith(
            'You do not have sufficient rights to access this field',
            $result['errors'][0]['message']
        );

        //no cookie header
        $I->dontSeeHttpHeader('Set-Cookie');
    }

    private function queryBasket(AcceptanceTester $I, bool $skipSession = true): array
    {
        $I->sendGQLQuery(
            'query{
                basket(basketId: "' . self::PUBLIC_BASKET . '") {
                    id
                }
            }',
            [],
            0,
            1,
            $skipSession ? [] : ['skipSession' => 'false']
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
