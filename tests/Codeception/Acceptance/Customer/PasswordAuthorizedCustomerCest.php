<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Customer;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group customer_test
 * @group oe_graphql_storefront
 */
final class PasswordAuthorizedCustomerCest extends CustomerBaseCest
{
    public function testCustomerPasswordResetAuthorizedIfAdmin(AcceptanceTester $I): void
    {
        $I->login($this->getAdminUsername(), $this->getAdminPassword());

        $result = $this->runCustomerPasswordReset($I);
        $I->assertSame('No customer was found by update id: "testUpdateID".', $result['errors'][0]['message']);
    }

    public function testCustomerPasswordResetAuthorizedIfAgent(AcceptanceTester $I): void
    {
        $I->login($this->getAgentUsername(), $this->getAgentPassword());

        $result = $this->runCustomerPasswordReset($I);
        $I->assertSame('No customer was found by update id: "testUpdateID".', $result['errors'][0]['message']);
    }

    private function runCustomerPasswordReset(AcceptanceTester $I): array
    {
        $I->sendGQLQuery(
            'mutation q($updateId: String!, $newPassword: String!, $repeatPassword: String!){
                customerPasswordReset(updateId: $updateId, newPassword: $newPassword, repeatPassword: $repeatPassword)
            }',
            [
                'updateId' => 'testUpdateID',
                'newPassword' => 'testPassword',
                'repeatPassword' => 'testPassword'
            ]
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
