<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Customer;

use Codeception\Example;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group customer
 * @group oe_graphql_storefront
 */
final class CustomerCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const USER_OXID = 'e7af1c3b786fd02906ccd75698f4e6b9';

    private const EXISTING_USERNAME = 'existinguser@oxid-esales.com';

    private const OTHER_PASSWORD = 'useruser';

    private const EXISTING_USER_ID = '9119cc8cd9593c214be93ee558235f3c';

    private const SUBSCRIPTION_ID = '_subscription_id';

    private const USERNAME_FOR_EMAIL_CHANGE = 'foremailchangeCE@oxid-esales.com';

    public function _after(AcceptanceTester $I): void
    {
        $I->logout();

        $I->deleteFromDatabase(
            'oxnewssubscribed',
            [
                'OXID LIKE' => '_%',
                'OXUSERID'  => self::EXISTING_USERNAME,
            ]
        );
    }

    public function testCustomerForNotLoggedInUser(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                customer {
                   id
                   firstName
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertStringStartsWith(
            'Cannot query field "customer" on type "Query".',
            $result['errors'][0]['message']
        );
    }

    public function testCustomerForLoggedInUser(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                customer {
                   id
                   firstName
                   lastName
                   email
                   customerNumber
                   birthdate
                   points
                   registered
                   created
                   updated
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $customerData = $result['data']['customer'];

        $I->assertEquals(self::USER_OXID, $customerData['id']);
        $I->assertEquals('Marc', $customerData['firstName']);
        $I->assertEquals('Muster', $customerData['lastName']);
        $I->assertEquals(self::USERNAME, $customerData['email']);
        $I->assertEquals('2', $customerData['customerNumber']);
        $I->assertSame(0, $customerData['points']);
        $I->assertSame('1984-12-21T00:00:00+01:00', $customerData['birthdate']);
        $I->assertSame('2011-02-01T08:41:25+01:00', $customerData['registered']);
        $I->assertSame('2011-02-01T08:41:25+01:00', $customerData['created']);
        $I->assertInstanceOf(DateTime::class, DateTime::createFromFormat(DateTime::ATOM, $customerData['updated']));
    }

    public function testCustomerNewsletterStatusNoEntryInDatabase(AcceptanceTester $I): void
    {
        $I->login(self::EXISTING_USERNAME, self::OTHER_PASSWORD);

        $I->sendGQLQuery(
            'query {
            customer {
                id
                firstName
                newsletterStatus {
                    status
                }
            }
        }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame('Eleanor', $result['data']['customer']['firstName']);
        $I->assertNull($result['data']['customer']['newsletterStatus']);
    }

    public function testCustomerNewsletterStatusInvalidEntryInDatabase(AcceptanceTester $I): void
    {
        $this->prepareTestData($I);

        $I->login(self::EXISTING_USERNAME, self::OTHER_PASSWORD);

        $I->sendGQLQuery(
            'query {
            customer {
                id
                firstName
                newsletterStatus {
                    status
                }
            }
        }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame('UNSUBSCRIBED', $result['data']['customer']['newsletterStatus']['status']);
    }

    public function testCustomerAndNewsletterStatusForUser(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
            customer {
                id
                firstName
                newsletterStatus {
                    salutation
                    firstName
                    lastName
                    email
                    status
                    failedEmailCount
                    subscribed
                    unsubscribed
                    updated
                }
            }
        }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $expected = [
            'salutation'       => 'MR',
            'firstName'        => 'Marc',
            'lastName'         => 'Muster',
            'email'            => self::USERNAME,
            'status'           => 'SUBSCRIBED',
            'failedEmailCount' => 0,
            'subscribed'       => '2020-04-01T11:11:11+02:00',
            'unsubscribed'     => null,
        ];

        $I->assertContains('T', $result['data']['customer']['newsletterStatus']['updated']);
        unset($result['data']['customer']['newsletterStatus']['updated']);

        $I->assertEquals(
            $expected,
            $result['data']['customer']['newsletterStatus']
        );
    }

    /**
     * @dataProvider dataProviderSuccessfulCustomerRegister
     */
    public function testSuccessfulCustomerRegister(AcceptanceTester $I, Example $data): void
    {
        $email     = $data['email'];
        $password  = $data['password'];
        $birthdate = $data['birthdate'];

        $I->sendGQLQuery(
            'mutation {
            customerRegister(customer: {
                email: "' . $email . '",
                password: "' . $password . '",
                ' . ($birthdate ? 'birthdate: "' . $birthdate . '"' : '') . '
            }) {
                id
                email
                birthdate
            }
        }',
            []
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $customerData = $result['data']['customerRegister'];
        $I->assertNotEmpty($customerData['id']);
        $I->assertSame($email, $customerData['email']);

        if ($birthdate) {
            $I->assertInstanceOf(
                DateTimeInterface::class,
                new DateTimeImmutable($customerData['birthdate'])
            );

            $I->assertSame(
                $birthdate . 'T00:00:00+01:00',
                $customerData['birthdate']
            );
        }

        $I->seeInDatabase(
            'oxobject2group',
            [
                'oxobjectid' => $customerData['id'],
                'oxgroupsid' => 'oxidnotyetordered',
            ]
        );
    }

    /**
     * @dataProvider dataProviderFailedCustomerRegistration
     */
    public function testFailedCustomerRegistration(AcceptanceTester $I, Example $data): void
    {
        $email    = $data['email'];
        $password = $data['password'];
        $message  = $data['message'];

        $I->sendGQLQuery(
            'mutation {
            customerRegister(customer: {
                email: "' . $email . '",
                password: "' . $password . '"
            }) {
                id
                email
                birthdate
            }
        }',
            []
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame($message, $result['errors'][0]['message']);
    }

    /**
     * @dataProvider dataProviderCustomerEmailUpdate
     */
    public function testCustomerEmailUpdate(AcceptanceTester $I, Example $data): void
    {
        $email          = $data['email'];
        $expectedError  = $data['expectedError'];

        $I->login(self::USERNAME_FOR_EMAIL_CHANGE, 'useruser');

        $I->sendGQLQuery(
            'mutation {
                customerEmailUpdate(email: "' . $email . '") {
                    id
                    email
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        if ($expectedError) {
            $I->assertSame($expectedError, $result['errors'][0]['message']);
        } else {
            $customerData = $result['data']['customerEmailUpdate'];

            $I->assertNotEmpty($customerData['id']);
            $I->assertSame($email, $customerData['email']);
        }
    }

    public function testCustomerBirthdateUpdateWithoutToken(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            '
            mutation {
                customerBirthdateUpdate(birthdate: "1986-12-25") {
                    email
                    birthdate
                }
            }
        '
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertStringStartsWith(
            'Cannot query field "customerBirthdateUpdate" on type "Mutation".',
            $result['errors'][0]['message']
        );
    }

    public function testCustomerBirthdateUpdate(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                customerBirthdateUpdate(birthdate: "1986-12-25") {
                    email
                    birthdate
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'email'     => self::USERNAME,
                'birthdate' => '1986-12-25T00:00:00+01:00',
            ],
            $result['data']['customerBirthdateUpdate']
        );
    }

    public function testBaskets(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                customer {
                    baskets {
                        id
                        public
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $baskets = $result['data']['customer']['baskets'];
        $I->assertEquals(6, count($baskets));

        $I->sendGQLQuery(
            'mutation {
                basketCreate(basket: {title: "noticelist", public: false}) {
                    id
                }
            }'
        );

        $I->seeResponseIsJson();
        $resultBasketCreate = $I->grabJsonResponseAsArray();

        $noticeListId = $resultBasketCreate['data']['basketCreate']['id'];

        $I->sendGQLQuery(
            'query {
                customer {
                    baskets {
                        id
                        public
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $baskets = $result['data']['customer']['baskets'];
        $I->assertEquals(7, count($baskets));

        $I->sendGQLQuery(
            'mutation {
                basketMakePublic(basketId: "' . $noticeListId . '")
            }'
        );

        $I->sendGQLQuery(
            'query {
                customer {
                    baskets {
                        id
                        public
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $baskets = $result['data']['customer']['baskets'];
        $I->assertEquals(7, count($baskets));

        $I->sendGQLQuery(
            'mutation {
                basketRemove(basketId: "' . $noticeListId . '")
            }'
        );
    }

    protected function dataProviderSuccessfulCustomerRegister()
    {
        return [
            [
                'email'     => 'testUser1@oxid-esales.com',
                'password'  => 'useruser',
                'birthdate' => null,
            ],
            [
                'email'     => 'testUser2@oxid-esales.com',
                'password'  => 'useruser',
                'birthdate' => null,
            ],
            [
                'email'     => 'testUser3@oxid-esales.com',
                'password'  => 'useruser',
                'birthdate' => '1986-12-25',
            ],
        ];
    }

    protected function dataProviderFailedCustomerRegistration()
    {
        return [
            [
                'email'    => 'testUser1',
                'password' => 'useruser',
                'message'  => "This e-mail address 'testUser1' is invalid!",
            ],
            [
                'email'    => 'user@oxid-esales.com',
                'password' => 'useruser',
                'message'  => "This e-mail address 'user@oxid-esales.com' already exists!",
            ],
            [
                'email'    => 'testUser3@oxid-esales.com',
                'password' => '',
                'message'  => 'Password does not match length requirements',
            ],
            [
                'email'    => '',
                'password' => 'useruser',
                'message'  => 'The e-mail address must not be empty!',
            ],
        ];
    }

    protected function dataProviderCustomerEmailUpdate()
    {
        return [
            [
                'email'          => 'user@oxid-esales.com',
                'expectedError'  => "This e-mail address 'user@oxid-esales.com' already exists!",
            ],
            [
                'email'          => '',
                'expectedError'  => 'The e-mail address must not be empty!',
            ],
            [
                'email'          => 'someuser',
                'expectedError'  => "This e-mail address 'someuser' is invalid!",
            ],
            [
                'email'          => 'newCustUser@oxid-esales.com',
                'expectedError'  => null,
            ],
        ];
    }

    private function prepareTestData(AcceptanceTester $I, int $optin = 2): void
    {
        $I->haveInDatabase(
            'oxnewssubscribed',
            [
                'OXID'           => self::SUBSCRIPTION_ID,
                'OXSUBSCRIBED'   => '2020-04-01 12:12:12',
                'OXUNSUBSCRIBED' => '1980-01-01 00:00:00',
            ]
        );

        $I->updateInDatabase(
            'oxnewssubscribed',
            [
                'OXUSERID'     => self::EXISTING_USER_ID,
                'OXDBOPTIN'    => 6,
                'OXEMAIL'      => self::EXISTING_USERNAME,
                'OXFNAME'      => 'Marc',
                'OXLNAME'      => 'Muster',
                'OXSUBSCRIBED' => '2020-04-01 12:12:12',
            ],
            [
                'OXID' => self::SUBSCRIPTION_ID,
            ]
        );
    }
}
