<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Order;

use Codeception\Util\HttpCode;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group order
 */
final class CustomerOrderPaymentCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const ORDER_NUMBER = 4;

    private const PAYMENT_ID = 'oxiddebitnote';

    public function testCustomerOrderPayment(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                customer {
                    orders {
                        orderNumber
                        payment {
                            id
                            payment {
                                id
                            }
                            values {
                                key
                            }
                            updated
                        }
                    }
                }
            }'
        );
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $orders = $result['data']['customer']['orders'];

        foreach ($orders as $order) {
            if ($order['orderNumber'] != self::ORDER_NUMBER) {
                continue;
            }

            $orderPayment = $order['payment'];
            $I->assertNotEmpty($orderPayment);
            $I->assertSame('direct_debit_order_payment', $orderPayment['id']);
            $I->assertNotEmpty($orderPayment['payment']);
            $I->assertNotEmpty($orderPayment['values']);

            // Updated field is not included in the sql query,
            // that's why it's value will be null despite the fact that it has a value.
            $I->assertNull($orderPayment['updated']);
        }
    }

    public function testCustomerPaymentUsedDuringOrder(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                customer {
                    orders {
                        orderNumber
                        payment {
                            payment {
                                id
                                active
                                title
                                description
                                updated
                            }
                        }
                    }
                }
            }',
            [],
            1
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $orders = $result['data']['customer']['orders'];

        foreach ($orders as $order) {
            if ($order['orderNumber'] != self::ORDER_NUMBER) {
                continue;
            }

            $payment = $order['payment']['payment'];
            $I->assertSame(self::PAYMENT_ID, $payment['id']);
            $I->assertSame(true, $payment['active']);
            $I->assertSame('Direct Debit', $payment['title']);
            $I->assertSame('Your bank account will be charged when the order is shipped.', $payment['description']);
            $I->assertNotEmpty($payment['updated']);
        }
    }

    public function testCustomerPaymentUsedDuringOrderMultiLanguage(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                customer {
                    orders {
                        orderNumber
                        payment {
                            payment {
                                id
                                active
                                title
                                description
                                updated
                            }
                        }
                    }
                }
            }'
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $orders = $result['data']['customer']['orders'];

        foreach ($orders as $order) {
            if ($order['orderNumber'] != self::ORDER_NUMBER) {
                continue;
            }

            $payment = $order['payment']['payment'];
            $I->assertSame('Bankeinzug/Lastschrift', $payment['title']);
            $I->assertSame('Die Belastung Ihres Kontos erfolgt mit dem Versand der Ware.', $payment['description']);
        }
    }

    public function testCustomerOrderPaymentValues(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                customer {
                    orders {
                        orderNumber
                        payment {
                            values {
                                key
                                value
                            }
                        }
                    }
                }
            }'
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $orders = $result['data']['customer']['orders'];

        foreach ($orders as $order) {
            if ($order['orderNumber'] != self::ORDER_NUMBER) {
                continue;
            }

            [$bank, $bic, $iban, $owner] = $order['payment']['values'];
            $I->assertSame('lsbankname', $bank['key']);
            $I->assertSame('Pro Credit Bank', $bank['value']);
            $I->assertSame('lsblz', $bic['key']);
            $I->assertSame('PRCBBGSF456', $bic['value']);
            $I->assertSame('lsktonr', $iban['key']);
            $I->assertSame('DE89 3704 0044 0532 0130 00', $iban['value']);
            $I->assertSame('lsktoinhaber', $owner['key']);
            $I->assertSame('Marc Muster', $owner['value']);
        }
    }

    public function testCustomerOrderPaymentWithInactivePayment(AcceptanceTester $I): void
    {
        $this->updatePaymentActiveStatus($I, false);

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                customer {
                    orders {
                        orderNumber
                        payment {
                            payment {
                                id
                            }
                            values {
                                key
                            }
                        }
                    }
                }
            }'
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $orders = $result['data']['customer']['orders'];

        foreach ($orders as $order) {
            if ($order['orderNumber'] != self::ORDER_NUMBER) {
                continue;
            }

            $I->assertNull($order['payment']['payment']);
            $I->assertNotNull($order['payment']['values']);
        }

        $this->updatePaymentActiveStatus($I, true);
    }

    public function testCustomerOrderPaymentWithNonExistingPayment(AcceptanceTester $I): void
    {
        $this->updatePaymentId($I, 'some-new-payment-id', self::PAYMENT_ID);

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                customer {
                    orders {
                        orderNumber
                        payment {
                            payment {
                                id
                            }
                            values {
                                key
                            }
                        }
                    }
                }
            }'
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $orders = $result['data']['customer']['orders'];

        foreach ($orders as $order) {
            if ($order['orderNumber'] != self::ORDER_NUMBER) {
                continue;
            }

            $I->assertNull($order['payment']['payment']);
            $I->assertNotNull($order['payment']['values']);
        }

        $this->updatePaymentId($I, self::PAYMENT_ID, 'some-new-payment-id');
    }

    private function updatePaymentActiveStatus(AcceptanceTester $I, bool $active): void
    {
        $I->updateInDatabase(
            'oxpayments',
            ['oxactive' => (int) $active],
            ['oxid'     => self::PAYMENT_ID]
        );
    }

    private function updatePaymentId(AcceptanceTester $I, string $fakePaymentId, $realPaymentId): void
    {
        $I->updateInDatabase(
            'oxpayments',
            ['oxid' => $fakePaymentId],
            ['oxid' => $realPaymentId]
        );
    }
}
