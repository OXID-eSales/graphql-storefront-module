<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\NewsletterStatus;

use Codeception\Example;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group newsletterstatus
 * @group oe_graphql_storefront
 * @group other
 */
final class NewsletterStatusMultiShopCest extends MultishopBaseCest
{
    private const OTHER_USERNAME = 'newsletter@oxid-esales.com';

    private const OTHER_USER_OXID = '_678b395b6c85c3881fcb9b437a73hh9';

    private const OTHER_USER_OXPASSALT = 'b186f117054b700a89de929ce90c6aef';

    private const OTHER_USER_PASSWORD = 'useruser';

    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    public function _after(AcceptanceTester $I): void
    {
        $this->assignUserToShop($I, 2);

        $I->deleteFromDatabase(
            'oxnewssubscribed',
            [
                'OXID LIKE' => '_%',
            ]
        );

        parent::_after($I);
    }

    /**
     * @dataProvider providerNewsletterStatusPerShop
     */
    public function testUserNewsletterStatusOptinPerShop(AcceptanceTester $I, Example $data): void
    {
        $this->prepareTestdata($I, $data['shopId']);
        $this->assignUserToShop($I, $data['shopId']);

        $I->sendGQLQuery(
            'mutation{
                newsletterOptIn(newsletterStatus: {
                    email:"' . self::OTHER_USERNAME . '",
                    confirmCode:"' . md5(self::OTHER_USERNAME . self::OTHER_USER_OXPASSALT) . '"
                }){
                    email
                    status
                }
            }',
            [],
            0,
            $data['shopId']
        );

        $result = $I->grabJsonResponseAsArray();

