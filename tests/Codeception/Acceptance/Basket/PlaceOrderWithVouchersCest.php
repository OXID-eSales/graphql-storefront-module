<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group oe_graphql_checkout
 * @group place_order
 * @group place_order_with_vouchers
 * @group basket
 */
final class PlaceOrderWithVouchersCest extends PlaceOrderBaseCest
{
    public function _after(AcceptanceTester $I): void
    {
        $I->updateInDatabase(
            'oxvouchers',
            [
                'OXDATEUSED'       => null,
                'OXORDERID'        => '',
                'OXUSERID'         => '',
                'OXRESERVED'       => 0,
                'OEGQL_BASKETID'   => 'null',
            ],
            [
                'OXUSERID'          => self::USER_OXID,
            ]
        );

        parent::_after($I);
    }

    public function placeOrderWithVouchers(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with vouchers');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->prepareBasket($I, 'cart_with_voucher');
        $this->addVoucherToBasket($I, $basketId, 'voucher1x');

        //check the basket costs
        $basketCosts = $this->checkBasketCosts($I, $basketId);

        //place the order
        $result  = $this->placeOrder($I, $basketId);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orderId, $orders['id']);
        $I->assertEquals($basketCosts['total'], $orders['cost']['total']);
        $I->assertEquals($basketCosts['voucher'], $orders['cost']['voucher']);
        $I->assertEquals($orders['vouchers'][0]['id'], 'voucher1xid');
        $I->assertNotEmpty($orders['invoiceAddress']);
        $I->assertNull($orders['deliveryAddress']);

