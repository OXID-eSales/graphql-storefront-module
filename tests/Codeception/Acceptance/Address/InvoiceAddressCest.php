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
 * @group oe_graphql_storefront
 */
final class InvoiceAddressCest extends BaseCest
{
    private const USERNAME = 'dodo@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const USER_OXID = 'e7af1c3b786fd02906ccd75698f4e6b9';

    /**
     * @var array
     */
    private $mustFillFieldsDefault;

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $this->mustFillFieldsDefault = $I->grabConfigValueFromDatabase('aMustFillFields', 1);
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('aMustFillFields', $this->mustFillFieldsDefault['value'], 'arr');

        $I->updateInDatabase(
            'oxuser',
            [
                'OXSAL' => 'MR',
                'OXFNAME' => 'Marc',
                'OXLNAME' => 'Muster',
                'OXSTREET' => 'Hauptstr.',
                'OXSTREETNR' => '13',
                'OXZIP' => '79098',
                'OXCITY' => 'Freiburg',
                'OXCOUNTRYID' => 'a7c40f631fc920687.20179984',
                'OXSTATEID' => '',
            ],
            [
                'OXID' => self::USER_OXID,
            ]
        );
    }

    public function testInvoiceAddressForNotLoggedInUser(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
            customerInvoiceAddress {
                firstName
                lastName
            }
        }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertStringStartsWith(
            'Cannot query field "customerInvoiceAddress" on type "Query".',
            $result['errors'][0]['message']
        );
    }

    public function testInvoiceAddressForLoggedInUser(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
            customerInvoiceAddress {
                salutation
                firstName
                lastName
                company
                additionalInfo
                street
                streetNumber
                zipCode
                city
                vatID
                phone
                mobile
                fax
            }
        }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            [
                'salutation' => 'MR',
                'firstName' => 'Marc',
                'lastName' => 'Muster',
                'company' => '',
                'additionalInfo' => '',
                'street' => 'Hauptstr.',
                'streetNumber' => '13',
                'zipCode' => '79098',
                'city' => 'Freiburg',
                'vatID' => '',
                'phone' => '',
                'mobile' => '',
                'fax' => '',
            ],
            $result['data']['customerInvoiceAddress']
        );
    }

    /**
     * @dataProvider customerInvoiceAddressPartialProvider
     */
    public function testCustomerInvoiceAddressSetWithoutOptionals(AcceptanceTester $I, Example $data): void
    {
        $invoiceData = $data['invoiceData'];

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
            customerInvoiceAddressSet (
                invoiceAddress: {
                    salutation: "' . $invoiceData['salutation'] . '"
                    firstName: "' . $invoiceData['firstName'] . '"
                    lastName: "' . $invoiceData['lastName'] . '"
                    street: "' . $invoiceData['street'] . '"
                    streetNumber: "' . $invoiceData['streetNumber'] . '"
                    zipCode: "' . $invoiceData['zipCode'] . '"
                    city: "' . $invoiceData['city'] . '"
                    countryId: "' . $invoiceData['country']['id'] . '"
                }
            ){
                salutation
                firstName
                lastName
                company
                additionalInfo
                street
                streetNumber
                zipCode
                city
                country {
                    id
                    title
                }
                vatID
                phone
                mobile
                fax
            }
        }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $actual = $result['data']['customerInvoiceAddressSet'];

        $setFields = [
            'salutation',
            'firstName',
            'lastName',
            'street',
            'streetNumber',
            'zipCode',
            'city',
        ];

        foreach ($setFields as $setField) {
            $I->assertSame($invoiceData[$setField], $actual[$setField]);
        }

        $I->assertSame($invoiceData['country']['id'], $actual['country']['id']);
    }

    /**
     * @dataProvider customerInvoiceAddressValidationFailProvider
     */
    public function testCustomerInvoiceAddressSetValidationFail(AcceptanceTester $I, Example $data): void
    {
        $invoiceData = $data['invoiceData'];
        $expectedError = $data['expectedError'];

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
            customerInvoiceAddressSet (
                invoiceAddress: {
                    salutation: "' . $invoiceData['salutation'] . '"
                    firstName: "' . $invoiceData['firstName'] . '"
                    lastName: "' . $invoiceData['lastName'] . '"
                    street: "' . $invoiceData['street'] . '"
                    streetNumber: "' . $invoiceData['streetNumber'] . '"
                    zipCode: "' . $invoiceData['zipCode'] . '"
                    city: "' . $invoiceData['city'] . '"
                    countryId: "' . $invoiceData['country']['id'] . '"
                }
            ){
                salutation
                firstName
                lastName
                company
                additionalInfo
                street
                streetNumber
                zipCode
                city
                country {
                    title
                }
                vatID
                phone
                mobile
                fax
            }
        }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            $expectedError,
            $result['errors'][0]['message']
        );
    }

    /**
     * @dataProvider customerInvoiceAddressProvider
     */
    public function testCustomerInvoiceAddressSetNotLoggedIn(AcceptanceTester $I, Example $data): void
    {
        $invoiceData = $data['inputFields'];
        $queryPart = '';

        foreach ($invoiceData as $key => $value) {
            $queryPart .= $key . ': "' . $value . '",' . PHP_EOL;
        }

        $I->sendGQLQuery(
            'mutation {
            customerInvoiceAddressSet (
                invoiceAddress: {' .
            $queryPart
            . '}
            ){
                salutation
                firstName
                lastName
                company
                additionalInfo
                street
                streetNumber
                zipCode
                city
                country {
                    id
                    title
                }
                vatID
                phone
                mobile
                fax
            }
        }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertStringStartsWith(
            'Cannot query field "customerInvoiceAddressSet" on type "Mutation".',
            $result['errors'][0]['message']
        );
    }

    /**
     * @dataProvider customerInvoiceAddressProvider
     */
    public function testCustomerInvoiceAddressSet(AcceptanceTester $I, Example $data): void
    {
        $inputFields = $data['inputFields'];

        $I->login(self::USERNAME, self::PASSWORD);

        $queryPart = '';

        foreach ($inputFields as $key => $value) {
            $queryPart .= $key . ': "' . $value . '",' . PHP_EOL;
        }

        $I->sendGQLQuery(
            'mutation {
            customerInvoiceAddressSet (
                invoiceAddress: {' .
            $queryPart
            . '}
            ){
                salutation
                firstName
                lastName
                company
                additionalInfo
                street
                streetNumber
                zipCode
                city
                country {
                    id
                }
                state {
                    id
                }
                vatID
                phone
                mobile
                fax
            }
        }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $invoiceAddress = $result['data']['customerInvoiceAddressSet'];

        $countryId = $inputFields['countryId'];
        unset($inputFields['countryId']);

        $stateId = null;

        if (isset($inputFields['stateId'])) {
            $stateId = $inputFields['stateId'];
            unset($inputFields['stateId']);
        }

        foreach ($inputFields as $key => $value) {
            $I->assertSame($value, $invoiceAddress[$key]);
        }

        $I->assertSame($countryId, $invoiceAddress['country']['id']);

        if ($stateId) {
            $I->assertSame($stateId, $invoiceAddress['state']['id']);
        }
    }

    /**
     * @dataProvider providerRequiredFields
     */
    public function testSetInvoiceAddressForLoggedInUserMissingInput(AcceptanceTester $I, Example $data): void
    {
        $I->updateConfigInDatabase('aMustFillFields', serialize($data['fields']), 'arr');
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                customerInvoiceAddressSet(invoiceAddress: {' .
            '})
                {
                    salutation
                }
            }'
        );

        $expected = [];

        foreach ($data['fields'] as $field) {
            $tmp = explode('__', $field);
            $name = ltrim($tmp[1], 'ox');
            $expected[$name] = $name;
        }
        $expected = rtrim(implode(', ', $expected), ', ');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertStringContainsString($expected, $result['errors'][0]['message']);
    }

    protected function customerInvoiceAddressPartialProvider(): array
    {
        return [
            'set1' => [
                'invoiceData' => [
                    'salutation' => 'Mrs.',
                    'firstName' => 'First',
                    'lastName' => 'Last',
                    'company' => 'Invoice Company',
                    'additionalInfo' => 'Invoice address additional info',
                    'street' => 'Invoice street',
                    'streetNumber' => '123',
                    'zipCode' => '3210',
                    'city' => 'Invoice city',
                    'country' => [
                        'id' => 'a7c40f631fc920687.20179984',
                        'title' => 'Deutschland',
                    ],
                    'vatID' => '0987654321',
                    'phone' => '',
                    'mobile' => '',
                    'fax' => '12345678900',
                ],
            ],
            'set2' => [
                'invoiceData' => [
                    'salutation' => 'Mr.',
                    'firstName' => 'Invoice First',
                    'lastName' => 'Invoice Last',
                    'company' => 'Invoice Company',
                    'additionalInfo' => 'Invoice address additional info',
                    'street' => 'Another invoice street',
                    'streetNumber' => '123',
                    'zipCode' => '3210',
                    'city' => 'Another invoice city',
                    'country' => [
                        'id' => 'a7c40f6321c6f6109.43859248',
                        'title' => 'Schweiz',
                    ],
                    'vatID' => '0987654321',
                    'phone' => '',
                    'mobile' => '',
                    'fax' => '12345678900',
                ],
            ],
        ];
    }

    protected function customerInvoiceAddressValidationFailProvider(): array
    {
        return [
            'set1' => [
                'invoiceData' => [
                    'salutation' => '',
                    'firstName' => '',
                    'lastName' => '',
                    'company' => '',
                    'additionalInfo' => '',
                    'street' => '',
                    'streetNumber' => '',
                    'zipCode' => '',
                    'city' => '',
                    'country' => [
                        'id' => '',
                        'title' => '',
                    ],
                    'vatID' => '',
                    'phone' => '',
                    'mobile' => '',
                    'fax' => '',
                ],
                'expectedError' =>
                    'Invoice address is missing required fields: fname, lname, street, streetnr, zip, city, countryid',
            ],
            'set2' => [
                'invoiceData' => [
                    'salutation' => 'Mrs.',
                    'firstName' => 'First',
                    'lastName' => 'Last',
                    'company' => '',
                    'additionalInfo' => '',
                    'street' => 'Another invoice street',
                    'streetNumber' => '123',
                    'zipCode' => '3210',
                    'city' => 'Another invoice city',
                    'country' => [
                        'id' => '8f241f1109621faf8.40135556', // invalid country
                        'title' => 'Philippinen',
                    ],
                    'vatID' => '',
                    'phone' => '',
                    'mobile' => '',
                    'fax' => '',
                ],
                'expectedError' => 'Unauthorized',
            ],
            'set3' => [
                'invoiceData' => [
                    'salutation' => 'Mrs.',
                    'firstName' => null,
                    'lastName' => null,
                    'street' => null,
                    'streetNumber' => null,
                    'zipCode' => null,
                    'company' => '',
                    'additionalInfo' => '',
                    'city' => 'Another invoice city',
                    'country' => [
                        'id' => '8f241f1109621faf8.40135556', // invalid country
                        'title' => 'Philippinen',
                    ],
                ],
                'expectedError' => 'Invoice address is missing required fields: fname, lname, street, streetnr, zip',
            ],
        ];
    }

    protected function customerInvoiceAddressProvider(): array
    {
        return [
            'set1' => [
                'inputFields' => [
                    'salutation' => 'Mrs.',
                    'firstName' => 'First',
                    'lastName' => 'Last',
                    'company' => '',
                    'additionalInfo' => '',
                    'street' => 'Invoice street',
                    'streetNumber' => '123',
                    'zipCode' => '3210',
                    'city' => 'Invoice city',
                    'countryId' => 'a7c40f6321c6f6109.43859248',
                    'vatID' => '',
                    'phone' => '',
                    'mobile' => '',
                    'fax' => '',
                ],
            ],
            'set2' => [
                'inputFields' => [
                    'salutation' => 'Mr.',
                    'firstName' => 'Invoice First',
                    'lastName' => 'Invoice Last',
                    'company' => 'Invoice Company',
                    'additionalInfo' => 'Invoice address additional info',
                    'street' => 'Another invoice street',
                    'streetNumber' => '123',
                    'zipCode' => '3210',
                    'city' => 'Another invoice city',
                    'countryId' => 'a7c40f631fc920687.20179984',
                    'vatID' => '0987654321',
                    'phone' => '1234567890',
                    'mobile' => '01234567890',
                    'fax' => '12345678900',
                ],
            ],
            'set3' => [
                'inputFields' => [
                    'salutation' => 'MS',
                    'firstName' => 'Dorothy',
                    'lastName' => 'Marlowe',
                    'company' => 'Invoice Company',
                    'additionalInfo' => 'private delivery',
                    'street' => 'Moonlight Drive',
                    'streetNumber' => '41',
                    'zipCode' => '08401',
                    'city' => 'Atlantic City',
                    'countryId' => '8f241f11096877ac0.98748826',
                    'stateId' => 'NJ',
                    'phone' => '1234',
                    'fax' => '4321',
                ],
            ],
        ];
    }

    protected function providerRequiredFields()
    {
        return [
            'set1' => [
                'fields' => [
                    'oxuser__oxfname',
                    'oxuser__oxlname',
                    'oxuser__oxstreet',
                    'oxuser__oxstreetnr',
                    'oxuser__oxzip',
                    'oxuser__oxcity',
                    'oxuser__oxcountryid',
                ],
            ],
            'set2' => [
                'fields' => [
                    'oxuser__oxfname',
                    'oxuser__oxlname',
                ],
            ],
        ];
    }
}
