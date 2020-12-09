<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Customer;

use Codeception\Util\HttpCode;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group customer
 */
final class PasswordCest extends BaseCest
{
    public function testChangePasswordWithoutToken(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('
            mutation {
                customerPasswordChange(old: "foobar", new: "foobaz")
            }
        ');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function testChangePasswordWithWrongOldPassword(AcceptanceTester $I): void
    {
        $I->login('admin', 'admin');

        $I->sendGQLQuery('
            mutation {
                customerPasswordChange(old: "foobar", new: "foobaz")
            }
        ');

        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
    }

    public function testChangePassword(AcceptanceTester $I): void
    {
        $I->login('admin', 'admin');

        $I->sendGQLQuery('
            mutation {
                customerPasswordChange(old: "admin", new: "foobar")
            }
        ');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->sendGQLQuery('
            mutation {
                customerPasswordChange(old: "foobar", new: "admin")
            }
        ');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }
}
