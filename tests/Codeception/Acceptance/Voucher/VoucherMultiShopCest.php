<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Voucher;

use Codeception\Example;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group voucher
 * @group oe_graphql_storefront
 * @group other
 */
final class VoucherMultiShopCest extends MultishopBaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const SHOP1_BASKET = '_test_voucher_public';

    private const SHOP2_BASKET = '_test_shop2_basket_public';

    private const SHOP1_VOUCHER_NR = 'myVoucher';

    private const SHOP1_VOUCHER_ID = 'personal_voucher_1';

    private const SHOP2_VOUCHER_NR = 'shop2voucher';

    private const SHOP2_VOUCHER_ID = 'shop_2_voucher_series';

    /**
     * @dataProvider dataProviderAddVoucherToBasketPerShop
     */
    public function testAddVoucherToBasketPerShop(AcceptanceTester $I, Example $data): void
    {
        $shopId = $data['shopId'];
        $basketId = $data['basketId'];
        $voucherNr = $data['voucherNr'];

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery($this->addVoucherMutation($basketId, $voucherNr), null, 0, $shopId);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            [
                'id' => $basketId,
                'vouchers' => [
                    [
                        'number' => $voucherNr,
                    ],
                ],
            ],
            $result['data']['basketAddVoucher']
        );
    }

    public function testAddVoucherFromShop1ToBasketFromShop2(AcceptanceTester $I): void
    {
        $this->prepareVoucherInBasket($I, '', 'personal_voucher_1');
        $I->login(self::USERNAME, self::PASSWORD, 2);

        $I->sendGQLQuery($this->addVoucherMutation(self::SHOP2_BASKET, self::SHOP1_VOUCHER_NR), null, 0, 2);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            'ERROR_MESSAGE_VOUCHER_NOVOUCHER',
            $result['errors'][0]['message']
        );

        $I->assertEquals(
            self::SHOP1_VOUCHER_NR,
            $result['errors'][0]['extensions']['number']
        );
    }

    public function testAddVoucherFromShop2ToBasketFromShop1(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD, 1);

        $I->sendGQLQuery($this->addVoucherMutation(self::SHOP1_BASKET, self::SHOP2_VOUCHER_NR));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            'ERROR_MESSAGE_VOUCHER_NOVOUCHER',
            $result['errors'][0]['message']
        );

        $I->assertEquals(
            self::SHOP2_VOUCHER_NR,
            $result['errors'][0]['extensions']['number']
        );
    }

    public function testUnableToRemoveVoucherFromShop2InShop1(AcceptanceTester $I): void
    {
        $this->prepareVoucherInBasket($I, self::SHOP1_BASKET, self::SHOP2_VOUCHER_ID);

        $I->login(self::USERNAME, self::PASSWORD, 1);

        $I->sendGQLQuery($this->removeVoucherMutation(self::SHOP1_BASKET, self::SHOP2_VOUCHER_ID));

        $result = $I->grabJsonResponseAsArray();
        $I->assertEquals(
            'MESSAGE_COUPON_NOT_APPLIED_FOR_SHOP',
            $result['errors'][0]['message']
        );
    }

    public function testRemoveVoucherWithMallUser(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);
        $this->prepareVoucherInBasket($I, self::SHOP1_BASKET, self::SHOP2_VOUCHER_ID);

        $I->login(self::USERNAME, self::PASSWORD, 2);

        $I->sendGQLQuery(
            $this->removeVoucherMutation(self::SHOP1_BASKET, self::SHOP2_VOUCHER_ID),
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            0,
            count($result['data']['basketRemoveVoucher']['vouchers'])
        );
    }

    /**
     * @dataProvider dataProviderRemoveVoucherToBasketPerShop
     */
    public function testRemoveVoucherFromBasketPerShop(AcceptanceTester $I, Example $data): void
    {
        $shopId = $data['shopId'];
        $basketId = $data['basketId'];
        $voucherId = $data['voucherId'];

        $this->prepareVoucherInBasket($I, $basketId, $voucherId);

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery($this->removeVoucherMutation($basketId, $voucherId), null, 0, $shopId);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            [
                'id' => $basketId,
                'vouchers' => [],
            ],
            $result['data']['basketRemoveVoucher']
        );
    }

    public function testSeeAppliedVoucherFromShop2InShop1WithMallUser(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        // apply voucher on basket in shop2
        $this->prepareVoucherInBasket($I, self::SHOP1_BASKET, self::SHOP2_VOUCHER_ID);

        $I->login(self::USERNAME, self::PASSWORD, 1);

        //query the basket in shop1
        $this->getBasket($I, self::SHOP1_BASKET);

        //the voucher from shop2 should now be marked as not reserved
        $I->seeInDatabase(
            'oxvouchers',
            [
                'oxid' => self::SHOP2_VOUCHER_ID,
                'oxreserved' => 0,
                'oegql_basketid' => '',
            ]
        );
    }

    public function testApplyVouchersFromDifferentShopsOnSameBasket(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);
        $this->prepareVoucherInBasket($I, self::SHOP1_BASKET, self::SHOP2_VOUCHER_ID);
        $this->prepareVoucherInBasket($I, '', self::SHOP1_VOUCHER_ID);

        $I->login(self::USERNAME, self::PASSWORD, 1);

        $I->sendGQLQuery($this->addVoucherMutation(self::SHOP1_BASKET, self::SHOP1_VOUCHER_NR));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            self::SHOP1_VOUCHER_NR,
            $result['data']['basketAddVoucher']['vouchers'][0]['number']
        );
    }

    protected function dataProviderAddVoucherToBasketPerShop()
    {
        return [
            'shop_1' => [
                'shopId' => 1,
                'basketId' => self::SHOP1_BASKET,
                'voucherNr' => self::SHOP1_VOUCHER_NR,
            ],
            'shop_2' => [
                'shopId' => 2,
                'basketId' => self::SHOP2_BASKET,
                'voucherNr' => self::SHOP2_VOUCHER_NR,
            ],
        ];
    }

    protected function dataProviderRemoveVoucherToBasketPerShop()
    {
        return [
            'shop_1' => [
                'shopId' => 1,
                'basketId' => self::SHOP1_BASKET,
                'voucherId' => self::SHOP1_VOUCHER_ID,
            ],
            'shop_2' => [
                'shopId' => 2,
                'basketId' => self::SHOP2_BASKET,
                'voucherId' => self::SHOP2_VOUCHER_ID,
            ],
        ];
    }

    private function addVoucherMutation(string $basketId, string $voucher)
    {
        return 'mutation {
                  basketAddVoucher(
                    basketId: "' . $basketId . '",
                    voucherNumber: "' . $voucher . '"
                  ) {
                    id
                    vouchers {
                      number
                    }
                  }
                }';
    }

    private function removeVoucherMutation(string $basketId, string $voucherId)
    {
        return 'mutation {
                  basketRemoveVoucher(
                    basketId: "' . $basketId . '",
                    voucherId: "' . $voucherId . '"
                  ) {
                    id
                    vouchers {
                        number
                    }
                  }
                }';
    }

    private function prepareVoucherInBasket(AcceptanceTester $I, string $basketId, string $voucherId): void
    {
        $I->updateInDatabase('oxvouchers', [
            'OXRESERVED' => $basketId ? time() : 0,
            'OEGQL_BASKETID' => $basketId,
        ], [
            'OXID' => $voucherId,
        ]);
    }

    private function getBasket(AcceptanceTester $I, string $basketId): void
    {
        $I->sendGQLQuery(
            'query{
                basket(basketId: "' . $basketId . '") {
                    vouchers{
                        voucher
                        id
                        discount
                        reserved
                    }
                }
            }'
        );
    }
}
