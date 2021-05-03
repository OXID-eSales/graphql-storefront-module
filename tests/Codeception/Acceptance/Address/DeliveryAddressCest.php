<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Address;

use Codeception\Example;
use Codeception\Scenario;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group address
 */
final class DeliveryAddressCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const DIFFERENT_USERNAME = 'differentuser@oxid-esales.com';

    private const DIFFERENT_PASSWORD = 'useruser';

    private const DEFAULT_DELIVERY_ADDRESS_ID = 'test_delivery_address';

    private const OTHER_DELIVERY_ADDRESS_ID = 'test_delivery_address_2';

    /**
     * @var array
     */
    private $mustFillFieldsDefault;

    /**
     * @var string
     */
    private $deliveryAddressId = '';

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $this->mustFillFieldsDefault = $I->grabConfigValueFromDatabase('aMustFillFields', 1);
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('aMustFillFields', $this->mustFillFieldsDefault['value'], 'arr');
    }

    public function testAddDeliveryAddressForNotLoggedInUser(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'mutation {
                customerDeliveryAddressAdd(deliveryAddress: {
                    salutation: "MR",
                    firstName: "Max",
                    lastName: "Mustermann"
                })
                {
                   firstName
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'The token is invalid',
            $result['errors'][0]['message']
        );
    }

    /**
     * @dataProvider providerRequiredFields
     */
    public function testAddDeliveryAddressForLoggedInUserMissingInput(AcceptanceTester $I, Example $data): void
    {
        $I->updateConfigInDatabase('aMustFillFields', serialize($data['fields']), 'arr');

        $prefix = 'Delivery address is missing required fields: ';

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                customerDeliveryAddressAdd(deliveryAddress: {' .
            '})
                {
                    salutation
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $expected = [];

        foreach ($data['fields'] as $field) {
            $tmp             = explode('__', $field);
            $name            = ltrim($tmp[1], 'ox');
            $expected[$name] = $name;
        }
        $expected = $prefix . rtrim(implode(', ', $expected), ', ');

        $I->assertSame($expected, $result['errors'][0]['message']);
    }

    public function testAddDeliveryAddressForLoggedInUserInvalidCountryId(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $inputFields =  [
            'salutation'     => 'MR',
            'firstName'      => 'Marc',
            'lastName'       => 'Muster',
            'company'        => 'No GmbH',
            'additionalInfo' => 'private delivery',
            'street'         => 'Bertoldstrasse',
            'streetNumber'   => '48',
            'zipCode'        => '79098',
            'city'           => 'Freiburg',
            'countryId'      => 'lalaland',
            'phone'          => '1234',
            'fax'            => '4321',
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
                    firstName
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Delivery address is missing required fields: countryid',
            $result['errors'][0]['message']
        );
    }

    public function testAddDeliveryAddressForLoggedInUserAllInputSet(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $inputFields =  [
            'salutation'     => 'MR',
            'firstName'      => 'Marc',
            'lastName'       => 'Muster',
            'company'        => 'No GmbH',
            'additionalInfo' => 'private delivery',
            'street'         => 'Bertoldstrasse',
            'streetNumber'   => '48',
            'zipCode'        => '79098',
            'city'           => 'Freiburg',
            'countryId'      => '8f241f11096877ac0.98748826',
            'stateId'        => 'NY',
            'phone'          => '1234',
            'fax'            => '4321',
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
                    salutation
                    firstName
                    lastName
                    company
                    additionalInfo
                    street
                    streetNumber
                    zipCode
                    city
                    phone
                    fax
                    country {
                        id
                    }
                    state {
                        id
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $deliveryAddress = $result['data']['customerDeliveryAddressAdd'];

        $countryId = $inputFields['countryId'];
        unset($inputFields['countryId']);

        $stateId = null;

        if (isset($inputFields['stateId'])) {
            $stateId = $inputFields['stateId'];
            unset($inputFields['stateId']);
        }

        foreach ($inputFields as $key => $value) {
            $I->assertSame($value, $deliveryAddress[$key]);
        }

        $I->assertSame($countryId, $deliveryAddress['country']['id']);

        if ($stateId) {
            $I->assertSame($stateId, $deliveryAddress['state']['id']);
        }

        $I->assertNotEmpty($deliveryAddress['id']);

        $this->deliveryAddressId = $deliveryAddress['id'];
    }

    public function testGetDeliveryAddressesForNotLoggedInUser(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {
            customerDeliveryAddresses {
                id
            }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'You need to be logged to access this field',
            $result['errors'][0]['message']
        );
    }

    /**
     * @depends testAddDeliveryAddressForLoggedInUserAllInputSet
     */
    public function testGetDeliveryAddressesForLoggedInUser(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('query {
            customerDeliveryAddresses {
                id
                firstName
                lastName
                street
                streetNumber
            }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            [
                [
                    'id'           => $this->deliveryAddressId,
                    'firstName'    => 'Marc',
                    'lastName'     => 'Muster',
                    'street'       => 'Bertoldstrasse',
                    'streetNumber' => '48',
                ],
                [
                    'id'           => self::DEFAULT_DELIVERY_ADDRESS_ID,
                    'firstName'    => 'Marc',
                    'lastName'     => 'Muster',
                    'street'       => 'Hauptstr',
                    'streetNumber' => '13',
                ],
                [
                    'id'           => self::OTHER_DELIVERY_ADDRESS_ID,
                    'firstName'    => 'Marc',
                    'lastName'     => 'Muster',
                    'street'       => 'Hauptstr2',
                    'streetNumber' => '132',
                ],
            ],
            $result['data']['customerDeliveryAddresses']
        );
    }

    /**
     * @depends testAddDeliveryAddressForLoggedInUserAllInputSet
     */
    public function testDeliveryAddressDeletionWithoutToken(AcceptanceTester $I): void
    {
        $this->deleteCustomerDeliveryAddressMutation($I, $this->deliveryAddressId);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'You need to be logged to access this field',
            $result['errors'][0]['message']
        );
    }

    /**
     * @depends testAddDeliveryAddressForLoggedInUserAllInputSet
     */
    public function testDeliveryAddressDeletionForDifferentCustomer(AcceptanceTester $I): void
    {
        $I->login(self::DIFFERENT_USERNAME, self::DIFFERENT_PASSWORD);

        $this->deleteCustomerDeliveryAddressMutation($I, $this->deliveryAddressId);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Unauthorized',
            $result['errors'][0]['message']
        );
    }

    public function testDeliveryAddressDeletionWithNonExistingId(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $this->deleteCustomerDeliveryAddressMutation($I, 'non-existing-id');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Delivery address was not found by id: non-existing-id',
            $result['errors'][0]['message']
        );
    }

    /**
     * @depends testAddDeliveryAddressForLoggedInUserAllInputSet
     */
    public function testDeliveryAddressDeletionWithToken(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $this->deleteCustomerDeliveryAddressMutation($I, $this->deliveryAddressId);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertTrue(
            $result['data']['customerDeliveryAddressDelete']
        );
    }

    public function testDeliveryAddressDeletionFromAdmin(AcceptanceTester $I): void
    {
        $this->testAddDeliveryAddressForLoggedInUserAllInputSet($I);

        $I->login('admin', 'admin');

        $this->deleteCustomerDeliveryAddressMutation($I, $this->deliveryAddressId);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertTrue(
            $result['data']['customerDeliveryAddressDelete']
        );
    }

    protected function providerRequiredFields()
    {
        return [
            'set1' => [
                'fields' => [
                    'oxaddress__oxfname',
                    'oxaddress__oxlname',
                    'oxaddress__oxstreet',
                    'oxaddress__oxstreetnr',
                    'oxaddress__oxzip',
                    'oxaddress__oxcity',
                    'oxaddress__oxcountryid',
                ],
            ],
            'set2' => [
                'fields' => [
                    'oxaddress__oxfname',
                    'oxaddress__oxlname',
                ],
            ],
        ];
    }

    protected function testAddDeliveryAddressForLoggedInUserInvalidInput(AcceptanceTester $I): void
    {
        //Test is incomplete atm (protected methods are not run in Cest)
        //'Shop is not validating the input so we mark test as incomplete until further notice.';
        $I->login(self::USERNAME, self::PASSWORD);

        $inputFields =  [
            'salutation'     => 'dual',
            'firstName'      => str_pad('?ö', 1000, '@'),
            'lastName'       => 'öäöääöä',
            'company'        => '1234',
            'additionalInfo' => str_pad('x', 1000, 'y'),
            'street'         => str_pad('x', 1000, 'z'),
            'streetNumber'   => 'is no numbeer',
            'zipCode'        => 'is no zip',
            'city'           => 'Freiburg is nice',
            'countryId'      => 'lalaland',
            'phone'          => 'fon',
            'fax'            => 'fax',
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
                    firstName
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Unauthorized',
            $result['errors'][0]['message']
        );
    }

    private function deleteCustomerDeliveryAddressMutation(AcceptanceTester $I, string $deliveryAddressId): array
    {
        $I->sendGQLQuery(
            'mutation {
                customerDeliveryAddressDelete(deliveryAddressId: "' . $deliveryAddressId . '")
            }'
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