        //check voucher status in database
        $I->seeInDatabase(
            'oxvouchers',
            [
                'oxid'           => 'voucher1xid',
                'oxorderid'      => $orderId,
                'oxreserved >'   => 0,

                'oegql_basketid' => $basketId,
            ]
        );

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
    }

    public function placeOrderWithTimedOutVoucherReservation(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with voucher that outdated before placing order');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->prepareBasket($I, 'outdated_voucher');
        $this->addVoucherToBasket($I, $basketId, 'voucher1x');
        $basketCosts = $this->checkBasketCosts($I, $basketId);

        //Voucher outdated after basket was created but before order is placed
        $I->updateInDatabase(
            'oxvouchers',
            [
                'oxreserved'     => time() - 10900,
            ],
            [
                'oxid'           => 'voucher1xid',
                'oegql_basketid' => $basketId,
            ]
        );

        //place the order
        $this->checkPlaceOrderRemovesVoucher($I, $basketId, $basketCosts);

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
    }

    public function placeOrderWithOutdatedVoucherSeries(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with voucher series outdated before placing order');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->prepareBasket($I, 'outdated_voucherseries');
        $this->addVoucherToBasket($I, $basketId, 'voucher1x');
        $basketCosts = $this->checkBasketCosts($I, $basketId);

        $I->updateInDatabase(
            'oxvoucherseries',
            [
                'OXENDDATE' => '2000-01-01',
            ],
            [
                'oxid' => 'voucherserie1x',
            ]
        );

        //place the order
        $this->checkPlaceOrderRemovesVoucher($I, $basketId, $basketCosts);

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
    }

    public function placeOrderWithProductVoucherAssignedToProductInBasket(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with product voucher assigned to basket product');
        $I->login(self::USERNAME, self::PASSWORD);

        $I->haveInDatabase(
            'oxobject2discount',
            [
                'OXID'         => 'voucher_assigned_to_product',
                'OXDISCOUNTID' => 'my_personal_voucher',
                'OXOBJECTID'   => self::PRODUCT_ID,
                'OXTYPE'       => 'oxarticles',
            ]
        );

        $basketId = $this->prepareBasket($I, 'product_voucher_yes');
        $this->addVoucherToBasket($I, $basketId, 'myPersonalVoucher');
        $basketCosts = $this->checkBasketCosts($I, $basketId);

        //place the order
        $this->checkPlaceOrder($I, $basketId, $basketCosts, 'my_personal_voucher_1');

        //remove basket and voucher relation
        $this->removeBasket($I, $basketId, self::USERNAME);
        $I->deleteFromDatabase('oxobject2discount', ['OXID' => 'voucher_assigned_to_product']);
    }

    public function placeOrderWithProductVoucherNoLongerAssignedToProductInBasket(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with product voucher no longer assigned to basket product');
        $I->login(self::USERNAME, self::PASSWORD);

        $I->haveInDatabase(
            'oxobject2discount',
            [
                'OXID'         => 'voucher_assigned_to_product',
                'OXDISCOUNTID' => 'product_voucher',
                'OXOBJECTID'   => self::PRODUCT_ID,
                'OXTYPE'       => 'oxarticles',
            ]
        );
        $I->haveInDatabase(
            'oxobject2discount',
            [
                'OXID'         => 'voucher_assigned_to_otherproduct',
                'OXDISCOUNTID' => 'product_voucher',
                'OXOBJECTID'   => 'other_product_id',
                'OXTYPE'       => 'oxarticles',
            ]
        );

        $basketId = $this->prepareBasket($I, 'product_voucher_no');
        $this->addVoucherToBasket($I, $basketId, 'productVoucher');
        $basketCosts = $this->checkBasketCosts($I, $basketId);

        //voucher is still related to other product which is not in our cart
        $I->deleteFromDatabase('oxobject2discount', ['OXID' => 'voucher_assigned_to_product']);

        //place the order
        $this->checkPlaceOrderRemovesVoucher($I, $basketId, $basketCosts, 'product_voucher_1');

        //remove basket and discount relation
        $this->removeBasket($I, $basketId, self::USERNAME);
        $I->deleteFromDatabase('oxobject2discount', ['OXID' => 'voucher_assigned_to_otherproduct']);
    }

    public function placeOrderWithVoucherAssignedToSpecificCategory(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with voucher assigned to specific category');
        $I->login(self::USERNAME, self::PASSWORD);

        $I->haveInDatabase(
            'oxobject2discount',
            [
                'OXID'         => 'voucher_assigned_to_category',
                'OXDISCOUNTID' => 'personal_voucher',
                'OXOBJECTID'   => self::CATEGORY_ID,
                'OXTYPE'       => 'oxcategories',
            ]
        );

        $basketId = $this->prepareBasket($I, 'category_voucher_yes');
        $this->addVoucherToBasket($I, $basketId, 'myPersonalVoucher');
        $basketCosts = $this->checkBasketCosts($I, $basketId);

        //place the order
        $this->checkPlaceOrder($I, $basketId, $basketCosts, 'my_personal_voucher_1');

        //remove basket and discount relation
        $this->removeBasket($I, $basketId, self::USERNAME);
        $I->deleteFromDatabase('oxobject2discount', ['OXID' => 'voucher_assigned_to_category']);
    }

    public function placeOrderWithVoucherNoLongerAssignedToSpecificCategory(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with voucher no longer assigned to specific category');
        $I->login(self::USERNAME, self::PASSWORD);

        $I->haveInDatabase(
            'oxobject2discount',
            [
                'OXID'         => 'voucher_assigned_to_category',
                'OXDISCOUNTID' => 'category_voucher',
                'OXOBJECTID'   => self::CATEGORY_ID,
                'OXTYPE'       => 'oxcategories',
            ]
        );

        $basketId = $this->prepareBasket($I, 'category_voucher_no');
        $this->addVoucherToBasket($I, $basketId, 'categoryVoucher');
        $basketCosts = $this->checkBasketCosts($I, $basketId);

        //remove product from category
        $I->updateInDatabase(
            'oxobject2category',
            [
                'OXOBJECTID' => 'other_product',
                'OXCATNID'   => self::CATEGORY_ID,
            ],
            [
                'OXOBJECTID' => self::PRODUCT_ID,
                'OXCATNID'   => self::CATEGORY_ID,
            ]
        );

        //place the order
        $this->checkPlaceOrderRemovesVoucher($I, $basketId, $basketCosts, 'category_voucher_1');

        //remove basket and discount relation, restore category relation
        $this->removeBasket($I, $basketId, self::USERNAME);
        $I->deleteFromDatabase('oxobject2discount', ['OXID' => 'voucher_assigned_to_category']);

        $I->updateInDatabase(
            'oxobject2category',
            [
                'OXOBJECTID' => self::PRODUCT_ID,
                'OXCATNID'   => self::CATEGORY_ID,
            ],
            [
                'OXOBJECTID' => 'other_product',
                'OXCATNID'   => self::CATEGORY_ID,
            ]
        );
    }

    public function placeOrderWithVoucherAssignedToUserGroup(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with voucher assigned to user group');
        $I->login(self::USERNAME, self::PASSWORD);

        $I->haveInDatabase(
            'oxobject2group',
            [
                'OXID'       => 'voucher_assigned_to_user_group',
                'OXSHOPID'   => 1,
                'OXOBJECTID' => 'my_personal_voucher',
                'OXGROUPSID' => 'oxidcustomer',
            ]
        );

        $basketId = $this->prepareBasket($I, 'usergroup_voucher_yes');
        $this->addVoucherToBasket($I, $basketId, 'myPersonalVoucher');
        $basketCosts = $this->checkBasketCosts($I, $basketId);

        //place the order
        $this->checkPlaceOrder($I, $basketId, $basketCosts, 'my_personal_voucher_1');

        //remove basket and discount relation
        $this->removeBasket($I, $basketId, self::USERNAME);
        $I->deleteFromDatabase('oxobject2discount', ['OXID' => 'voucher_assigned_to_usergroup']);
    }

    public function placeOrderWithVoucherAssignedToUserGroupButUserNoLongerInThatGroup(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with voucher assigned to user group but user unassigned to group now');
        $I->login(self::USERNAME, self::PASSWORD);

        $I->haveInDatabase(
            'oxobject2group',
            [
                'OXID'       => 'voucher_assigned_to_user_group',
                'OXSHOPID'   => 1,
                'OXOBJECTID' => 'user_voucher',
                'OXGROUPSID' => 'oxidgoodcust',
            ]
        );

        $basketId = $this->prepareBasket($I, 'user_voucher_no');
        $this->addVoucherToBasket($I, $basketId, 'userVoucher');
        $basketCosts = $this->checkBasketCosts($I, $basketId);

        //remove user from group
        $I->updateInDatabase(
            'oxobject2group',
            [
                'OXOBJECTID' => 'other_user_id',
                'OXGROUPSID' => 'oxidgoodcust',
            ],
            [
                'OXOBJECTID' => self::USER_OXID,
                'OXGROUPSID' => 'oxidgoodcust',
            ]
        );

        //place the order
        $this->checkPlaceOrderRemovesVoucher($I, $basketId, $basketCosts, 'user_voucher_1');

        //remove basket and discount relation, restore user group relation
        $this->removeBasket($I, $basketId, self::USERNAME);
        $I->deleteFromDatabase('oxobject2discount', ['OXID' => 'voucher_assigned_to_usergroup']);
        $I->updateInDatabase(
            'oxobject2group',
            [
                'OXOBJECTID' => self::USER_OXID,
                'OXGROUPSID' => 'oxidgoodcust',
            ],
            [
                'OXOBJECTID' => 'other_user_id',
                'OXGROUPSID' => 'oxidgoodcust',
            ]
        );
    }

    public function placeOrderWithVoucherWithMinOrderValue(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with voucher with min order value and order cost above that value');
        $I->login(self::USERNAME, self::PASSWORD);

        $I->updateInDatabase(
            'oxvoucherseries',
            ['oxminimumvalue' => 20.00],
            ['oxid'           => 'my_personal_voucher']
        );

        $basketId = $this->prepareBasket($I, 'minvalue_voucher_ok');
        $this->addVoucherToBasket($I, $basketId, 'myPersonalVoucher');
        $basketCosts = $this->checkBasketCosts($I, $basketId);

        //place the order
        $this->checkPlaceOrder($I, $basketId, $basketCosts, 'my_personal_voucher_1');

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
    }

    public function placeOrderWithVoucherWithMinOrderValueNotReached(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with voucher with min order value and order cost below that value');
        $I->login(self::USERNAME, self::PASSWORD);

        $I->updateInDatabase(
            'oxvoucherseries',
            ['oxminimumvalue' => 20.00],
            ['oxid'           => 'minvalue_voucher']
        );

        $basketId = $this->prepareBasket($I, 'minvalue_voucher');
        $this->addVoucherToBasket($I, $basketId, 'minvalueVoucher');
        $basketCosts = $this->checkBasketCosts($I, $basketId);

        //change voucher so that order does no longer reach minimum value
        $I->updateInDatabase(
            'oxvoucherseries',
            ['oxminimumvalue' => 100.00],
            ['oxid'           => 'minvalue_voucher']
        );

        //place the order
        $this->checkPlaceOrderRemovesVoucher($I, $basketId, $basketCosts, 'minvalue_voucher_1');

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
    }

    public function placeOrderWithDeletedVoucher(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with voucher that has been removed after basket creation');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->prepareBasket($I, 'cart_for_deleted_voucher');
        $this->addVoucherToBasket($I, $basketId, 'myDeleteVoucher');

        //check the basket costs
        $basketCosts = $this->checkBasketCosts($I, $basketId);

        //remove voucher
        $I->deleteFromDatabase(
            'oxvouchers',
            [
                'oxid' => 'my_delete_voucher_1',
            ]
        );

        $result  = $this->placeOrder($I, $basketId);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orderId, $orders['id']);
        $I->assertEquals($basketCosts['total'] + 5, $orders['cost']['total']);
        $I->assertEquals(0, $orders['cost']['voucher']);
        $I->assertEmpty($orders['vouchers']);

        //remove basket, restore voucher
        $this->removeBasket($I, $basketId, self::USERNAME);
        $I->haveInDatabase(
            'oxvouchers',
            [
                'OXDATEUSED'       => null,
                'OXORDERID'        => '',
                'OXUSERID'         => '',
                'OXRESERVED'       => 0,
                'OXVOUCHERNR'      => 'myDeleteVoucher',
                'OXVOUCHERSERIEID' => 'delete_voucher',
                'OXDISCOUNT'       => 5,
                'OXID'             => 'my_delete_voucher_1',
                'OXTIMESTAMP'      => date('Y-m-d', strtotime('-1 day')),
                'OEGQL_BASKETID'   => 'null',
            ]
        );
    }

    private function prepareBasket(AcceptanceTester $I, string $basketName): string
    {
        //prepare basket
        $basketId = $this->createBasket($I, $basketName);
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        return $basketId;
    }

    private function checkBasketCosts(AcceptanceTester $I, string $basketId): array
    {
        //check the basket costs
        $basketCosts = $this->queryBasketCost($I, $basketId);
        $I->assertEquals(29.9, $basketCosts['productGross']['sum']);
        $I->assertEquals(7.5, $basketCosts['payment']['price']);
        $I->assertEquals(3.9, $basketCosts['delivery']['price']);
        $I->assertEquals(5, $basketCosts['voucher']);
        $I->assertEquals(5, $basketCosts['discount']); //this is sum of all discounts, including vouchers
        $I->assertEquals(36.3, $basketCosts['total']);

        return $basketCosts;
    }

    private function checkPlaceOrder(AcceptanceTester $I, string $basketId, array $basketCosts, string $voucherId = 'voucher1id'): void
    {
        //place the order
        $result  = $this->placeOrder($I, $basketId);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orderId, $orders['id']);
        $I->assertEquals($basketCosts['total'], $orders['cost']['total']);
        $I->assertEquals($basketCosts['voucher'], $orders['cost']['voucher']);
        $I->assertEquals($orders['vouchers'][0]['id'], $voucherId);
        $I->assertNotEmpty($orders['invoiceAddress']);
        $I->assertNull($orders['deliveryAddress']);

        //check voucher status in database
        $I->seeInDatabase(
            'oxvouchers',
            [
                'oxid'           => $voucherId,
                'oxorderid'      => $orderId,
                'oxreserved >'   => 0,
                'oegql_basketid' => $basketId,
            ]
        );
    }

    private function checkPlaceOrderRemovesVoucher(
        AcceptanceTester $I,
        string $basketId,
        array $basketCosts,
        string $voucherId = 'voucher1xid'
    ): void {
        //place the order
        $result  = $this->placeOrder($I, $basketId);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history, voucher was not applied
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orderId, $orders['id']);
        $I->assertEquals($basketCosts['total'] + 5, $orders['cost']['total']);
        $I->assertEquals(0, $orders['cost']['voucher']);
        $I->assertEmpty($orders['vouchers']);

        //voucher should have been unreserved
        $I->seeInDatabase(
            'oxvouchers',
            [
                'oxid'           => $voucherId,
                'oxorderid'      => '',
                'oxreserved'     => 0,
                'oegql_basketid' => '',
            ]
        );
    }
}
