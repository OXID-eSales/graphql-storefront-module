<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Customer;

use Codeception\Example;
use Datetime;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group customer
 */
final class CustomerMultiShopCest extends MultishopBaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const OTHER_PASSWORD = 'useruser';

    private const BOTH_SHOPS_USERNAME = 'multishopuser@oxid-esales.com';

    private const PRIMARY_SHOP_PASSWORD = 'useruser';

    private const SUBSHOP_USER_ID = '_09db395b6c85c3881fcb9b437a73hh9';

    private const USERNAME_FOR_EMAIL_CHANGE = 'foremailchange@oxid-esales.com';

    /**
     * @dataProvider dataProviderCustomerNewsletterPerShop
     */
    public function testCustomerNewsletterPerShopForMallUser(AcceptanceTester $I, Example $data): void
    {
        $shopId   = $data['shopId'];
        $expected = $data['expected'];

        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD, $shopId);

        $I->sendGQLQuery(
            'query {
                customer {
                    newsletterStatus {
                        status
                    }
                }
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame($expected, $result['data']['customer']['newsletterStatus']['status']);
    }

    public function testCustomerExistingInBothShopsLoggedIntoSecondaryShop(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', false, 'bool', [1, 2]);

        $I->login(self::BOTH_SHOPS_USERNAME, self::PRIMARY_SHOP_PASSWORD, 2);

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
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $customerData = $result['data']['customer'];

        $I->assertEquals(self::SUBSHOP_USER_ID, $customerData['id']);
        $I->assertEquals('Marc', $customerData['firstName']);
        $I->assertEquals('Muster', $customerData['lastName']);
        $I->assertEquals(self::BOTH_SHOPS_USERNAME, $customerData['email']);
        $I->assertEquals('8', $customerData['customerNumber']);
        $I->assertSame(0, $customerData['points']);
        $I->assertSame('1984-12-22T00:00:00+01:00', $customerData['birthdate']);
        $I->assertSame('2011-02-01T08:41:25+01:00', $customerData['registered']);
        $I->assertSame('2011-02-01T08:41:25+01:00', $customerData['created']);
        $I->assertInstanceOf(DateTime::class, DateTime::createFromFormat(DateTime::ATOM, $customerData['updated']));
    }

    /**
     * @dataProvider dataProviderCustomerRegister
     */
    public function testCustomerRegister(AcceptanceTester $I, Example $data): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', false, 'bool', [1, 2]);

        $shopId         = $data['shopId'];
        $expectedError  = $data['expectedError'];

        $I->sendGQLQuery(
            'mutation ($email: String!, $password: String!){
                customerRegister(customer:  {
                    email: $email,
                    password: $password
                }) {
                    id
                    email
                    birthdate
                }
            }',
            $data['variables'],
            0,
            $shopId
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        if ($expectedError) {
            $I->assertSame($expectedError, $result['errors'][0]['message']);
        } else {
            $customerData = $result['data']['customerRegister'];
            $I->assertNotEmpty($customerData['id']);
            $I->assertSame($data['variables']['email'], $customerData['email']);
        }
    }

    public function dataProviderCustomerEmailUpdate()
    {
        return [
            [
                'shopId'         => 2,
                'email'          => 'user@oxid-esales.com',
                'userId'         => '309db395b6c85c3881fcb9b437a73dd6',
                'expectedError'  => "This e-mail address 'user@oxid-esales.com' already exists!",
            ],
            [
                'shopId'         => 2,
                'email'          => '',
                'userId'         => '309db395b6c85c3881fcb9b437a73dd6',
                'expectedError'  => 'The e-mail address must not be empty!',
            ],
            [
                'shopId'         => 2,
                'email'          => 'otheruser@oxid-esales.com',
                'userId'         => '309db395b6c85c3881fcb9b437a73ddx',
                'expectedError'  => null,
            ],
            [
                'shopId'         => 1,
                'email'          => 'newUser@oxid-esales.com',
                'userId'         => '9119cc8cd9593c214be93ee558235f3x',
                'expectedError'  => null,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderCustomerEmailUpdate
     */
    public function testCustomerEmailUpdate(AcceptanceTester $I, Example $data): void
    {
        $shopId         = $data['shopId'];
        $userId         = $data['userId'];
        $expectedError  = $data['expectedError'];

        $I->updateConfigInDatabaseForShops('blMallUsers', false, 'bool', [1, 2]);

        $I->login(self::USERNAME_FOR_EMAIL_CHANGE, 'useruser', $shopId);

        $I->sendGQLQuery(
            'mutation ($email: String!) {
                customerEmailUpdate(email: $email) {
                    id
                    email
                }
            }',
            [
                'email' => $data['email'],
            ],
            0,
            $shopId
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        if ($expectedError) {
            $I->assertSame($expectedError, $result['errors'][0]['message']);
        } else {
            $customerData = $result['data']['customerEmailUpdate'];

            $I->assertSame($userId, $customerData['id']);
            $I->assertSame($data['email'], $customerData['email']);
        }
    }

    public function testCustomerBirthdateUpdate(AcceptanceTester $I): void
    {
        $shopId = 2;

        $I->updateConfigInDatabaseForShops('blMallUsers', false, 'bool', [1, 2]);

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'mutation {
                customerBirthdateUpdate(birthdate: "1986-12-25") {
                    id
                    email
                    birthdate
                }
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'id'        => '123ad3b5380202966df6ff128e9eecaq',
                'email'     => self::USERNAME,
                'birthdate' => '1986-12-25T00:00:00+01:00',
            ],
            $result['data']['customerBirthdateUpdate']
        );
    }

    /**
     * @dataProvider providerMallUserOrders
     */
    public function testMallUserOrders(AcceptanceTester $I, Example $data): void
    {
        $shopId = $data['shopId'];

        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'query {
                customer{
                    orders {
                        id
                    }
                }
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame($data['expected'], count($result['data']['customer']['orders']));
    }

    protected function dataProviderCustomerNewsletterPerShop()
    {
        return [
            'shop_1' => [
                'shopId'   => 1,
                'expected' => 'SUBSCRIBED',
            ],
            'shop_2' => [
                'shopId'   => 2,
                'expected' => 'MISSING_DOUBLE_OPTIN',
            ],
        ];
    }

    protected function dataProviderCustomerRegister()
    {
        return [
            [
                'shopId'         => 1,
                'variables'      => [
                    'email'    => 'user@oxid-esales.com',
                    'password' => 'useruser',
                ],
                'expectedError'  => "This e-mail address 'user@oxid-esales.com' already exists!",
            ],
            [
                'shopId'         => 2,
                'variables'      => [
                    'email'    => 'user@oxid-esales.com',
                    'password' => 'useruser',
                ],
                'expectedError'  => "This e-mail address 'user@oxid-esales.com' already exists!",
            ],
            [
                'shopId'         => 1,
                'variables'      => [
                    'email'    => 'testUserEE@oxid-esales.com',
                    'password' => 'useruser',
                ],
                'expectedError'  => null,
            ],
            [
                'shopId'         => 2,
                'variables'      => [
                    'email'    => 'testUserEE@oxid-esales.com',
                    'password' => 'useruser',
                ],
                'expectedError'  => null,
            ],
        ];
    }

    protected function providerMallUserOrders()
    {
        return [
            'shop_1' => [
                'shopId'   => 1,
                'expected' => 4,
            ],
            'shop_2' => [
                'shopId'   => 2,
                'expected' => 1,
            ],
        ];
    }
}
