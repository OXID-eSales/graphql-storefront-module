<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\NewsletterStatus;

use Codeception\Example;
use Codeception\Scenario;
use Codeception\Util\HttpCode;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group newsletterstatus
 */
final class NewsletterStatusSubscribeCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const OTHER_USER_OXID = '245ad3b5380202966df6ff128e9eecaq';

    private const SUBSCRIPTION_ID = '_othertestuser';

    private const OTHER_USER_PASSWORD = 'useruser';

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $I->updateConfigInDatabase('blOrderOptInEmail', true, 'bool');
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->deleteFromDatabase(
            'oxnewssubscribed',
            [
                'OXID LIKE' => '_%',
            ]
        );
    }

    public function testNewsletterSubscribeMissingInputData(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('mutation {
            newsletterSubscribe (newsletterStatus: {})
            {
               status
            }
        }');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function testNewsletterSubscribeMissingInputDataButToken(AcceptanceTester $I): void
    {
        $this->prepareTestData($I, 0);
        $I->login(self::OTHER_USERNAME, self::OTHER_USER_PASSWORD);

        $I->sendGQLQuery('mutation {
            newsletterSubscribe (newsletterStatus: {})
            {
               status
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('MISSING_DOUBLE_OPTIN', $result['data']['newsletterSubscribe']['status']);
        $this->assertSubscriptionStatus($I, 'MISSING_DOUBLE_OPTIN');

        $this->assertSubscriptionHasNoEmailErrors($I, self::OTHER_USERNAME);
    }

    public function testNewsletterSubscribeExistingUserWithoutToken(AcceptanceTester $I): void
    {
        $this->prepareTestData($I, 0);

        $I->sendGQLQuery('mutation {
            newsletterSubscribe(newsletterStatus: {
              email: "' . self::OTHER_USERNAME . '"
            }) {
                status
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('MISSING_DOUBLE_OPTIN', $result['data']['newsletterSubscribe']['status']);
        $this->assertSubscriptionStatus($I, 'MISSING_DOUBLE_OPTIN');

        $this->assertSubscriptionHasNoEmailErrors($I, self::OTHER_USERNAME);
    }

    public function testNewsletterSubscribeExistingSubcribedUser(AcceptanceTester $I): void
    {
        $this->prepareTestData($I, 1);

        $I->sendGQLQuery('mutation {
            newsletterSubscribe(newsletterStatus: {
              email: "' . self::OTHER_USERNAME . '"
            }) {
                status
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('MISSING_DOUBLE_OPTIN', $result['data']['newsletterSubscribe']['status']);
        $this->assertSubscriptionStatus($I, 'MISSING_DOUBLE_OPTIN');

        $this->assertSubscriptionHasNoEmailErrors($I, self::OTHER_USERNAME);
    }

    public function testNewsletterSubscribeExistingSubcribedUserByToken(AcceptanceTester $I): void
    {
        $this->prepareTestData($I, 1);
        $I->login(self::OTHER_USERNAME, self::OTHER_USER_PASSWORD);

        $I->sendGQLQuery('mutation {
            newsletterSubscribe (newsletterStatus: {})
            {
                status
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('SUBSCRIBED', $result['data']['newsletterSubscribe']['status']);
        $this->assertSubscriptionStatus($I, 'SUBSCRIBED');
    }

    /**
     * @dataProvider providerNewsletterSubscribeNotExistingUser
     */
    public function testNewsletterSubscribeNotExistingUser(AcceptanceTester $I, Example $data): void
    {
        $I->updateConfigInDatabase('blOrderOptInEmail', $data['require_optin'], 'bool');
        $input = $data['input'];

        $I->sendGQLQuery('mutation {
            newsletterSubscribe(newsletterStatus: {
              salutation: "' . $input['salutation'] . '"
              firstName: "' . $input['firstName'] . '"
              lastName: "' . $input['lastName'] . '"
              email: "' . $input['email'] . '"
            }) {
                salutation
                firstName
                lastName
                email
                status
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals($input, $result['data']['newsletterSubscribe']);
    }

    public function providerNewsletterSubscribeNotExistingUser()
    {
        $newUserMail = random_int(0, 10) . time() . '@oxid-esales.com';

        return [
            'max_data' => [
                'input' => [
                    'salutation' => 'mrs',
                    'firstName'  => 'Newgirl',
                    'lastName'   => 'Intown',
                    'email'      => $newUserMail,
                    'status'     => 'MISSING_DOUBLE_OPTIN',
                ],
                'require_optin' => true,
            ],
            'min_data' => [
                'input' => [
                    'salutation' => '',
                    'firstName'  => '',
                    'lastName'   => '',
                    'email'      => '2' . $newUserMail,
                    'status'     => 'MISSING_DOUBLE_OPTIN',
                ],
                'require_optin' => true,
            ],
            'no_optin_required' => [
                'input' => [
                    'salutation' => '',
                    'firstName'  => '',
                    'lastName'   => '',
                    'email'      => '3' . $newUserMail,
                    'status'     => 'SUBSCRIBED',
                ],
                'require_optin' => false,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderNewsletterSubscribeNotExistingUserIncompleteInput
     */
    public function testNewsletterSubscribeInvalidInput(AcceptanceTester $I, Example $data): void
    {
        $template = 'mutation {
                newsletterSubscribe(newsletterStatus: {
                  salutation: "%s"
                  firstName: "%s"
                  lastName: "%s"
                  email: "%s"
                }) {
                    status
                }
            }';

        $I->sendGQLQuery(sprintf($template, ...array_values($data['data'])));
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals($data['expected'], $result['errors'][0]['message']);
    }

    public function dataProviderNewsletterSubscribeNotExistingUserIncompleteInput()
    {
        $strangeEmail = str_pad('x', 1000) . '@oxid-esales.com';

        return [
            'empty_email' => [
                'data' => [
                    'salutation' => 'mrs',
                    'firstName'  => 'NewGirl',
                    'lastName'   => 'InTown',
                    'email'      => '',
                ],
                'expected' => "This e-mail address '' is invalid!",
            ],
            'invalid_email' => [
                'data' => [
                    'salutation' => 'mrs',
                    'firstName'  => 'NewGirl',
                    'lastName'   => 'InTown',
                    'email'      => 'admin',
                ],
                'expected' => "This e-mail address 'admin' is invalid!",
            ],
            'crazy_input' => [
                'data' => [
                    'salutation' => 'mrs',
                    'firstName'  => 'NewGirl',
                    'lastName'   => 'InTown',
                    'email'      => $strangeEmail,
                ],
                'expected' => "This e-mail address '{$strangeEmail}' is invalid!",
            ],
        ];
    }

    public function testNewsletterSubscribeExistingUserDifferentInputGetsIgnored(AcceptanceTester $I): void
    {
        $this->prepareTestData($I, 0);

        $I->sendGQLQuery('mutation {
            newsletterSubscribe(newsletterStatus: {
              salutation: "mrs"
              firstName: "Newgirl"
              lastName: "Intown"
              email: "' . self::OTHER_USERNAME . '"
            }) {
                salutation
                firstName
                lastName
                email
                status
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $result = $I->grabJsonResponseAsArray();

        $expected = [
            'salutation' => '',
            'firstName'  => 'Marc',
            'lastName'   => 'Muster',
            'email'      => self::OTHER_USERNAME,
            'status'     => 'MISSING_DOUBLE_OPTIN',
        ];
        $I->assertEquals($expected, $result['data']['newsletterSubscribe']);
        $this->assertSubscriptionStatus($I, 'MISSING_DOUBLE_OPTIN');

        $this->assertSubscriptionHasNoEmailErrors($I, self::OTHER_USERNAME);
    }

    public function testNewsletterSubscribePreferInputOverToken(AcceptanceTester $I): void
    {
        $this->prepareTestData($I, 0);
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('mutation {
            newsletterSubscribe(newsletterStatus: {
              salutation: "mrs"
              firstName: "Newgirl"
              lastName: "Intown"
              email: "' . self::OTHER_USERNAME . '"
            }) {
                salutation
                firstName
                lastName
                email
                status
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $result = $I->grabJsonResponseAsArray();

        $expected = [
            'salutation' => '',
            'firstName'  => 'Marc',
            'lastName'   => 'Muster',
            'email'      => self::OTHER_USERNAME,
            'status'     => 'MISSING_DOUBLE_OPTIN',
        ];
        $I->assertEquals($expected, $result['data']['newsletterSubscribe']);
        $this->assertSubscriptionStatus($I, 'MISSING_DOUBLE_OPTIN');

        $this->assertSubscriptionHasNoEmailErrors($I, self::OTHER_USERNAME);
    }

    private function prepareTestData(AcceptanceTester $I, int $optin = 2): void
    {
        $I->haveInDatabase(
            'oxnewssubscribed',
            [
                'OXID'           => self::SUBSCRIPTION_ID,
                'OXSUBSCRIBED'   => '2020-04-01 14:14:14',
                'OXUNSUBSCRIBED' => '1980-01-01 00:00:00',
            ]
        );

        $I->updateInDatabase(
            'oxnewssubscribed',
            [
                'OXUSERID'     => self::OTHER_USER_OXID,
                'OXDBOPTIN'    => $optin,
                'OXEMAIL'      => self::OTHER_USERNAME,
                'OXFNAME'      => 'Marc',
                'OXLNAME'      => 'Muster',
                'OXSUBSCRIBED' => '2020-04-01 14:14:14',
            ],
            [
                'OXID' => self::SUBSCRIPTION_ID,
            ]
        );
    }

    private function assertSubscriptionStatus(AcceptanceTester $I, string $status, string $email = self::OTHER_USERNAME): void
    {
        $I->login($email, self::PASSWORD);

        $I->sendGQLQuery('query {
            customer {
                id
                newsletterStatus {
                    status
                }
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals($status, $result['data']['customer']['newsletterStatus']['status']);
    }

    private function assertSubscriptionHasNoEmailErrors(AcceptanceTester $I, string $email = self::OTHER_USERNAME): void
    {
        $I->canSeeInDatabase(
            'oxnewssubscribed',
            [
                'OXEMAIL'       => $email,
                'OXEMAILFAILED' => 0,
            ]
        );
    }
}
