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
final class CustomerDeleteCest extends BaseCest
{
    private const USERNAME = 'tobedeleted@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const ADMIN_USERNAME = 'admin';

    private const ADMIN_PASSWORD = 'admin';

    public function testDeleteNotLoggedInCustomer(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('mutation {
            customerDelete
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'You need to be logged to access this field',
            $result['errors'][0]['message']
        );
    }

    public function testCustomerNotAllowedToBeDeleted(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('blAllowUsersToDeleteTheirAccount', false, 'bool');

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('mutation {
            customerDelete
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Deleting your own account is not enabled by shop admin!',
            $result['errors'][0]['message']
        );
    }

    public function testCustomerMallAdminCannotBeDelete(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('blAllowUsersToDeleteTheirAccount', true, 'bool');

        $I->login(self::ADMIN_USERNAME, self::ADMIN_PASSWORD);

        $I->sendGQLQuery('mutation {
            customerDelete
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Unable to delete an account marked as mall admin!',
            $result['errors'][0]['message']
        );
    }
}
