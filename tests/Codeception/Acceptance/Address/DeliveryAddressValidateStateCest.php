<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Address;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group address
 * @group oe_graphql_storefront
 */
final class DeliveryAddressValidateStateCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    /**
     * This test should fail because the country and the state does not match to each other.
     * The validation should be part of the shop itself.
     * That's why this test is separated from the others.
     */
    public function testAddDeliveryAddressForLoggedInUserInvalidStateId(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $inputFields = [
            'salutation' => 'MR',
            'firstName' => 'Marc',
            'lastName' => 'Muster',
            'company' => 'No GmbH',
            'additionalInfo' => 'private delivery',
            'street' => 'Bertoldstrasse',
            'streetNumber' => '48',
            'zipCode' => '79098',
            'city' => 'Freiburg',
            'countryId' => 'a7c40f631fc920687.20179984',
            'stateId' => 'NY',
            'phone' => '1234',
            'fax' => '4321',
        ];

        $queryPart = '';

        foreach ($inputFields as $key => $value) {
            $queryPart .= $key . ': "' . $value . '",' . PHP_EOL;
        }

        $I->sendGQLQuery(
            'mutation {
                customerDeliveryAddressAdd(deliveryAddress: {' .
            $queryPart .
            '})
                {
                    id
                    country {
                        title
                    }
                    state {
                        title
                    }
                }
            }',
            null,
            1
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->sendGQLQuery(
            'mutation {
                customerDeliveryAddressDelete(deliveryAddressId: "' . $result['data']['customerDeliveryAddressAdd']['id'] . '")
            }',
            null,
            0
        );

        $country = $result['data']['customerDeliveryAddressAdd']['country'];
        $I->assertSame('Germany', $country['title']);

        $state = $result['data']['customerDeliveryAddressAdd']['state'];
        $I->assertSame('New York', $state['title']);
    }
}
