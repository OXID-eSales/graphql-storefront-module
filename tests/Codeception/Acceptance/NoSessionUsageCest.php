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

        $I->sendGQLQuery(
            'query{
                basket(id: "' . self::PUBLIC_BASKET . '") {
                    id
                    cost {
                        delivery {
                            price
                        }
                    }
                }
            }',
            [],
            0,
            1
        );

        //graphql only processes skipSession calls but this will be handled in shop .htaccess in rewrite rule
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        //User is logged in and sends the token, so we expect to see delivery costs
        $I->assertEquals(3.9, $result['data']['basket']['cost']['delivery']['price']);

        //We did not send the skipSession GET parameter but graphql makes sure no cookies are sent in response
        $sid = $I->extractSidFromResponseCookies();
        $I->assertEmpty($sid);
    }

    public function testAccidentalSessionUsageForLoggedInUserSkipSession(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query{
                basket(id: "' . self::PUBLIC_BASKET . '") {
                    id
                    cost {
                        delivery {
                            price
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        //User is logged in and sends the token, so we expect to see delivery costs
        $I->assertEquals(3.9, $result['data']['basket']['cost']['delivery']['price']);

        //no cookie header
        $I->dontSeeHttpHeader('Set-Cookie');
    }

    public function testAccidentalSessionUsageForFormerlyLoggedInUser(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);
        $I->logout(); // remove the token header

        $I->sendGQLQuery(
            'query{
                basket(id: "' . self::PUBLIC_BASKET . '") {
                    id
                    cost {
                        delivery {
                            price
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        //User does not send the token, so we expect to see zero delivery costs
        $I->assertEquals(0, $result['data']['basket']['cost']['delivery']['price']);

        //no cookie header
        $I->dontSeeHttpHeader('Set-Cookie');
    }
}
