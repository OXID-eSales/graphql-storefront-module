<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\NewsletterStatus;

use Codeception\Util\HttpCode;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group newsletterstatus
 */
final class NewsletterStatusCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const OTHER_USER_OXID = '245ad3b5380202966df6ff128e9eecaq';

    private const OTHER_USER_OXPASSALT = 'b186f117054b700a89de929ce90c6aef';

    private const SUBSCRIPTION_ID = '_othertestuser';

    public function testNewsletterOptInNoDatabaseEntry(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('mutation{
          newsletterOptIn(newsletterStatus: {
            email:"' . self::OTHER_USERNAME . '",
            confirmCode:"' . md5(self::OTHER_USERNAME . self::OTHER_USER_OXPASSALT) . '"
          }){
            email
            status
          }
        }');
        $result = $I->grabJsonResponseAsArray();
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->assertEquals(
            'Newsletter subscription status was not found for: ' . self::OTHER_USERNAME,
            $result['errors'][0]['message']
        );
    }

    public function testNewsletterOptInWrongConfirmationCode(AcceptanceTester $I): void
    {
        $this->prepareTestData($I);

        $I->sendGQLQuery('mutation{
          newsletterOptIn(newsletterStatus: {
            email:"' . self::OTHER_USERNAME . '",
            confirmCode:"incorrect"
          }){
            email
            status
          }
        }');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals("Wrong e-mail confirmation code 'incorrect'!", $result['errors'][0]['message']);
    }

    public function testNewsletterOptInEmptyEmail(AcceptanceTester $I): void
    {
        $this->prepareTestData($I);

        $I->sendGQLQuery('mutation{
          newsletterOptIn(newsletterStatus: {
            email:"",
            confirmCode:""
          }){
            email
            status
          }
        }');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('The e-mail address must not be empty!', $result['errors'][0]['message']);
    }

    public function testNewsletterOptInWorks(AcceptanceTester $I): void
    {
        $this->prepareTestData($I);

        $I->sendGQLQuery('mutation{
          newsletterOptIn(newsletterStatus: {
            email:"' . self::OTHER_USERNAME . '",
            confirmCode:"' . md5(self::OTHER_USERNAME . self::OTHER_USER_OXPASSALT) . '"
          }){
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
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $result = $I->grabJsonResponseAsArray();

        $data = $result['data']['newsletterOptIn'];
        $I->assertEquals('SUBSCRIBED', $data['status']);
    }

    public function testNewsletterStatusUnsubscribe(AcceptanceTester $I): void
    {
        $this->prepareTestData($I, 1);

        $I->sendGQLQuery(
            'mutation {
                newsletterUnsubscribe (newsletterStatus: {
                  email: "' . self::OTHER_USERNAME . '"
                })
            }'
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $result = $I->grabJsonResponseAsArray();
        $I->assertTrue($result['data']['newsletterUnsubscribe']);

        $I->canSeeInDatabase(
            'oxnewssubscribed',
            [
                'OXUSERID'  => self::OTHER_USER_OXID,
                'OXDBOPTIN' => 0,
            ]
        );
    }

    public function testNewsletterStatusUnsubscribeForMissingData(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('mutation {
            newsletterUnsubscribe (newsletterStatus: {
              email: "nouser@oxid-esales.com"
            })
        }');

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    public function testNewsletterStatusUnsubscribeWithTokenOnly(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('mutation {
            newsletterUnsubscribe
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $result = $I->grabJsonResponseAsArray();

        $I->assertTrue($result['data']['newsletterUnsubscribe']);
    }

    public function testNewsletterStatusUnsubscribeForMissingDataOrToken(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('mutation {
            newsletterUnsubscribe
        }');

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('Missing subscriber email or token', $result['errors']['0']['message']);
    }

    public function testNewsletterStatusUnsubscribePreferInputOverToken(AcceptanceTester $I): void
    {
        $this->prepareTestData($I, 1);
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('mutation {
            newsletterUnsubscribe (newsletterStatus: {
              email: "' . self::OTHER_USERNAME . '"
            })
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $result = $I->grabJsonResponseAsArray();

        $I->assertTrue($result['data']['newsletterUnsubscribe']);

        $I->canSeeInDatabase(
            'oxnewssubscribed',
            [
                'OXUSERID'  => self::OTHER_USER_OXID,
                'OXDBOPTIN' => 0,
            ]
        );
    }

    private function prepareTestData(AcceptanceTester $I, int $optin = 2): void
    {
        $I->haveInDatabase(
            'oxnewssubscribed',
            [
                'OXID'           => self::SUBSCRIPTION_ID,
                'OXSUBSCRIBED'   => '2020-04-01 13:13:13',
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
                'OXSUBSCRIBED' => '2020-04-01 13:13:13',
            ],
            [
                'OXID' => self::SUBSCRIPTION_ID,
            ]
        );
    }
}
