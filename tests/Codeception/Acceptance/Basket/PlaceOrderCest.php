<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use Codeception\Util\HttpCode;
use GraphQL\Validator\Rules\FieldsOnCorrectType;
use OxidEsales\GraphQL\Storefront\Basket\Exception\PlaceOrder;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\Exception\UnavailableDeliveryMethod;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group oe_graphql_checkout
 * @group place_order
 * @group basket
 */
final class PlaceOrderCest extends PlaceOrderBaseCest
{
    /**
     * @group allowed_to_fail_for_anonymous_token
     */
    public function placeOrderWithAnonymousUser(AcceptanceTester $I): void
    {
        $I->wantToTest('anonymous user is placing an order');
        $I->login(self::USERNAME, self::PASSWORD, 0);

        //prepare basket
        $basketId = $this->createBasket($I, 'my_anonymous_cart');
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 2);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $I->logout();
        $I->login(null, null, 0);
        $result  = $this->placeOrder($I, $basketId, HttpCode::BAD_REQUEST);

        $expectedMessage = FieldsOnCorrectType::undefinedFieldMessage('placeOrder', 'Mutation', [], []);
        $I->assertEquals($expectedMessage, $result['errors'][0]['message']);

        $this->removeBasket($I, $basketId, self::USERNAME);
    }

    public function placeOrderUsingInvoiceAddress(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order successfully with invoice address only');
        $I->login(self::USERNAME, self::PASSWORD, 0);

        //prepare basket
        $basketId = $this->createBasket($I, 'my_cart_one');
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 2);
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

        $this->ensureBasketDoesNotExist($I, $basketId, self::USERNAME);
    }

    public function placeOrderUsingInvoiceAddressAndDefaultSavedBasket(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order for savedbasket and blPerfNoBasketSaving');

        $I->updateConfigInDatabase('blPerfNoBasketSaving', true, 'bool');
        $I->login(self::CHECKOUT_USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, self::DEFAULT_SAVEDBASKET);
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 2);
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

        $this->ensureBasketDoesNotExist($I, $basketId, self::CHECKOUT_USERNAME);
    }

    public function placeOrderRemovesCorrectBasket(AcceptanceTester $I): void
    {
        $I->wantToTest('savedbasket(default basket) should not be removed on other basket order');

        $I->login(self::CHECKOUT_USERNAME, self::PASSWORD);

        //prepare default basket
        $defaultBasketId = $this->createBasket($I, self::DEFAULT_SAVEDBASKET);
        $this->addProductToBasket($I, $defaultBasketId, self::PRODUCT_ID, 2);

        //prepare ordering basket
        $basketId = $this->createBasket($I, 'specialBasket');
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 2);
        $this->setBasketDeliveryMethod($I, $basketId, self::TEST_SHIPPING);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_TEST);

        $this->ensureBasketExist($I, $basketId, self::CHECKOUT_USERNAME);
        $this->ensureBasketExist($I, $defaultBasketId, self::CHECKOUT_USERNAME);

        //place the order
        $this->placeOrder($I, $basketId);

        //both baskets removed
        $this->ensureBasketDoesNotExist($I, $basketId, self::CHECKOUT_USERNAME);
        $this->ensureBasketExist($I, $defaultBasketId, self::CHECKOUT_USERNAME);
    }

    public function placeOrderUsingDeliveryAddress(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order successfully with delivery address');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'my_cart_two');
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 2);
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

        //remove basket
        $this->ensureBasketDoesNotExist($I, $basketId, self::USERNAME);
    }

    public function placeOrderWithoutToken(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order when logged out');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'my_cart_three');
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 2);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //log out
        $I->logout();

        //place the order
        $this->placeOrder($I, $basketId, HttpCode::BAD_REQUEST);

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
    }

    public function placeOtherUsersOrder(AcceptanceTester $I): void
    {
        $I->wantToTest('placing another users order');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'my_cart_four');
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 2);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //log out
        $I->logout();

        //log in different user and place the order
        $I->login(self::OTHER_USERNAME, self::PASSWORD);
        $this->placeOrder($I, $basketId, HttpCode::UNAUTHORIZED);

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
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
        $this->placeOrder($I, $basketId, HttpCode::BAD_REQUEST);

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
    }

    public function prepareOrderWithNoShippingMethodForCountry(AcceptanceTester $I): void
    {
        $I->wantToTest('that using delivery address with unsupported country fails');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket with invoice address
        $basketId = $this->createBasket($I, 'my_cart_five');
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 3);
        $this->setBasketDeliveryAddress($I, $basketId, self::ALTERNATE_COUNTRY);

        //shipping method not supported
        $errorMessage         = $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD, HttpCode::BAD_REQUEST);
        $expectedError        = UnavailableDeliveryMethod::byId(self::SHIPPING_STANDARD);
        $expectedErrorMessage = $expectedError->getMessage();
        $I->assertEquals($expectedErrorMessage, $errorMessage);

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
    }

    public function placeOrderWithChangedDeliveryAddress(AcceptanceTester $I): void
    {
        $I->wantToTest('that placing an order with changed delivery address fails');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket with german delivery address
        $basketId = $this->createBasket($I, 'my_cart_six');
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 3);
        $this->setBasketDeliveryAddress($I, $basketId); //Germany
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //this country is not supported for chosen shipping method
        $this->setBasketDeliveryAddress($I, $basketId, self::ALTERNATE_COUNTRY);

        //place the order
        $this->placeOrder($I, $basketId, HttpCode::BAD_REQUEST);

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
    }

    public function placeOrderWithUnavailablePayment(AcceptanceTester $I): void
    {
        $I->wantToTest('that placing an order with unavailable payment fails');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket with invoice address
        $basketId = $this->createBasket($I, 'my_cart_seven');
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 3);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        $I->updateInDatabase('oxuserbaskets', ['oegql_paymentid' => self::PAYMENT_TEST], ['oxid' => $basketId]);

        //place the order
        $this->placeOrder($I, $basketId, HttpCode::BAD_REQUEST);

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
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
        $this->addProductToBasket($I, $basketId, self::DISCOUNT_PRODUCT, 1);
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

        $this->ensureBasketDoesNotExist($I, $basketId, self::USERNAME);

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
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 2);
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

        $this->ensureBasketDoesNotExist($I, $basketId, self::USERNAME);
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
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result  = $this->placeOrder($I, $basketId, HttpCode::OK, true);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orders['id'], $orderId);
        $I->assertEquals($orders['cost']['total'], 41.3);

        $this->ensureBasketDoesNotExist($I, $basketId, self::USERNAME);
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
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result               = $this->placeOrder($I, $basketId, HttpCode::BAD_REQUEST);
        $actualErrorMessage   = (string) $result['errors'][0]['message'];
        $expectedErrorMessage = PlaceOrder::notAcceptedTOS($basketId)->getMessage();
        $I->assertEquals($expectedErrorMessage, $actualErrorMessage);

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
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
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result               = $this->placeOrder($I, $basketId, HttpCode::BAD_REQUEST, false);
        $actualErrorMessage   = (string) $result['errors'][0]['message'];
        $expectedErrorMessage = PlaceOrder::notAcceptedTOS($basketId)->getMessage();
        $I->assertEquals($expectedErrorMessage, $actualErrorMessage);

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
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
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result               = $this->placeOrder($I, $basketId, HttpCode::BAD_REQUEST, null);
        $actualErrorMessage   = (string) $result['errors'][0]['message'];
        $expectedErrorMessage = PlaceOrder::notAcceptedTOS($basketId)->getMessage();
        $I->assertEquals($expectedErrorMessage, $actualErrorMessage);

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
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
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result  = $this->placeOrder($I, $basketId, HttpCode::OK, true);
        $orderId = $result['data']['placeOrder']['id'];

        //check order history
        $orders = $this->getOrderFromOrderHistory($I);
        $I->assertEquals($orders['id'], $orderId);
        $I->assertEquals($orders['cost']['total'], 41.3);

        $this->ensureBasketDoesNotExist($I, $basketId, self::USERNAME);
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
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result               = $this->placeOrder($I, $basketId, HttpCode::BAD_REQUEST, false);
        $actualErrorMessage   = (string) $result['errors'][0]['message'];
        $expectedErrorMessage = PlaceOrder::notAcceptedTOS($basketId)->getMessage();
        $I->assertEquals($expectedErrorMessage, $actualErrorMessage);

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
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
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result  = $this->placeOrder($I, $basketId, HttpCode::OK);
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
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $result  = $this->placeOrder($I, $basketId, HttpCode::OK, null);
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
        $this->addProductToBasket($I, $basketId, self::DOWNLOADABLE_FILE, 1);
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
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        // change minimum price to place an order
        $I->updateConfigInDatabase('iMinOrderPrice', '100', 'str');

        //place the order
        $this->placeOrder($I, $basketId, HttpCode::BAD_REQUEST);

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
        $I->updateConfigInDatabase('iMinOrderPrice', '0', 'str');
    }

    public function placeOrderOnOutOfStockNotBuyableProduct(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order on a product which is out of stock or not buyable');
        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'cart_with_not_buyable_product');
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 5);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        // making product out of stock now
        $I->updateInDatabase('oxarticles', ['oxstock' => '3', 'oxstockflag' => '3'], ['oxid' => self::PRODUCT_ID]);

        //place the order
        $this->placeOrder($I, $basketId, HttpCode::BAD_REQUEST);

        //remove basket
        $this->removeBasket($I, $basketId, self::USERNAME);
        $I->updateInDatabase('oxarticles', ['oxstock' => '15', 'oxstockflag' => '1'], ['oxid' => self::PRODUCT_ID]);
    }

    public function placeOrderWithoutDeliveryMethod(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order without selected delivery method');

        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'no_delivery_method');
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 1);

        //place the order
        $this->placeOrder($I, $basketId, HttpCode::BAD_REQUEST);
    }

    public function placeOrderWithoutPaymentMethod(AcceptanceTester $I): void
    {
        $I->wantToTest('placing an order without selected payment method');

        $I->login(self::USERNAME, self::PASSWORD);

        //prepare basket
        $basketId = $this->createBasket($I, 'no_payment_method');
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);

        //place the order
        $this->placeOrder($I, $basketId, HttpCode::BAD_REQUEST);
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
        $this->addProductToBasket($I, $basketId, self::PRODUCT_ID, 1);
        $this->setBasketDeliveryMethod($I, $basketId, self::SHIPPING_STANDARD);
        $this->setBasketPaymentMethod($I, $basketId, self::PAYMENT_STANDARD);

        //place the order
        $this->placeOrder($I, $basketId, HttpCode::OK);

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
}
