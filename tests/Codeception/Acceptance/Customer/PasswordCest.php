<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Customer;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group customer
 * @group oe_graphql_storefront
 */
final class PasswordCest extends BaseCest
{
    public function testChangePasswordWithoutToken(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            '
            mutation {
                customerPasswordChange(old: "foobar", new: "foobaz") {
                    refreshToken
                    accessToken
                }
            }
        '
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertStringStartsWith(
            'Cannot query field "customerPasswordChange" on type "Mutation".',
            $result['errors'][0]['message']
        );
    }

    public function testChangePasswordWithWrongOldPassword(AcceptanceTester $I): void
    {
        $I->login('admin', 'admin');

        $I->sendGQLQuery(
            '
            mutation {
                customerPasswordChange(old: "foobar", new: "foobaz") {
                    refreshToken
                    accessToken
                }
            }
        '
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Old password does not match our records',
            $result['errors'][0]['message']
        );
    }

    public function testChangePassword(AcceptanceTester $I): void
    {
        $I->login('admin', 'admin');

        $I->sendGQLQuery(
            '
            mutation {
                customerPasswordChange(old: "admin", new: "foobar") {
                    refreshToken
                    accessToken
                }
            }
        '
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertTrue(
            $result['data']['customerPasswordChange']
        );

        $I->sendGQLQuery(
            '
            mutation {
                customerPasswordChange(old: "foobar", new: "admin") {
                    refreshToken
                    accessToken
                }
            }
        '
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertTrue(
            $result['data']['customerPasswordChange']
        );
    }

    /**
     * @param AcceptanceTester $I
     * @group customer
     * @return void
     */
    public function testCustomerPasswordForgotRequest(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            '
            mutation {
                customerPasswordForgotRequest(email: "user@oxid-esales.com")
            }
        '
        );

        $I->openRecentEmail();
        $I->amGoingTo('Check Email subject');
        $title = $I->grabFromDatabase('oxshops', 'OXFORGOTPWDSUBJECT', ['oxid' => '1']);
        $I->seeInOpenedEmailSubject($title);
    }
}
