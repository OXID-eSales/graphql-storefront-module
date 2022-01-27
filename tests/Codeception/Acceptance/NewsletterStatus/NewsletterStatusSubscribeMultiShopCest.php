<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\NewsletterStatus;

use Codeception\Example;
use Codeception\Scenario;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group newsletterstatus
 * @group oe_graphql_storefront
 * @group other
 */
final class NewsletterStatusSubscribeMultiShopCest extends MultishopBaseCest
{
    private const DIFFERENT_USERNAME = 'multishopuser@oxid-esales.com';

    private const DIFFERENT_USER_OXID = '_09db395b6c85c3881fcb9b437a73gg6';

    private const PASSWORD = 'useruser';

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $I->updateConfigInDatabaseForShops('blOrderOptInEmail', true, 'bool', [1, 2]);
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->deleteFromDatabase(
            'oxnewssubscribed',
            [
                'OXUSERID LIKE' => '_%',
            ]
        );

        parent::_after($I);
    }

    /**
     * @dataProvider dataProviderNewsletterSubscribePerShop
     */
    public function testUserNewsletterSubscribePerShopWithoutToken(AcceptanceTester $I, Example $data): void
    {
        $shopId = $data['shopId'];

        $this->prepareTestdata($I, $shopId, 0);

        $I->sendGQLQuery(
            'mutation {
                newsletterSubscribe(newsletterStatus: {
                  email: "' . self::DIFFERENT_USERNAME . '"
                }) {
                  status
                }
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('MISSING_DOUBLE_OPTIN', $result['data']['newsletterSubscribe']['status']);
        $this->assertSubscriptionStatus($I, 'MISSING_DOUBLE_OPTIN', $shopId);
    }

    public function testNewsletterStatusMallUserSubscribeFromToken(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        $this->prepareTestdata($I, 1, 0);

        $I->login(self::DIFFERENT_USERNAME, self::PASSWORD, 2);

        $I->sendGQLQuery(
            'mutation{
                newsletterSubscribe (newsletterStatus: {})
                {
                    status
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('MISSING_DOUBLE_OPTIN', $result['data']['newsletterSubscribe']['status']);

        //malluser is still not subscribed in shop 1 but subscribed in shop 2 (before optin)
        $this->assertSubscriptionStatus($I, 'UNSUBSCRIBED', 1);
        $this->assertSubscriptionStatus($I, 'MISSING_DOUBLE_OPTIN', 2);
    }

    /**
     * @dataProvider dataProviderNewsletterStatusMallUser
     */
    public function testNewsletterSubscribeForMallUserFromOtherSubshop(AcceptanceTester $I, Example $data): void
    {
        $flag           = $data['flag'];
        $expectSameUser = $data['sameuser'];

        $I->updateConfigInDatabaseForShops('blMallUsers', $flag, 'bool', [1, 2]);
        $this->prepareTestdata($I, 1, 0);

        $I->sendGQLQuery(
            'mutation {
                newsletterSubscribe(newsletterStatus: {
                  salutation: "mrs"
                  firstName: "Newgirl"
                  lastName: "Intown"
                  email: "' . self::DIFFERENT_USERNAME . '"
                }){
                  status
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('MISSING_DOUBLE_OPTIN', $result['data']['newsletterSubscribe']['status']);

        $this->assertSubscriptionStatus($I, 'UNSUBSCRIBED', 1);
        $userId = $this->assertSubscriptionStatus($I, 'MISSING_DOUBLE_OPTIN', 2);

        if ($expectSameUser) {
            $I->assertEquals(self::DIFFERENT_USER_OXID, $userId);
        } else {
            $I->assertNotEquals(self::DIFFERENT_USER_OXID, $userId);
        }
    }

    protected function dataProviderNewsletterSubscribePerShop()
    {
        return [
            'shop_1' => [
                'shopId'   => 1,
            ],
            'shop_2' => [
                'shopId'   => 2,
            ],
        ];
    }

    protected function dataProviderNewsletterStatusMallUser()
    {
        return [
            'malluser' => [
                'flag'     => true,
                'sameuser' => true,
            ],
            'no_malluser' => [
                'flag'     => false,
                'sameuser' => false,
            ],
        ];
    }

    private function prepareTestdata(AcceptanceTester $I, int $shopid, int $optin = 2): void
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
                'OXID'           => $oxid,
                'oxsubscribed'   => '2020-04-01 15:15:15',
                'oxunsubscribed' => '1980-01-01 00:00:00',
            ]
        );

        $I->updateInDatabase(
            'oxnewssubscribed',
            [
                'oxuserid'     => self::DIFFERENT_USER_OXID,
                'oxemail'      => self::DIFFERENT_USERNAME,
                'oxdboptin'    => $optin,
                'oxshopid'     => $shopid,
                'oxsubscribed' => '2020-04-01 15:15:15',
            ],
            [
                'OXID' => $oxid,
            ]
        );
    }

    private function assertSubscriptionStatus(
        AcceptanceTester $I,
        string $status,
        int $shopId
    ): string {
        $I->login(self::DIFFERENT_USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'query {
                customer {
                    id
                    newsletterStatus {
                        status
                    }
                }
            }',
            null,
            0,
            $shopId
        );

        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals($status, $result['data']['customer']['newsletterStatus']['status']);

        return $result['data']['customer']['id'];
    }
}
