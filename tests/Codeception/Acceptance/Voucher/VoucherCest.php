<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Voucher;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group voucher
 */
final class VoucherCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const BASKET = '_test_voucher_public';

    private const BASKET_PUBLIC = '_test_basket_public';

    private const VOUCHER = 'myVoucher';

    private const SERIES_VOUCHER = 'mySeriesVoucher';

    private const OTHER_SERIES_VOUCHER = 'seriesVoucher';

    private const WRONG_VOUCHER = 'non_existing_voucher';

    private const USED_VOUCHER = 'used_voucher';

    private const PRODUCT_ID = 'dc5ffdf380e15674b56dd562a7cb6aec';

    public function _after(AcceptanceTester $I): void
    {
        //Reset voucher usage
        $this->prepareVoucher($I, '', 'personal_voucher_1');
        $this->prepareVoucher($I, '', self::USED_VOUCHER);
    }

    public function testAddVoucherNotLoggedIn(AcceptanceTester $I): void
    {
        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET, self::VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'You do not have sufficient rights to access this field',
            $result['errors'][0]['message']
        );
    }

    public function testAddVoucherUnauthorized(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::PASSWORD);

        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET, self::VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'You are not allowed to access this basket as it belongs to somebody else',
            $result['errors'][0]['message']
        );
    }

    public function testAddVoucher(AcceptanceTester $I): void
    {
        $this->prepareVoucher($I, '', 'personal_voucher_1');

        $I->seeInDatabase(
            'oxvouchers',
            [
                'oxid'           => 'personal_voucher_1',
                'oxreserved'     => 0,
                'oegql_basketid' => '',
            ]
        );

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET, self::VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            self::BASKET,
            $result['data']['basketAddVoucher']['id']
        );
    }

    public function testAddVoucherNonExistingBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = 'non_existing';

        $I->sendGQLQuery($this->addVoucherMutation($basketId, self::VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            sprintf('Basket was not found by id: %s', $basketId),
            $result['errors'][0]['message']
        );
    }

    public function testAddWrongVoucher(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET, self::WRONG_VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            'ERROR_MESSAGE_VOUCHER_NOVOUCHER',
            $result['errors'][0]['message']
        );

        $I->assertEquals(
            self::WRONG_VOUCHER,
            $result['errors'][0]['extensions']['number']
        );
    }

    public function testAddAlreadyUsedVoucher(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET, self::USED_VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            'ERROR_MESSAGE_VOUCHER_NOVOUCHER',
            $result['errors'][0]['message']
        );

        $I->assertEquals(
            self::USED_VOUCHER,
            $result['errors'][0]['extensions']['number']
        );
    }

    public function testNotAllowToAddSecondVoucher(AcceptanceTester $I): void
    {
        $this->prepareVoucherInBasket($I);

        $I->seeInDatabase(
            'oxvouchers',
            [
                'oxid'           => 'personal_series_voucher_1',
                'oxreserved'     => 0,
                'oegql_basketid' => '',
            ]
        );
        $I->seeInDatabase(
            'oxvouchers',
            [
                'oxid'           => 'personal_series_voucher_2',
                'oxreserved'     => 0,
                'oegql_basketid' => '',
            ]
        );

        $I->login(self::USERNAME, self::PASSWORD);

        //Add first voucher
        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET_PUBLIC, self::SERIES_VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            self::BASKET_PUBLIC,
            $result['data']['basketAddVoucher']['id']
        );

        //Add second voucher and get error
        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET_PUBLIC, self::SERIES_VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            'ERROR_MESSAGE_VOUCHER_NOTALLOWEDSAMESERIES',
            $result['errors'][0]['message']
        );
    }

    public function testAllowAddingMultipleVouchers(AcceptanceTester $I): void
    {
        $this->prepareVoucherInBasket($I);
        $this->prepareSeriesVouchers($I, 'personal_series_voucher');

        $I->seeInDatabase(
            'oxvouchers',
            [
                'oxid'           => 'personal_series_voucher_1',
                'oxreserved'     => 0,
                'oegql_basketid' => '',
            ]
        );
        $I->seeInDatabase(
            'oxvouchers',
            [
                'oxid'           => 'personal_series_voucher_2',
                'oxreserved'     => 0,
                'oegql_basketid' => '',
            ]
        );

        $I->login(self::USERNAME, self::PASSWORD);

        //Add first voucher
        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET_PUBLIC, self::SERIES_VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            self::BASKET_PUBLIC,
            $result['data']['basketAddVoucher']['id']
        );

        //Add second voucher
        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET_PUBLIC, self::SERIES_VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            self::BASKET_PUBLIC,
            $result['data']['basketAddVoucher']['id']
        );
    }

    public function testNotAllowDifferentSeriesVoucher(AcceptanceTester $I): void
    {
        $this->prepareVoucherInBasket($I);

        $I->login(self::USERNAME, self::PASSWORD);

        $I->seeInDatabase(
            'oxvouchers',
            [
                'oxid'           => 'personal_series_voucher_1',
                'oxreserved'     => 0,
                'oegql_basketid' => '',
            ]
        );
        $I->seeInDatabase(
            'oxvouchers',
            [
                'oxid'           => 'personal_series_voucher_2',
                'oxreserved'     => 0,
                'oegql_basketid' => '',
            ]
        );

        //Add voucher from first series
        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET_PUBLIC, self::SERIES_VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            self::BASKET_PUBLIC,
            $result['data']['basketAddVoucher']['id']
        );

        //Add voucher from second series
        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET_PUBLIC, self::OTHER_SERIES_VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            'ERROR_MESSAGE_VOUCHER_NOTALLOWEDOTHERSERIES',
            $result['errors'][0]['message']
        );
    }

    public function testAllowDifferentSeriesVoucher(AcceptanceTester $I): void
    {
        $this->prepareVoucherInBasket($I);
        $this->prepareSeriesVouchers($I, 'series_voucher');

        $I->seeInDatabase(
            'oxvouchers',
            [
                'oxid'           => 'personal_series_voucher_1',
                'oxreserved'     => 0,
                'oegql_basketid' => '',
            ]
        );
        $I->seeInDatabase(
            'oxvouchers',
            [
                'oxid'           => 'personal_series_voucher_2',
                'oxreserved'     => 0,
                'oegql_basketid' => '',
            ]
        );

        $I->login(self::USERNAME, self::PASSWORD);

        //Add voucher from first series
        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET_PUBLIC, self::SERIES_VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            self::BASKET_PUBLIC,
            $result['data']['basketAddVoucher']['id']
        );

        //Add voucher from second series
        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET_PUBLIC, self::OTHER_SERIES_VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            self::BASKET_PUBLIC,
            $result['data']['basketAddVoucher']['id']
        );
    }

    public function testRemoveVoucherNotLoggedIn(AcceptanceTester $I): void
    {
        $I->sendGQLQuery($this->removeVoucherMutation(self::BASKET, self::VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'You do not have sufficient rights to access this field',
            $result['errors'][0]['message']
        );
    }

    public function testRemoveVoucherUnauthorized(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::PASSWORD);

        $I->sendGQLQuery($this->removeVoucherMutation(self::BASKET, 'personal_voucher_1'));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'You are not allowed to access this basket as it belongs to somebody else',
            $result['errors'][0]['message']
        );
    }

    public function testRemoveNonExistingVoucher(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery($this->removeVoucherMutation(self::BASKET, self::WRONG_VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            'ERROR_MESSAGE_VOUCHER_NOVOUCHER',
            $result['errors'][0]['message']
        );

        $I->assertEquals(
            self::WRONG_VOUCHER,
            $result['errors'][0]['extensions']['id']
        );
    }

    public function testRemoveVoucher(AcceptanceTester $I): void
    {
        $this->prepareVoucher($I, self::BASKET, 'personal_voucher_1');

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery($this->removeVoucherMutation(self::BASKET, 'personal_voucher_1'));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            self::BASKET,
            $result['data']['basketRemoveVoucher']['id']
        );
    }

    public function testVoucherBasketDiscount(AcceptanceTester $I): void
    {
        $this->prepareVoucher($I, '', 'personal_series_voucher_1');
        $this->prepareVoucher($I, '', 'series_voucher_1');

        $I->login(self::USERNAME, self::PASSWORD);

        //Check basket discounts without applied voucher
        $I->sendGQLQuery($this->basketQuery(self::BASKET_PUBLIC));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame($result['data']['basket'], [
            'id'   => self::BASKET_PUBLIC,
            'cost' => [
                'voucher'  => 0,
                'discount' => 0,
            ],
            'vouchers' => [],
        ]);

        //Add voucher and check basket discount
        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET_PUBLIC, self::VOUCHER));

        $I->sendGQLQuery($this->basketQuery(self::BASKET_PUBLIC));

        $I->seeResponseIsJson();
        $discountResult = $I->grabJsonResponseAsArray();

        $I->assertSame($discountResult['data']['basket'], [
            'id'   => self::BASKET_PUBLIC,
            'cost' => [
                'voucher'  => 5,
                'discount' => 5,
            ],
            'vouchers' => [
                [
                    'id' => 'personal_voucher_1',
                ],
            ],
        ]);
    }

    public function testRemoveInvalidVoucherFromBasket(AcceptanceTester $I): void
    {
        $this->prepareVoucher($I, self::BASKET, self::USED_VOUCHER);

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery($this->basketQuery(self::BASKET));

        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();
        $I->assertSame(
            [
                'id'       => self::BASKET,
                'cost'     => [
                    'voucher'  => 0,
                    'discount' => 0,
                ],
                'vouchers' => [],
            ],
            $result['data']['basket']
        );
    }

    public function testVoucherVisibilityWithSavedBasketAndGraphqlBasket(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::PASSWORD);

        // Create basket with voucher using graphql which is different than 'savedbasket'
        $graphqlBasketId = $this->basketCreateMutation($I, 'basket_with_vouchers');
        $I->sendGQLQuery($this->addVoucherMutation($graphqlBasketId, self::VOUCHER));

        $I->sendGQLQuery($this->basketQuery($graphqlBasketId));
        $vouchers = $I->grabJsonResponseAsArray()['data']['basket']['vouchers'];

        $I->assertCount(1, $vouchers);

        // Check 'savedbasket' owned by the same user, the vouchers should be 0
        $savedBasketId = $this->basketCreateMutation($I, 'savedbasket');
        $I->sendGQLQuery($this->basketQuery($savedBasketId));
        $vouchers = $I->grabJsonResponseAsArray()['data']['basket']['vouchers'];

        $I->assertCount(0, $vouchers);

        $this->basketRemoveMutation($I, $graphqlBasketId);
    }

    public function testVoucherIsUnmarkedAsReservedAfterBasketWasRemoved(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreateMutation($I, 'basket_remove');
        $I->sendGQLQuery($this->addVoucherMutation($basketId, self::VOUCHER));

        $I->seeInDatabase('oxvouchers', [
            'oxid'           => 'personal_voucher_1',
            'oxreserved >'   => 0,
            'oegql_basketid' => $basketId,
        ]);

        $this->basketRemoveMutation($I, $basketId);

        $I->seeInDatabase('oxvouchers', [
            'oxid'           => 'personal_voucher_1',
            'oxreserved'     => 0,
            'oegql_basketid' => '',
        ]);
    }

    public function testVoucherAssignedToSpecificProduct(AcceptanceTester $I): void
    {
        $productId2 = 'f4f73033cf5045525644042325355732';
        $I->haveInDatabase(
            'oxobject2discount',
            [
                'OXID'         => 'voucher_assigned_to_product',
                'OXDISCOUNTID' => 'personal_voucher',
                'OXOBJECTID'   => self::PRODUCT_ID,
                'OXTYPE'       => 'oxarticles',
            ]
        );

        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreateMutation($I, 'basket_voucher_product');
        $this->basketAddProductMutation($I, $basketId, $productId2);
        $I->sendGQLQuery($this->addVoucherMutation($basketId, self::VOUCHER));

        $result = $I->grabJsonResponseAsArray();
        $I->assertEquals(
            'MESSAGE_COUPON_NOT_APPLIED_FOR_ARTICLES',
            $result['errors'][0]['message']
        );

        $this->basketAddProductMutation($I, $basketId, self::PRODUCT_ID);
        $I->sendGQLQuery($this->addVoucherMutation($basketId, self::VOUCHER));

        $I->seeInDatabase('oxvouchers', [
            'oxid'           => 'personal_voucher_1',
            'oxreserved >'   => 0,
            'oegql_basketid' => $basketId,
        ]);

        $I->sendGQLQuery($this->basketQuery($basketId));
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $I->assertSame(
            [
                'id'       => $basketId,
                'cost'     => [
                    'voucher'  => 5,
                    'discount' => 5,
                ],
                'vouchers' => [
                    ['id' => 'personal_voucher_1'],
                ],
            ],
            $result['data']['basket']
        );

        $this->basketRemoveProductMutation($I, $basketId, self::PRODUCT_ID);

        $I->seeInDatabase('oxvouchers', [
            'oxid'           => 'personal_voucher_1',
            'oxreserved'     => 0,
            'oegql_basketid' => '',
        ]);

        // Reset DB
        $this->basketRemoveProductMutation($I, $basketId, $productId2);
        $this->basketRemoveMutation($I, $basketId);
    }

    public function testVoucherAssignedToSpecificCategory(AcceptanceTester $I): void
    {
        $categoryId = '0f4fb00809cec9aa0910aa9c8fe36751'; // Kites
        $productId  = 'b56369b1fc9d7b97f9c5fc343b349ece'; // Product from Kites category
        $I->haveInDatabase(
            'oxobject2discount',
            [
                'OXID'         => 'voucher_assigned_to_category',
                'OXDISCOUNTID' => 'personal_voucher',
                'OXOBJECTID'   => $categoryId,
                'OXTYPE'       => 'oxcategories',
            ]
        );

        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreateMutation($I, 'basket_voucher_category');
        $this->basketAddProductMutation($I, $basketId, self::PRODUCT_ID);
        $I->sendGQLQuery($this->addVoucherMutation($basketId, self::VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $I->assertEquals(
            'MESSAGE_COUPON_NOT_APPLIED_FOR_ARTICLES',
            $result['errors'][0]['message']
        );

        $this->basketAddProductMutation($I, $basketId, $productId);
        $I->sendGQLQuery($this->addVoucherMutation($basketId, self::VOUCHER));

        $I->seeInDatabase('oxvouchers', [
            'oxid'           => 'personal_voucher_1',
            'oxreserved >'   => 0,
            'oegql_basketid' => $basketId,
        ]);

        $I->sendGQLQuery($this->basketQuery($basketId));
        $result = $I->grabJsonResponseAsArray();
        $I->assertSame(
            [
                'id'       => $basketId,
                'cost'     => [
                    'voucher'  => 5,
                    'discount' => 5,
                ],
                'vouchers' => [
                    ['id' => 'personal_voucher_1'],
                ],
            ],
            $result['data']['basket']
        );

        $this->basketRemoveProductMutation($I, $basketId, $productId);

        $I->seeInDatabase('oxvouchers', [
            'oxid'           => 'personal_voucher_1',
            'oxreserved'     => 0,
            'oegql_basketid' => '',
        ]);

        // Reset DB
        $this->basketRemoveProductMutation($I, $basketId, self::PRODUCT_ID);
        $this->basketRemoveMutation($I, $basketId);
    }

    public function testAddVoucherWhichIsOutdated(AcceptanceTester $I): void
    {
        $I->updateInDatabase(
            'oxvoucherseries',
            ['oxenddate' => date('Y-m-d H:i:s', strtotime('-1 day'))],
            ['oxid'      => 'personal_voucher']
        );

        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreateMutation($I, 'outdated_voucher');
        $this->basketAddProductMutation($I, $basketId, self::PRODUCT_ID);
        $I->sendGQLQuery($this->addVoucherMutation($basketId, self::VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $I->assertEquals(
            'MESSAGE_COUPON_EXPIRED',
            $result['errors'][0]['message']
        );

        // Reset DB
        $this->basketRemoveProductMutation($I, $basketId, self::PRODUCT_ID);
        $this->basketRemoveMutation($I, $basketId);
        $I->updateInDatabase(
            'oxvoucherseries',
            ['oxenddate' => '2050-12-31 00:00:00'],
            ['oxid'      => 'personal_voucher']
        );
    }

    public function testAddVoucherWhichIsNotStarted(AcceptanceTester $I): void
    {
        $I->updateInDatabase(
            'oxvoucherseries',
            ['oxbegindate' => date('Y-m-d H:i:s', strtotime('+1 day'))],
            ['oxid'        => 'personal_voucher']
        );

        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreateMutation($I, 'outdated_voucher');
        $this->basketAddProductMutation($I, $basketId, self::PRODUCT_ID);
        $I->sendGQLQuery($this->addVoucherMutation($basketId, self::VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $I->assertEquals(
            'ERROR_MESSAGE_VOUCHER_NOVOUCHER',
            $result['errors'][0]['message']
        );

        // Reset DB
        $this->basketRemoveProductMutation($I, $basketId, self::PRODUCT_ID);
        $this->basketRemoveMutation($I, $basketId);
        $I->updateInDatabase(
            'oxvoucherseries',
            ['oxbegindate' => '2000-01-01 00:00:00'],
            ['oxid'        => 'personal_voucher']
        );
    }

    public function testVoucherWhichWillOutDate(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        // Add voucher to basket
        $basketId = $this->basketCreateMutation($I, 'outdated_voucher');
        $this->basketAddProductMutation($I, $basketId, self::PRODUCT_ID);
        $I->sendGQLQuery($this->addVoucherMutation($basketId, self::VOUCHER));

        // Update the voucher and make it invalid
        $I->updateInDatabase(
            'oxvoucherseries',
            ['oxenddate' => date('Y-m-d H:i:s', strtotime('-1 day'))],
            ['oxid'      => 'personal_voucher']
        );

        // Get basket data
        $I->sendGQLQuery($this->basketQuery($basketId));
        $result = $I->grabJsonResponseAsArray();

        // Check voucher data
        $I->assertSame($result['data']['basket'], [
            'id'   => $basketId,
            'cost' => [
                'voucher'  => 0,
                'discount' => 0,
            ],
            'vouchers' => [],
        ]);

        // Reset DB
        $this->basketRemoveProductMutation($I, $basketId, self::PRODUCT_ID);
        $this->basketRemoveMutation($I, $basketId);
        $I->updateInDatabase(
            'oxvoucherseries',
            ['oxenddate' => '2050-12-31 00:00:00'],
            ['oxid'      => 'personal_voucher']
        );
    }

    public function testVoucherWithMinOrderValue(AcceptanceTester $I): void
    {
        $I->updateInDatabase(
            'oxvoucherseries',
            ['oxminimumvalue' => 50.00],
            ['oxid'           => 'personal_voucher']
        );

        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreateMutation($I, 'basket_with_min_voucher');
        $this->basketAddProductMutation($I, $basketId, self::PRODUCT_ID);
        $I->sendGQLQuery($this->addVoucherMutation($basketId, self::VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $I->assertEquals(
            'ERROR_MESSAGE_VOUCHER_INCORRECTPRICE',
            $result['errors'][0]['message']
        );

        $this->basketAddProductMutation($I, $basketId, self::PRODUCT_ID);
        $I->sendGQLQuery($this->addVoucherMutation($basketId, self::VOUCHER));

        $I->seeInDatabase('oxvouchers', [
            'oxid'           => 'personal_voucher_1',
            'oxreserved >'   => 0,
            'oegql_basketid' => $basketId,
        ]);

        $this->basketRemoveProductMutation($I, $basketId, self::PRODUCT_ID);

        $I->seeInDatabase('oxvouchers', [
            'oxid'           => 'personal_voucher_1',
            'oxreserved'     => 0,
            'oegql_basketid' => '',
        ]);

        // Reset DB
        $this->basketRemoveMutation($I, $basketId);
        $I->updateInDatabase(
            'oxvoucherseries',
            ['oxminimumvalue' => 0.00],
            ['oxid'           => 'personal_voucher']
        );
    }

    public function testVoucherAssignedToUserGroup(AcceptanceTester $I): void
    {
        // Assign group to the voucher
        $I->haveInDatabase(
            'oxobject2group',
            [
                'OXID'       => 'voucher_assigned_to_user_group',
                'OXSHOPID'   => 1,
                'OXOBJECTID' => 'personal_voucher',
                'OXGROUPSID' => 'oxidcustomer',
            ]
        );

        // Add voucher with user which is not into the group
        $I->login(self::OTHER_USERNAME, self::PASSWORD);

        $basketId = $this->basketCreateMutation($I, 'voucher_assigned_to_user_group');
        $this->basketAddProductMutation($I, $basketId, self::PRODUCT_ID);
        $I->sendGQLQuery($this->addVoucherMutation($basketId, self::VOUCHER));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $I->assertEquals(
            'ERROR_MESSAGE_VOUCHER_NOTVALIDUSERGROUP',
            $result['errors'][0]['message']
        );

        // Reset DB
        $this->basketRemoveProductMutation($I, $basketId, self::PRODUCT_ID);
        $this->basketRemoveMutation($I, $basketId);

        // Add voucher with user which is into the group
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreateMutation($I, 'voucher_assigned_to_user_group');
        $this->basketAddProductMutation($I, $basketId, self::PRODUCT_ID);
        $I->sendGQLQuery($this->addVoucherMutation($basketId, self::VOUCHER));

        // Reset DB
        $this->basketRemoveProductMutation($I, $basketId, self::PRODUCT_ID);
        $this->basketRemoveMutation($I, $basketId);
    }

    public function testBasketWithTimedOutVoucherReservation(AcceptanceTester $I): void
    {
        $I->wantToTest('basket with voucher reservation timed out');
        $I->updateConfigInDatabase('iVoucherTimeout', 10800, 'int'); //shop's default value
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery($this->addVoucherMutation(self::BASKET, self::VOUCHER));

        $I->sendGQLQuery($this->basketQuery(self::BASKET));
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $I->assertNotEmpty($result['data']['basket']['vouchers']);

        //Voucher outdated after basket was created but before order is placed
        $I->updateInDatabase(
            'oxvouchers',
            [
                'oxreserved'     => time() - 10900,
            ],
            [
                'oegql_basketid' => self::BASKET,
            ]
        );

        $I->sendGQLQuery($this->basketQuery(self::BASKET));

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $I->assertEmpty($result['data']['basket']['vouchers']);
    }

    private function basketRemoveProductMutation(AcceptanceTester $I, string $basketId, string $productId, int $amount = 1): void
    {
        $I->sendGQLQuery('mutation {
            basketRemoveProduct(basketId: "' . $basketId . '", productId: "' . $productId . '", amount: ' . $amount . ') {
                id
            }
        }');

        $I->seeResponseIsJson();
    }

    private function addVoucherMutation(string $basketId, string $voucherNumber)
    {
        return 'mutation {
            basketAddVoucher (
                basketId: "' . $basketId . '",
                voucherNumber: "' . $voucherNumber . '"
            ) {
                id
            }
        }';
    }

    private function removeVoucherMutation(string $basketId, string $voucherId)
    {
        return 'mutation {
            basketRemoveVoucher (
                basketId: "' . $basketId . '",
                voucherId: "' . $voucherId . '"
            ) {
                id
            }
        }';
    }

    private function basketQuery(string $basketId)
    {
        return 'query {
            basket(id: "' . $basketId . '") {
                id
                cost {
                    voucher
                    discount
                }
                vouchers {
                    id
                }
            }
        }';
    }

    private function basketCreateMutation(AcceptanceTester $I, string $title): string
    {
        $I->sendGQLQuery('mutation {
            basketCreate(basket: {title: "' . $title . '", public: false}) {
                id
            }
        }');

        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();

        return $result['data']['basketCreate']['id'];
    }

    private function basketRemoveMutation(AcceptanceTester $I, string $basketId): void
    {
        $I->sendGQLQuery('mutation {
            basketRemove(id: "' . $basketId . '")
        }');

        $I->seeResponseIsJson();
    }

    private function basketAddProductMutation(AcceptanceTester $I, string $basketId, string $productId, int $amount = 1): void
    {
        $I->sendGQLQuery('mutation {
            basketAddProduct(basketId: "' . $basketId . '", productId: "' . $productId . '", amount: ' . $amount . ') {
                id
            }
        }');

        $I->seeResponseIsJson();
    }

    private function prepareVoucherInBasket(AcceptanceTester $I): void
    {
        $this->prepareVoucher($I, '', 'personal_series_voucher_1');
        $this->prepareVoucher($I, '', 'personal_series_voucher_2');
    }

    private function prepareSeriesVouchers(AcceptanceTester $I, string $voucherId): void
    {
        $I->updateInDatabase('oxvoucherseries', [
            'OXALLOWSAMESERIES'  => 1,
            'OXALLOWOTHERSERIES' => 1,
        ], [
            'OXID' => $voucherId,
        ]);
    }

    private function prepareVoucher(AcceptanceTester $I, string $basketId, string $voucherId): void
    {
        $I->updateInDatabase('oxvouchers', [
            'OXRESERVED'     => $basketId ? time() : 0,
            'OEGQL_BASKETID' => $basketId,
        ], [
            'OXID' => $voucherId,
        ]);
    }
}