        $I->assertSame('SUBSCRIBED', $result['data']['newsletterOptIn']['status']);
    }

    /**
     * @dataProvider providerNewsletterStatusPerShop
     */
    public function testNewsletterUnsubscribePerShop(AcceptanceTester $I, Example $data): void
    {
        $this->prepareTestdata($I, $data['shopId']);
        $this->assignUserToShop($I, $data['shopId']);

        $I->sendGQLQuery(
            'mutation{
                newsletterUnsubscribe(newsletterStatus: {
                    email:"' . self::OTHER_USERNAME . '"
                })
            }',
            [],
            0,
            $data['shopId']
        );

        $result = $I->grabJsonResponseAsArray();

        $I->assertTrue($result['data']['newsletterUnsubscribe']);
    }

    protected function providerNewsletterStatusPerShop()
    {
        return [
            'shop_1' => [
                'shopId' => 1,
            ],
            'shop_2' => [
                'shopId' => 2,
            ],
        ];
    }

    /**
     * @dataProvider providerNewsletterStatusMallUser
     */
    public function testNewsletterOptInForMallUserFromOtherSubshop(AcceptanceTester $I, Example $data): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', $data['flag'], 'bool', [1, 2]);

        $this->prepareTestdata($I, 2);
        $this->assignUserToShop($I, 1);

        $I->sendGQLQuery(
            'mutation{
                newsletterOptIn(newsletterStatus: {
                    email:"' . self::OTHER_USERNAME . '",
                    confirmCode:"' . md5(self::OTHER_USERNAME . self::OTHER_USER_OXPASSALT) . '"
                }){
                    email
                    status
                }
            }',
            [],
            0,
            2
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        if ($data['expectedFailure'] === true) {
            $I->assertSame(
                'Newsletter subscription status was not found for: ' . self::OTHER_USERNAME,
                $result['errors'][0]['message']
            );
        } else {
            $I->assertSame(
                'newsletter@oxid-esales.com',
                $result['data']['newsletterOptIn']['email']
            );
            $I->assertSame(
                'SUBSCRIBED',
                $result['data']['newsletterOptIn']['status']
            );
        }

        if ($data['flag']) {
            $I->assertSame('SUBSCRIBED', $result['data']['newsletterOptIn']['status']);
        }
    }

    /**
     * @dataProvider providerNewsletterStatusMallUser
     */
    public function testNewsletterUnsubcribeForMallUserFromOtherSubshop(AcceptanceTester $I, Example $data): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', $data['flag'], 'bool', [1, 2]);

        $this->prepareTestdata($I, 2);
        $this->assignUserToShop($I, 1);

        $I->sendGQLQuery(
            'mutation{
                newsletterUnsubscribe(newsletterStatus: {
                    email:"' . self::OTHER_USERNAME . '"
                })
            }',
            [],
            0,
            2
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        if ($data['expectedFailure'] === true) {
            $I->assertSame(
                'Newsletter subscription status was not found for: ' . self::OTHER_USERNAME,
                $result['errors'][0]['message']
            );
        } else {
            $I->assertTrue($result['data']['newsletterUnsubscribe']);
        }

        if ($data['flag']) {
            $I->assertTrue($result['data']['newsletterUnsubscribe']);
        }
    }

    protected function providerNewsletterStatusMallUser()
    {
        return [
            'malluser' => [
                'flag' => true,
                'expectedFailure' => false,
            ],
            'no_malluser' => [
                'flag' => false,
                'expectedFailure' => true,
            ],
        ];
    }

    public function testNewsletterStatusMallUserUnsubscribeFromToken(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        $this->prepareTestdata($I, 1);
        $this->prepareTestdata($I, 2);
        $this->assignUserToShop($I, 1);

        $I->login(self::OTHER_USERNAME, self::OTHER_USER_PASSWORD, 2);

        $I->sendGQLQuery(
            'mutation{
                newsletterUnsubscribe
            }',
            [],
            0,
            2
        );

        $result = $I->grabJsonResponseAsArray();

        $I->assertTrue($result['data']['newsletterUnsubscribe']);

        $I->seeInDatabase(
            'oxnewssubscribed',
            [
                'OXID' => '_othertestuser1',
                'OXDBOPTIN' => 2,
            ]
        );

        $I->seeInDatabase(
            'oxnewssubscribed',
            [
                'OXID' => '_othertestuser2',
                'OXDBOPTIN' => 0,
            ]
        );
    }

    public function testNewsletterStatusMallUserUnsubscribePreferInputOverToken(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        // otheruser belongs to subshop 2
        $this->prepareTestdata($I, 2);
        $this->assignUserToShop($I, 2);

        $I->login(self::USERNAME, self::PASSWORD, 2);

        $I->sendGQLQuery(
            'mutation{
                newsletterUnsubscribe(newsletterStatus: {
                    email:"' . self::OTHER_USERNAME . '"
                })
            }',
            [],
            0,
            2
        );

        $result = $I->grabJsonResponseAsArray();

        $I->assertTrue($result['data']['newsletterUnsubscribe']);

        $I->seeInDatabase(
            'oxnewssubscribed',
            [
                'OXID' => '_othertestuser2',
                'OXDBOPTIN' => 0,
            ]
        );
    }

    private function prepareTestdata(AcceptanceTester $I, int $shopid): void
    {
        $oxid = '_othertestuser' . $shopid;

        $I->deleteFromDatabase(
            'oxnewssubscribed',
            [
                'OXID' => $oxid,
            ]
        );

        $I->haveInDatabase(
            'oxnewssubscribed',
            [
                'OXID' => $oxid,
                'oxsubscribed' => '2020-04-01 13:13:13',
                'oxunsubscribed' => '1980-01-01 00:00:00',
            ]
        );

        $I->updateInDatabase(
            'oxnewssubscribed',
            [
                'oxuserid' => self::OTHER_USER_OXID,
                'oxemail' => self::OTHER_USERNAME,
                'oxdboptin' => 2,
                'oxshopid' => $shopid,
                'oxsubscribed' => '2020-04-01 13:13:13',
            ],
            [
                'OXID' => $oxid,
            ]
        );
    }

    private function assignUserToShop(AcceptanceTester $I, int $shopid): void
    {
        $I->updateInDatabase(
            'oxuser',
            [
                'oxshopid' => $shopid,
            ],
            [
                'OXID' => self::OTHER_USER_OXID,
            ]
        );
    }
}
