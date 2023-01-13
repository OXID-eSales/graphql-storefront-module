<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Vendor;

use Codeception\Example;
use Codeception\Scenario;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group vendor
 * @group oe_graphql_storefront
 * @group other
 */
final class VendorMultishopCest extends MultishopBaseCest
{
    private const VENDOR_ID = 'a57c56e3ba710eafb2225e98f058d989';

    private const VENDOR_MAP_ID = '902';

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $I->updateInDatabase(
            'oxvendor',
            [
                'oxtitle' => 'www.true-fashion.com/de',
                'oxtitle_1' => 'www.true-fashion.com/en',
            ],
            ['oxid' => self::VENDOR_ID]
        );
    }

    public function testGetSingleNotInShopActiveVendorWillFail(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                vendor (vendorId: "' . self::VENDOR_ID . '") {
                    id
                }
            }'
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Vendor was not found by id: ' . self::VENDOR_ID,
            $response['errors'][0]['message']
        );
    }

    public function testGetEmptyVendorListOfNotMainShop(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query{
                vendors {
                    id
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertCount(
            0,
            $response['data']['vendors']
        );
    }

    public function testGetSingleInShopActiveVendorWillWork(AcceptanceTester $I): void
    {
        $this->addVendorToSecondShop($I);

        $I->sendGQLQuery(
            'query {
                vendor (vendorId: "' . self::VENDOR_ID . '") {
                    id,
                    title
                    products {
                        id
                    }
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'id' => self::VENDOR_ID,
                'title' => 'www.true-fashion.com/de',
                'products' => [],
            ],
            $response['data']['vendor']
        );

        $this->removeVendorFromSecondShop($I);
    }

    public function testGetOneVendorInListOfNotMainShop(AcceptanceTester $I): void
    {
        $this->addVendorToSecondShop($I);

        $I->sendGQLQuery(
            'query{
                vendors {
                    id
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertCount(
            1,
            $response['data']['vendors']
        );

        $this->removeVendorFromSecondShop($I);
    }

    /**
     * @dataProvider providerGetVendorMultilanguage
     */
    public function testGetSingleTranslatedSecondShopVendor(AcceptanceTester $I, Example $data): void
    {
        $this->addVendorToSecondShop($I);

        $I->sendGQLQuery(
            'query {
                vendor (vendorId: "' . self::VENDOR_ID . '") {
                    id
                    title
                }
            }',
            null,
            $data['languageId'],
            $data['shopId'],
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'id' => self::VENDOR_ID,
                'title' => $data['title'],
            ],
            $response['data']['vendor']
        );

        $this->removeVendorFromSecondShop($I);
    }

    protected function providerGetVendorMultilanguage()
    {
        return [
            'shop_1_de' => [
                'shopId' => 1,
                'languageId' => 0,
                'title' => 'www.true-fashion.com/de',
            ],
            'shop_1_en' => [
                'shopId' => 1,
                'languageId' => 1,
                'title' => 'www.true-fashion.com/en',
            ],
            'shop_2_de' => [
                'shopId' => 2,
                'languageId' => 0,
                'title' => 'www.true-fashion.com/de',
            ],
            'shop_2_en' => [
                'shopId' => 2,
                'languageId' => 1,
                'title' => 'www.true-fashion.com/en',
            ],
        ];
    }

    /**
     * @dataProvider providerGetVendorMultilanguage
     */
    public function testGetListTranslatedSecondShopVendors(AcceptanceTester $I, Example $data): void
    {
        $this->addVendorToSecondShop($I);

        $I->sendGQLQuery(
            'query {
                vendors(filter: {
                    title: {
                        equals: "' . $data['title'] . '"
                    }
                }) {
                    id,
                    title
                }
            }',
            null,
            $data['languageId'],
            $data['shopId'],
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'id' => self::VENDOR_ID,
                'title' => $data['title'],
            ],
            $response['data']['vendors'][0]
        );

        $this->removeVendorFromSecondShop($I);
    }

    private function addVendorToSecondShop(AcceptanceTester $I): void
    {
        $I->haveInDatabase(
            'oxvendor2shop',
            [
                'oxshopid' => 2,
                'oxmapobjectid' => self::VENDOR_MAP_ID,
            ]
        );
    }

    private function removeVendorFromSecondShop(AcceptanceTester $I): void
    {
        $I->deleteFromDatabase(
            'oxvendor2shop',
            [
                'oxshopid' => 2,
                'oxmapobjectid' => self::VENDOR_MAP_ID,
            ]
        );
    }
}
