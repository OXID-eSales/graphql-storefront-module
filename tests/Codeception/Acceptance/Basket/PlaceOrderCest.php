<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use OxidEsales\GraphQL\Storefront\Basket\Exception\PlaceOrder;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\Exception\UnavailableDeliveryMethod;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;
use TheCodingMachine\GraphQLite\Middlewares\MissingAuthorizationException;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @group oe_graphql_checkout
 * @group place_order
 * @group basket
 * @group oe_graphql_storefront
 */
final class PlaceOrderCest extends PlaceOrderBaseCest
{
    public function placeOrderWithRemark(AcceptanceTester $I): void
    {
        $I->wantToTest('Placing an order with remark');
        $I->login(self::USERNAME, self::PASSWORD, 0);

        //prepare basket
        $basketId = $this->createBasket($I, 'my_cart_one');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 2);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $remark = "some remark";
        $result  = $this->placeOrder($I, $basketId, null, $remark);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orderId, $orders['id']);
        $I->assertEquals($remark, $orders['remark']);
    }

    /**
     * @group allowed_to_fail_for_anonymous_token
     */
    public function placeOrderWithAnonymousUser(AcceptanceTester $I): void
    {
        $I->wantToTest('anonymous user is placing an order');
        $I->login(self::USERNAME, self::PASSWORD, 0);

        //prepare basket
        $basketId = $this->createBasket($I, 'my_anonymous_cart');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 2);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $I->logout();
        $I->login(null, null, 0);
        $result = $this->placeOrder($I, $basketId);

        $I->assertEquals(
            MissingAuthorizationException::forbidden()->getMessage(),
            $result['errors'][0]['message']
        );

        $I->login(self::USERNAME, self::PASSWORD, 0);
        $this->removeBasket($I, $basketId);
    }

    public function placeOrderUsingInvoiceAddress(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order successfully with invoice address only');
        $I->login(self::USERNAME, self::PASSWORD, 0);

        //prepare basket
        $basketId = $this->createBasket($I, 'my_cart_one');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 2);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //check the basket costs
        $basketCosts = $this->queryBasketCost($I, $basketId);
        $I->assertEquals(59.8, $basketCosts['productGross']['sum']);
        $I->assertEquals(7.5, $basketCosts['payment']['price']);
        $I->assertEquals(3.9, $basketCosts['delivery']['price']);
        $I->assertEquals(0, $basketCosts['voucher']);
        $I->assertEquals(0, $basketCosts['discount']);
        $I->assertEquals(71.2, $basketCosts['total']);

        //place the order
        $result  = $this->placeOrder($I, $basketId);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orderId, $orders['id']);
        $I->assertEquals($basketCosts['total'], $orders['cost']['total']);
        $I->assertNotEmpty($orders['invoiceAddress']);
        $I->assertNull($orders['deliveryAddress']);
    }

    public function placeOrderUsingInvoiceAddressAndDefaultSavedBasket(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order for savedbasket and blPerfNoBasketSaving');

        $I->updateConfigInDatabase('blPerfNoBasketSaving', true, 'bool');
        $I->login(self::CHECKOUT_USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, self::DEFAULT_SAVEDBASKET);
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 2);
        $this->setBasketDeliveryMethod($I, $basketId, self::TEST_SHIPPING);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_TEST);

        //check the basket costs
        $basketCosts = $this->queryBasketCost($I, $basketId);
        $I->assertEquals(59.8, $basketCosts['productGross']['sum']);
        $I->assertEquals(7.77, $basketCosts['payment']['price']);
        $I->assertEquals(6.66, $basketCosts['delivery']['price']);
        $I->assertEquals(0, $basketCosts['voucher']);
        $I->assertEquals(0, $basketCosts['discount']);
        $I->assertEquals(74.23, $basketCosts['total']);

        //place the order
        $result  = $this->placeOrder($I, $basketId);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orderId, $orders['id']);
        $I->assertEquals($basketCosts['total'], $orders['cost']['total']);
        $I->assertNotEmpty($orders['invoiceAddress']);
        $I->assertNull($orders['deliveryAddress']);
    }

    public function placeOrderRemovesCorrectBasket(AcceptanceTester $I): void
    {
        $I->wantToTest('savedbasket(default basket) should not be removed on other basket order');

        $I->login(self::CHECKOUT_USERNAME, self::PASSWORD);

        //prepare default basket
        $defaultBasketId = $this->createBasket($I, self::DEFAULT_SAVEDBASKET);
        $this->addItemToBasket($I, $defaultBasketId, self::PRODUCT_ID, 2);

        //prepare ordering basket
        $basketId = $this->createBasket($I, 'specialBasket');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 2);
        $this->setBasketDeliveryMethod($I, $basketId, self::TEST_SHIPPING);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_TEST);

        //place the order
        $this->placeOrder($I, $basketId);

        $I->assertFalse($this->basketExists($I, $basketId));
        $I->assertTrue($this->basketExists($I, $defaultBasketId));
    }

    public function placeOrderUsingDeliveryAddress(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order successfully with delivery address');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'my_cart_two');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 2);
        $this->setBasketDeliveryAddress($I, $basketId);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result  = $this->placeOrder($I, $basketId);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orderId, $orders['id']);
        $I->assertNotEmpty($orders['invoiceAddress']);
        $I->assertNotEmpty($orders['deliveryAddress']);
    }

    public function placeOrderWithoutToken(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order when logged out');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'my_cart_three');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 2);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //log out
        $I->logout();

        //place the order
        $result = $this->placeOrder($I, $basketId);

        $I->assertStringStartsWith(
            MissingAuthorizationException::forbidden()->getMessage(),
            $result['errors'][0]['message']
        );

        //remove basket
        $I->login(self::USERNAME, self::PASSWORD);
        $this->removeBasket($I, $basketId);
    }

    public function placeOtherUsersOrder(AcceptanceTester $I): void
    {
        $I->wantToTest('placing another users order');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'my_cart_four');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 2);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //log out
        $I->logout();

        //log in different user and place the order
        $I->login(self::OTHER_USERNAME, self::PASSWORD);
        $result = $this->placeOrder($I, $basketId);

        $I->assertSame(
            'You are not allowed to access this basket as it belongs to somebody else',
            $result['errors'][0]['message']
        );

        //remove basket
        $I->login(self::USERNAME, self::PASSWORD);
        $this->removeBasket($I, $basketId);
    }

    public function placeOrderWithEmptyBasket(AcceptanceTester $I): void
    {
        $I->wantToTest('that placing an order with empty basket fails');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, self::EMPTY_BASKET_NAME);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result = $this->placeOrder($I, $basketId);

        $I->assertSame(
            'Order cannot be placed. Basket with id: ' . $basketId . ' is empty',
            $result['errors'][0]['message']
        );

        //remove basket
        $this->removeBasket($I, $basketId);
    }

    public function prepareOrderWithNoShippingMethodForCountry(AcceptanceTester $I): void
    {
        $I->wantToTest('that using delivery address with unsupported country fails');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket with invoice address
        $basketId = $this->createBasket($I, 'my_cart_five');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 3);
        $this->setBasketDeliveryAddress($I, $basketId, self::ALTERNATE_COUNTRY);

        //shipping method not supported
        $errorMessage         = $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $expectedError        = UnavailableDeliveryMethod::byId(self::SHIPPING_STANDARD);
        $expectedErrorMessage = $expectedError->getMessage();
        $I->assertEquals($expectedErrorMessage, $errorMessage);

        //remove basket
        $this->removeBasket($I, $basketId);
    }

    public function placeOrderWithChangedDeliveryAddress(AcceptanceTester $I): void
    {
        $I->wantToTest('that placing an order with changed delivery address fails');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket with german delivery address
        $basketId = $this->createBasket($I, 'my_cart_six');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 3);
        $this->setBasketDeliveryAddress($I, $basketId); //Germany
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //this country is not supported for chosen shipping method
        $this->setBasketDeliveryAddress($I, $basketId, self::ALTERNATE_COUNTRY);

        //place the order
        $result = $this->placeOrder($I, $basketId);

        $I->assertSame(
            "Delivery set '" . self::SHIPPING_STANDARD . "' is unavailable!",
            $result['errors'][0]['message']
        );

        //remove basket
        $this->removeBasket($I, $basketId);
    }

    public function placeOrderWithUnavailablePayment(AcceptanceTester $I): void
    {
        $I->wantToTest('that placing an order with unavailable payment fails');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket with invoice address
        $basketId = $this->createBasket($I, 'my_cart_seven');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 3);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        $I->updateInDatabase('oxuserbaskets', ['oegql_paymentid' => self::PAYMENT_TEST], ['oxid' => $basketId]);

        //place the order
        $result = $this->placeOrder($I, $basketId);

        $I->assertSame(
            "Payment method '" . self::PAYMENT_TEST . "' is unavailable!",
            $result['errors'][0]['message']
        );

        //remove basket
        $this->removeBasket($I, $basketId);
    }

    public function placeOrderWithDiscountedProduct(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with a discounted product');
        $I->login(self::USERNAME, self::PASSWORD);

        // get 10% off from 200 EUR product value on
        $I->updateInDatabase('oxdiscount', ['oxactive' => 0]);
        $I->updateInDatabase('oxdiscount', ['oxactive' => 1], ['oxid' => self::DISCOUNT_ID]);

        //prepare basket
        $basketId = $this->createBasket($I, 'cart_with_discount');
        $this->addItemToBasket($I, $basketId, self::DISCOUNT_PRODUCT, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //check the basket costs
        $basketCosts = $this->queryBasketCost($I, $basketId);
        $I->assertEquals(479.0, $basketCosts['productGross']['sum']);
        $I->assertEquals(7.5, $basketCosts['payment']['price']);
        $I->assertEquals(0.0, $basketCosts['delivery']['price']);
        $I->assertEquals(0.0, $basketCosts['voucher']);
        $I->assertEquals(47.9, $basketCosts['discount']); //this is sum of all discounts, including vouchers
        $I->assertEquals(438.6, $basketCosts['total']);

        //place the order
        $result  = $this->placeOrder($I, $basketId);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orderId, $orders['id']);
        $I->assertEquals($basketCosts['total'], $orders['cost']['total']);
        $I->assertEquals($basketCosts['discount'], $orders['cost']['discount']);
        $I->assertEquals($basketCosts['voucher'], $orders['cost']['voucher']);

        $I->updateInDatabase('oxdiscount', ['oxactive' => 0]);
        $I->updateInDatabase('oxdiscount', ['oxactive' => 1], ['oxid' => self::DEFAULT_DISCOUNT_ID]);
    }

    public function placeOrderAndNoCalculateDelCostIfNotLoggedIn(AcceptanceTester $I): void
    {
        $I->wantToTest('that blCalculateDelCostIfNotLoggedIn has no effect on placeOrder');
        $I->updateConfigInDatabase('blCalculateDelCostIfNotLoggedIn', true, 'bool');

        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket with invoice address
        $basketId = $this->createBasket($I, 'my_cart_del_cost_flag');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 2);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //check the basket costs
        $basketCosts = $this->queryBasketCost($I, $basketId);
        $I->assertEquals(59.8, $basketCosts['productGross']['sum']);
        $I->assertEquals(7.5, $basketCosts['payment']['price']);
        $I->assertEquals(3.9, $basketCosts['delivery']['price']);
        $I->assertEquals(0, $basketCosts['voucher']);
        $I->assertEquals(0, $basketCosts['discount']);
        $I->assertEquals(71.2, $basketCosts['total']);

        //place the order
        $result  = $this->placeOrder($I, $basketId);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orderId, $orders['id']);
        $I->assertEquals($basketCosts['total'], $orders['cost']['total']);
    }

    /**
     * @group agb
     */
    public function placeOrderWithConfirmAGB(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with required and confirmed ABG');

        $I->updateConfigInDatabase('blConfirmAGB', true);
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'cart_with_agb_given');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result  = $this->placeOrder($I, $basketId, true);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orders['id'], $orderId);
        $I->assertEquals($orders['cost']['total'], 41.3);

        $I->updateConfigInDatabase('blConfirmAGB', false);
    }

    /**
     * @group agb
     */
    public function placeOrderWithConfirmAGBNotGiven(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with required and not given ABG');

        $I->updateConfigInDatabase('blConfirmAGB', true);
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket with invoice address
        $basketId = $this->createBasket($I, 'cart_without_agb_given');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result               = $this->placeOrder($I, $basketId);
        $actualErrorMessage   = (string) $result['errors'][0]['message'];
        $expectedErrorMessage = PlaceOrder::notAcceptedTOS($basketId)->getMessage();
        $I->assertEquals($expectedErrorMessage, $actualErrorMessage);

        //remove basket
        $this->removeBasket($I, $basketId);
        $I->updateConfigInDatabase('blConfirmAGB', false);
    }

    /**
     * @group agb
     */
    public function placeOrderWithConfirmAGBRefused(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with required and refused ABG');

        $I->updateConfigInDatabase('blConfirmAGB', true);
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket with invoice address
        $basketId = $this->createBasket($I, 'cart_with_agb_refused');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result               = $this->placeOrder($I, $basketId, false);
        $actualErrorMessage   = (string) $result['errors'][0]['message'];
        $expectedErrorMessage = PlaceOrder::notAcceptedTOS($basketId)->getMessage();
        $I->assertEquals($expectedErrorMessage, $actualErrorMessage);

        //remove basket
        $this->removeBasket($I, $basketId);
        $I->updateConfigInDatabase('blConfirmAGB', false);
    }

    /**
     * @group agb
     */
    public function placeOrderWithConfirmAGBNull(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with required and set as null ABG');

        $I->updateConfigInDatabase('blConfirmAGB', true);
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket with invoice address
        $basketId = $this->createBasket($I, 'cart_with_null_agb');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result               = $this->placeOrder($I, $basketId, null);
        $actualErrorMessage   = (string) $result['errors'][0]['message'];
        $expectedErrorMessage = PlaceOrder::notAcceptedTOS($basketId)->getMessage();
        $I->assertEquals($expectedErrorMessage, $actualErrorMessage);

        //remove basket
        $this->removeBasket($I, $basketId);
        $I->updateConfigInDatabase('blConfirmAGB', false);
    }

    /**
     * @group agb
     */
    public function placeOrderWithConfirmAGBNotRequiredButGiven(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with not required and confirmed AGB');

        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket with invoice address
        $basketId = $this->createBasket($I, 'cart_with_agb_not_required_but_given');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result  = $this->placeOrder($I, $basketId, true);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orders['id'], $orderId);
        $I->assertEquals($orders['cost']['total'], 41.3);

        $I->updateConfigInDatabase('blConfirmAGB', false);
    }

    /**
     * @group agb
     */
    public function placeOrderWithConfirmAGBNotRequiredAndRefused(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with not required and refused AGB');

        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket with invoice address
        $basketId = $this->createBasket($I, 'cart_with_agb_not_required_but_refused');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result               = $this->placeOrder($I, $basketId, false);
        $actualErrorMessage   = (string) $result['errors'][0]['message'];
        $expectedErrorMessage = PlaceOrder::notAcceptedTOS($basketId)->getMessage();
        $I->assertEquals($expectedErrorMessage, $actualErrorMessage);

        //remove basket
        $this->removeBasket($I, $basketId);
    }

    /**
     * @group agb
     */
    public function placeOrderWithConfirmAGBNotRequiredAndNotGiven(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with not required and not given ABG');

        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket with invoice address
        $basketId = $this->createBasket($I, 'cart_with_agb_not_required_and_not_given');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result  = $this->placeOrder($I, $basketId);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orders['id'], $orderId);
        $I->assertEquals($orders['cost']['total'], 41.3);
    }

    /**
     * @group agb
     */
    public function placeOrderWithConfirmAGBNotRequiredAndNull(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order with not required and set as null ABG');

        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket with invoice address
        $basketId = $this->createBasket($I, 'cart_with_null_agb');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result  = $this->placeOrder($I, $basketId, null);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orders['id'], $orderId);
        $I->assertEquals($orders['cost']['total'], 41.3);
    }

    public function placeOrderWithDownloadableProduct(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order on downloadable product');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'cart_with_files');
        $this->addItemToBasket($I, $basketId, self::DOWNLOADABLE_FILE, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //check the basket costs
        //NOTE: usually you won't use cash on delivery with downloads but shop allows it also in standard checkout
        $basketCosts = $this->queryBasketCost($I, $basketId);
        $I->assertEquals(0, $basketCosts['productGross']['sum']);
        $I->assertEquals(7.5, $basketCosts['payment']['price']);
        $I->assertEquals(0, $basketCosts['delivery']['price']);
        $I->assertEquals(0, $basketCosts['voucher']);
        $I->assertEquals(0, $basketCosts['discount']);
        $I->assertEquals(7.5, $basketCosts['total']);

        //place the order
        $result  = $this->placeOrder($I, $basketId);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orders['id'], $orderId);
        $I->assertEquals($orders['cost']['total'], $basketCosts['total']);
    }

    public function placeOrderWithBelowMinPriceBasket(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order when basket total is below minimum price');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'cart_below_min_price');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        // change minimum price to place an order
        $I->updateConfigInDatabase('iMinOrderPrice', '100', 'str');

        //place the order
        $result = $this->placeOrder($I, $basketId);

        $I->assertSame(
            'Place order for user basket id: ' . $basketId . ' has status 8',
            $result['errors'][0]['message']
        );

        //remove basket
        $this->removeBasket($I, $basketId);
        $I->updateConfigInDatabase('iMinOrderPrice', '0', 'str');
    }

    public function placeOrderOnProductWithAmountMoreThanLimited(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order on a product which is out of stock or not buyable');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'cart_with_not_buyable_product');
        $items    = $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 5);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        // making product out of stock now
        $I->updateInDatabase('oxarticles', ['oxstock' => '3', 'oxstockflag' => '3'], ['oxid' => self::PRODUCT_ID]);

        //place the order
        $result = $this->placeOrder($I, $basketId);
        $errors = $result['errors'];

        $I->assertCount(2, $errors);

        //Check errors, first should be mutation error
        $I->assertSame('Some products are not orderable', $errors[0]['message']);

        //Error for product
        unset($errors[1]['extensions']['category']);
        $I->assertSame([
            'message'    => 'Not enough items of product with id ' . self::PRODUCT_ID . ' in stock! Available: 3',
            'extensions' => [
                'type'         => 'LIMITEDAVAILABILITY',
                'productId'    => self::PRODUCT_ID,
                'basketItemId' => $items[0]['id'],
            ],
        ], $errors[1]);

        //remove basket
        $this->removeBasket($I, $basketId);
        $I->updateInDatabase('oxarticles', ['oxstock' => '15', 'oxstockflag' => '1'], ['oxid' => self::PRODUCT_ID]);
    }

    public function placeOrderOnOutOfStockProduct(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order on a product which is out of stock');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'cart_with_not_buyable_product');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 5);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        // making product out of stock now
        $I->updateInDatabase('oxarticles', ['oxstock' => '0', 'oxstockflag' => '3'], ['oxid' => self::PRODUCT_ID]);

        //place the order
        $result = $this->placeOrder($I, $basketId);
        $errors = $result['errors'];

        $I->assertCount(2, $errors);

        //Check errors, first should be mutation error
        $I->assertSame('Some products are not orderable', $errors[0]['message']);

        //Error for product
        unset($errors[1]['extensions']['category']);
        $I->assertSame([
            'message'    => 'Product with id ' . self::PRODUCT_ID . ' is out of stock',
            'extensions' => [
                'type' => 'OUTOFSTOCK',
            ],
        ], $errors[1]);

        //remove basket
        $this->removeBasket($I, $basketId);
        $I->updateInDatabase('oxarticles', ['oxstock' => '15', 'oxstockflag' => '1'], ['oxid' => self::PRODUCT_ID]);
    }

    public function placeOrderOnNotBuyableProduct(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order on a product which is not buyable');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'cart_with_not_buyable_product');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 5);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        // making product out of stock now
        $I->updateInDatabase('oxarticles', ['oxactive' => '0'], ['oxid' => self::PRODUCT_ID]);

        //place the order
        $result = $this->placeOrder($I, $basketId);
        $errors = $result['errors'];

        $I->assertCount(2, $errors);

        //Check errors, first should be mutation error
        $I->assertSame('Some products are not orderable', $errors[0]['message']);

        //Error for product
        unset($errors[1]['extensions']['category']);
        $I->assertSame([
            'message'    => 'Product with id ' . self::PRODUCT_ID . ' is not available',
            'extensions' => [
                'type' => 'NOTAVAILABLE',
            ],
        ], $errors[1]);

        //remove basket
        $this->removeBasket($I, $basketId);
        $I->updateInDatabase('oxarticles', ['oxactive' => '1'], ['oxid' => self::PRODUCT_ID]);
    }

    public function placeOrderWithoutDeliveryMethod(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order without selected delivery method');

        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'no_delivery_method');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 1);

        //place the order
        $result = $this->placeOrder($I, $basketId);
        $I->assertSame('Delivery set must be selected!', $result['errors'][0]['message']);

        //remove basket
        $this->removeBasket($I, $basketId);
    }

    public function placeOrderWithoutPaymentMethod(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order without selected payment method');

        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'no_payment_method');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);

        //place the order
        $result = $this->placeOrder($I, $basketId);
        $I->assertSame('Payment must be selected!', $result['errors'][0]['message']);

        //remove basket
        $this->removeBasket($I, $basketId);
    }

    public function placeOrderWithNewlyRegisteredCustomer(AcceptanceTester $I): void
    {
        $I->wantToTest('register new customer, place order, check user group assignment after each step');

        //register customer
        $username     = 'newcheckoutuser@oxid-esales.com';
        $password     = self::PASSWORD;
        $customerData = $this->registerCustomer($I, $username, $password);

        $I->seeInDatabase(
            'oxobject2group',
            [
                'oxobjectid' => $customerData['id'],
                'oxgroupsid' => 'oxidnotyetordered',
            ]
        );

        $I->dontSeeInDatabase(
            'oxobject2group',
            [
                'oxobjectid' => $customerData['id'],
                'oxgroupsid' => 'oxidnewcustomer',
            ]
        );

        //log in and set invoice address
        $I->login($username, $password);
        $this->setInvoiceAddress($I);

        $I->seeInDatabase(
            'oxobject2group',
            [
                'oxobjectid' => $customerData['id'],
                'oxgroupsid' => 'oxidnotyetordered',
            ]
        );

        //only after invoice address is set, user will be assigned to oxidnewcustomer, based on his invoice country
        $I->seeInDatabase(
            'oxobject2group',
            [
                'oxobjectid' => $customerData['id'],
                'oxgroupsid' => 'oxidnewcustomer',
            ]
        );

        //prepare basket
        $basketId = $this->createBasket($I, 'not_yet_ordered');
        $this->addItemToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $this->placeOrder($I, $basketId);

        $I->seeInDatabase(
            'oxobject2group',
            [
                'oxobjectid' => $customerData['id'],
                'oxgroupsid' => 'oxidcustomer',
            ]
        );

        $I->dontSeeInDatabase(
            'oxobject2group',
            [
                'oxobjectid' => $customerData['id'],
                'oxgroupsid' => 'oxidnotyetordered',
            ]
        );

        $I->seeInDatabase(
            'oxobject2group',
            [
                'oxobjectid' => $customerData['id'],
                'oxgroupsid' => 'oxidnewcustomer',
            ]
        );
    }

    private function basketExists(AcceptanceTester $I, $basketId)
    {
        $variables = [
            'basketId' => new ID($basketId),
        ];

        $query = 'query ($basketId: ID!){
            basket (basketId: $basketId) {
                id
            }
        }';

        $result = $this->getGQLResponse($I, $query, $variables);

        if (array_key_exists('errors', $result)) {
            return false;
        }

        return true;
    }
}
