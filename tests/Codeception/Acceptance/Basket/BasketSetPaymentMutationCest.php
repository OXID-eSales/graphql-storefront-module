<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use Codeception\Scenario;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group oe_graphql_checkout
 * @group payment
 * @group basket
 * @group oe_graphql_storefront
 */
final class BasketSetPaymentMutationCest extends BaseCest
{
    private const USERNAME = 'standarduser@oxid-esales.com';

    private const OTHER_USERNAME = 'anotheruser@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const BASKET_TITLE = 'paymentsetbasket';

    private const BASKET_PAYMENT_TITLE = 'basket_payment';

    private const DELIVERY_SET_ID = 'oxidstandard';

    private const AVAILABLE_PAYMENT_ID = 'oxidinvoice';

    private const AVAILABLE_PAYMENT_CASH_ON_DELIVERY_ID = 'oxidcashondel';

    private const UNAVAILABLE_PAYMENT_ID = 'oxempty';

    private const NON_EXISTING_PAYMENT_ID = 'non-existing-payment-id';

    private const NON_EXISTING_BASKET_ID = 'non-existing-basket-id';

    private const COUNTRY_UK = 'a7c40f632a0804ab5.18804076';

    private const COUNTRY_DE = 'a7c40f631fc920687.20179984';

    private $basketId;

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $this->basketCreate($I);
    }

    public function _after(AcceptanceTester $I): void
    {
        $this->basketRemove($I);

        parent::_after($I);
    }

    public function setAvailablePaymentToBasket(AcceptanceTester $I): void
    {
        $this->basketSetDelivery($I, self::DELIVERY_SET_ID);

        $I->sendGQLQuery(
            $this->basketSetPayment(self::AVAILABLE_PAYMENT_ID)
        );

        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();
        $basket = $result['data']['basketSetPayment'];

        $I->assertSame($this->basketId, $basket['id']);
        $I->assertSame(self::AVAILABLE_PAYMENT_ID, $basket['payment']['id']);
    }

    public function setPaymentToBasketWithoutSetDeliveryMethod(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            $this->basketSetPayment(self::AVAILABLE_PAYMENT_ID)
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Delivery method must be provided!',
            $result['errors'][0]['message']
        );
    }

    public function setUnavailablePaymentToBasket(AcceptanceTester $I): void
    {
        $this->basketSetDelivery($I, self::DELIVERY_SET_ID);

        $I->sendGQLQuery(
            $this->basketSetPayment(self::UNAVAILABLE_PAYMENT_ID)
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            "Payment method '" . self::UNAVAILABLE_PAYMENT_ID . "' is unavailable!",
            $result['errors'][0]['message']
        );
    }

    public function setNonExistingPaymentToBasket(AcceptanceTester $I): void
    {
        $this->basketSetDelivery($I, self::DELIVERY_SET_ID);

        $I->sendGQLQuery(
            $this->basketSetPayment(self::NON_EXISTING_PAYMENT_ID)
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            "Payment method '" . self::NON_EXISTING_PAYMENT_ID . "' is unavailable!",
            $result['errors'][0]['message']
        );
    }

    public function setPaymentToWrongBasket(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            $this->basketSetPayment(self::AVAILABLE_PAYMENT_ID)
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'You are not allowed to access this basket as it belongs to somebody else',
            $result['errors'][0]['message']
        );

        // Login as the basket owner, because on _after the basket will be deleted
        $I->login(self::USERNAME, self::PASSWORD);
    }

    public function setDeliveryMethodToNonExistingBasket(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            $this->basketSetPayment(self::AVAILABLE_PAYMENT_ID, self::NON_EXISTING_BASKET_ID)
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Basket was not found by id: ' . self::NON_EXISTING_BASKET_ID,
            $result['errors'][0]['message']
        );
    }

    public function setPaymentToBasketWithWrongCountry(AcceptanceTester $I): void
    {
        $this->basketSetDelivery($I, self::DELIVERY_SET_ID);

        $this->setCountry(self::COUNTRY_UK);

        $I->sendGQLQuery(
            $this->basketSetPayment(self::AVAILABLE_PAYMENT_ID)
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            "Payment method 'oxidinvoice' is unavailable!",
            $result['errors'][0]['message']
        );

        $this->setCountry(self::COUNTRY_DE);
    }

    public function testPaymentCostOnChange(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            $this->basketSetPayment(self::AVAILABLE_PAYMENT_ID, self::BASKET_PAYMENT_TITLE)
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $basket = $result['data']['basketSetPayment'];

        $I->assertSame(self::BASKET_PAYMENT_TITLE, $basket['id']);
        $I->assertSame(self::AVAILABLE_PAYMENT_ID, $basket['payment']['id']);
        $I->assertSame(0, $basket['cost']['payment']['price']);
        $I->assertSame(33.8, $basket['cost']['total']);

        $I->sendGQLQuery(
            $this->basketSetPayment(self::AVAILABLE_PAYMENT_CASH_ON_DELIVERY_ID, self::BASKET_PAYMENT_TITLE)
        );
        $result = $I->grabJsonResponseAsArray();
        $basket = $result['data']['basketSetPayment'];

        $I->assertSame(self::BASKET_PAYMENT_TITLE, $basket['id']);
        $I->assertSame(self::AVAILABLE_PAYMENT_CASH_ON_DELIVERY_ID, $basket['payment']['id']);
        $I->assertSame(7.5, $basket['cost']['payment']['price']);
        $I->assertSame(41.3, $basket['cost']['total']);
    }

    private function basketCreate(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            '
            mutation {
                basketCreate(basket: {title: "' . self::BASKET_TITLE . '"}) {
                    id
                }
            }
        '
        );

        $result = $I->grabJsonResponseAsArray();

        $this->basketId = $result['data']['basketCreate']['id'];
    }

    private function basketSetDelivery(AcceptanceTester $I, string $deliveryMethodId): void
    {
        $I->sendGQLQuery(
            'mutation {
                basketSetDeliveryMethod(basketId: "' . $this->basketId . '", deliveryMethodId: "' . $deliveryMethodId . '") {
                    id
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            $this->basketId,
            $result['data']['basketSetDeliveryMethod']['id']
        );
    }

    private function basketRemove(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            '
            mutation {
                basketRemove (basketId: "' . $this->basketId . '")
            }
        '
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertTrue(
            $result['data']['basketRemove']
        );
    }

    private function basketSetPayment(string $paymentId, ?string $basketId = null): string
    {
        $basketId = $basketId ?: $this->basketId;

        return 'mutation {
            basketSetPayment(basketId: "' . $basketId . '", paymentId: "' . $paymentId . '") {
                id
                payment {
                    id
                }
                cost {
                    payment {
                        price
                    }
                    total
                }
            }
        }';
    }

    private function setCountry(string $countryId): void
    {
        $queryBuilder = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class)
            ->create();

        $queryBuilder->update('oxuser')
            ->set('oxcountryid', ':countryId')
            ->where('oxusername = :username')
            ->setParameter(':countryId', $countryId)
            ->setParameter(':username', self::USERNAME)
            ->execute();
    }
}
