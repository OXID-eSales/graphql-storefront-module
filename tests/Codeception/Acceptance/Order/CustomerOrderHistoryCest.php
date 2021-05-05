<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Order;

use Codeception\Example;
use Codeception\Scenario;
use OxidEsales\Eshop\Core\Registry as EshopRegistry;
use OxidEsales\Facts\Facts;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group order
 * @group oe_graphql_storefront
 */
final class CustomerOrderHistoryCest extends BaseCest
{
    private const EXAMPLE_USERNAME = 'exampleuser@oxid-esales.com';

    private const DIFFERENT_USERNAME = 'differentuser@oxid-esales.com';

    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const ORDER_WITH_ALL_DATA = '8c726d3f42ff1a6ea2828d5f309de881';

    private const PARCEL_SERVICE_LINK = 'http://myshinyparcel.com?ID=';

    private $originalParcelService = '';

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $this->originalParcel = EshopRegistry::getConfig()->getConfigParam('sParcelService');
        $I->updateConfigInDatabase('sParcelService', self::PARCEL_SERVICE_LINK, 'str');
        $I->login(self::USERNAME, self::PASSWORD);
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('sParcelService', $this->originalParcelService, 'str');
    }

    public function testCustomerOrderWithAllData(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            ' query {
                customer {
                    id
                    orders(
                        pagination: {limit: 1, offset: 0}
                    ){
                        id
                        orderNumber
                        invoiceNumber
                        invoiced
                        remark
                        cancelled
                        ordered
                        paid
                        updated
                        vouchers {
                            id
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $I->assertEquals(1, count($result['data']['customer']['orders']));

        $order = $result['data']['customer']['orders'][0];
        $I->assertSame(self::ORDER_WITH_ALL_DATA, $order['id']);
        $I->assertSame(4, $order['orderNumber']);
        $I->assertSame(665, $order['invoiceNumber']);
        $I->assertSame('2020-08-24T00:00:00+02:00', $order['invoiced']);
        $I->assertSame('please deliver as fast as you can', $order['remark']);
        $I->assertFalse($order['cancelled']);
        $I->assertSame('2020-05-23T14:08:55+02:00', $order['ordered']);
        $I->assertNull($order['paid']);
        $I->assertNotEmpty($order['updated']);
        $I->assertEmpty($order['vouchers']);
    }

    public function testCustomerOrderAddresses(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            ' query {
                customer {
                    id
                    orders(
                        pagination: {limit: 1, offset: 0}
                    ){
                        invoiceAddress {
                            salutation
                            email
                            firstName
                            lastName
                            company
                            additionalInfo
                            street
                            streetNumber
                            zipCode
                            city
                            vatID
                            phone
                            fax
                            country {
                                id
                            }
                            state {
                                id
                            }
                        }
                        deliveryAddress {
                            salutation
                            firstName
                            lastName
                            company
                            additionalInfo
                            street
                            streetNumber
                            zipCode
                            city
                            phone
                            fax
                            country {
                                id
                            }
                            state {
                                id
                            }
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $order  = $result['data']['customer']['orders'][0];

        $this->assertInvoiceAddress($I, $order['invoiceAddress']);
        $this->assertDeliveryAddress($I, $order['deliveryAddress']);
    }

    public function testCustomerOrderWithoutDeliveryAddress(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                customer {
                    orders(
                      pagination: {limit: 1, offset: 3}
                    )
                    {
                        id
                        orderNumber
                        deliveryAddress {
                            firstName
                            lastName
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $I->assertEquals(1, count($result['data']['customer']['orders']));

        $I->assertSame(1, $result['data']['customer']['orders'][0]['orderNumber']);
        $I->assertNull($result['data']['customer']['orders'][0]['deliveryAddress']);
    }

    public function testCustomerOrders(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                customer {
                    orders {
                        id
                        orderNumber
                        deliveryAddress {
                            firstName
                            lastName
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(4, count($result['data']['customer']['orders']));
    }

    public function testOrderVouchers(AcceptanceTester $I): void
    {
        $I->login(self::DIFFERENT_USERNAME, self::PASSWORD);

        $I->sendGQLQuery('query {
            customer {
                id
                orders(
                    pagination: {limit: 1, offset: 0}
                ){
                    id
                    vouchers {
                        id
                        number
                        discount
                        redeemedAt
                    }
                }
            }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $vouchers = $result['data']['customer']['orders'][0]['vouchers'];
        $I->assertEquals(1, count($vouchers));

        $voucher = $vouchers[0];
        $I->assertSame('usedvoucherid', $voucher['id']);
        $I->assertSame('voucher1', $voucher['number']);
        $I->assertSame(21.6, $voucher['discount']);
        $I->assertStringStartsWith('2020-08-28', $voucher['redeemedAt']);
    }

    public function testOrderCost(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                customer {
                    id
                    orders(
                        pagination: {limit: 1, offset: 0}
                    ){
                        cost {
                            total
                            discount
                            voucher
                            productNet {
                                price
                                vat
                            }
                            productGross {
                                sum
                                vats {
                                    vatRate
                                    vatPrice
                                }
                            }
                            delivery {
                                price
                                vat
                                currency {
                                    name
                                }
                            }
                            payment {
                                price
                                vat
                                currency {
                                    name
                               }
                            }
                            currency {
                                name
                            }
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(1, count($result['data']['customer']['orders']));
        $this->assertCost($I, $result['data']['customer']['orders'][0]['cost']);
    }

    public function testCustomerOrdersWithoutPagination(AcceptanceTester $I): void
    {
        $I->login(self::DIFFERENT_USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query
                {
                customer {
                    orders
                    {
                        id
                        orderNumber
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(14, count($result['data']['customer']['orders']));
        $I->assertEquals('113', $result['data']['customer']['orders'][0]['orderNumber']);
        $I->assertEquals('100', array_pop($result['data']['customer']['orders'])['orderNumber']);
    }

    /**
     * @dataProvider providerCustomerOrdersPagination
     */
    public function testCustomerOrdersPagination(
        AcceptanceTester $I,
        Example $data
    ): void {
        $pagination   = $data['pagination'];
        $expected     = $data['expected'];
        $firstOrderNr = $data['first_ordernr'];
        $lastOrderNr  = $data['last_ordernr'];

        $I->login(self::DIFFERENT_USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query ($limit: Int!, $offset: Int!)
                {
                customer {
                    orders (pagination: {limit: $limit, offset: $offset})
                    {
                        id
                        orderNumber
                    }
                }
            }',
            $pagination
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals($expected, count($result['data']['customer']['orders']));

        if (!empty($firstOrderNr)) {
            $I->assertEquals($firstOrderNr, $result['data']['customer']['orders'][0]['orderNumber']);
            $I->assertEquals($lastOrderNr, array_pop($result['data']['customer']['orders'])['orderNumber']);
        }
    }

    public function testShippedOrderDelivery(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                customer {
                    id
                    orders(
                        pagination: {limit: 1, offset: 0}
                    ){
                        delivery {
                            trackingNumber
                            trackingURL
                            dispatched
                            provider {
                                id
                                active
                                title
                            }
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(1, count($result['data']['customer']['orders']));
        $this->assertDelivery($I, $result['data']['customer']['orders'][0]['delivery']);
    }

    public function testOrderWithNotExistingDelivery(AcceptanceTester $I): void
    {
        $I->login(self::EXAMPLE_USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                customer {
                    id
                    orders(
                        pagination: {limit: 1, offset: 0}
                    ){
                        delivery {
                            trackingNumber
                            trackingURL
                            dispatched
                            provider {
                                id
                                active
                                title
                            }
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(1, count($result['data']['customer']['orders']));

        $delivery = $result['data']['customer']['orders'][0]['delivery'];

        $I->assertNull($delivery['dispatched']);
        $I->assertEquals(false, $delivery['provider']['active']);
        $I->assertEmpty($delivery['provider']['id']);
        $I->assertEmpty($delivery['provider']['title']);
        $I->assertEmpty($delivery['trackingNumber']);
        $I->assertEmpty($delivery['trackingURL']);
    }

    protected function providerCustomerOrdersPagination()
    {
        return [
            'set1' => [
                'pagination'    => [
                    'limit'  => 1,
                    'offset' => 0,
                ],
                'expected'      => 1,
                'first_ordernr' => '113',
                'last_ordernr'  => '113',
            ],
            'set2' => [
                'pagination'    => [
                    'limit'  => 10,
                    'offset' => 0,
                ],
                'expected'      => 10,
                'first_ordernr' => '113',
                'last_ordernr'  => '104',
            ],
            'set3' => [
                'pagination'    => [
                    'limit'  => 1,
                    'offset' => 10,
                ],
                'expected'      => 1,
                'first_ordernr' => '103',
                'last_ordernr'  => '103',
            ],
            'set4' => [
                'pagination'    => [
                    'limit'  => 1,
                    'offset' => 100,
                ],
                'expected'      => 0,
                'first_ordernr' => '',
                'last_ordernr'  => '',
            ],
        ];
    }

    private function assertInvoiceAddress(AcceptanceTester $I, array $address): void
    {
        $expected = [
            'email'          => 'billuser@oxid-esales.com',
            'salutation'     => 'MR',
            'firstName'      => 'Marc',
            'lastName'       => 'Muster',
            'company'        => 'bill company',
            'additionalInfo' => 'additional bill info',
            'street'         => 'Hauptstr.',
            'streetNumber'   => '13',
            'zipCode'        => '79098',
            'city'           => 'Freiburg',
            'vatID'          => 'bill vat id',
            'phone'          => '1234',
            'fax'            => '4567',
            'country'        => ['id' => 'a7c40f631fc920687.20179984'],
            'state'          => null,
        ];

        if (Facts::getEdition() !== 'EE') {
            $expected['vatID'] = '';
        }

        foreach ($expected as $key => $value) {
            $I->assertSame($value, $address[$key], $key);
        }
    }

    private function assertDeliveryAddress(AcceptanceTester $I, array $address): void
    {
        $expected = [
            'salutation'     => 'MRS',
            'firstName'      => 'Marcia',
            'lastName'       => 'Pattern',
            'company'        => 'del company',
            'additionalInfo' => 'del addinfo',
            'street'         => 'Nebenstraße',
            'streetNumber'   => '123',
            'zipCode'        => '79106',
            'city'           => 'Freiburg',
            'phone'          => '04012345678',
            'fax'            => '04012345679',
            'country'        => ['id' => 'a7c40f631fc920687.20179984'],
            'state'          => null,
        ];

        foreach ($expected as $key => $value) {
            $I->assertSame($value, $address[$key], $key);
        }
    }

    private function assertCost(AcceptanceTester $I, array $costs): void
    {
        $expected = [
            'total'        => 220.78,
            'discount'     => 123.4,
            'voucher'      => 0.0,
            'productNet'   => [
                'price' => 178.3,
                'vat'   => 0.0,
            ],
            'productGross' => [
                'sum'  => 209.38,
                'vats' => [
                    [
                        'vatRate'  => 10.0,
                        'vatPrice' => 2.72,
                    ],
                    [
                        'vatRate'  => 19.0,
                        'vatPrice' => 27.38,
                    ],
                ],
            ],
            'delivery'     => [
                'price'    => 3.9,
                'vat'      => 19.0,
                'currency' => [
                    'name' => 'EUR',
                ],
            ],
            'payment'      => [
                'price'    => 7.5,
                'vat'      => 19.0,
                'currency' => [
                    'name' => 'EUR',
                ],
            ],
            'currency'     => [
                'name' => 'EUR',
            ],
        ];

        $I->assertEquals($expected, $costs);
    }

    private function assertDelivery(AcceptanceTester $I, array $delivery): void
    {
        $expected = [
            'trackingNumber' => 'tracking_code',
            'trackingURL'    => self::PARCEL_SERVICE_LINK,
            'dispatched'     => '2020-09-02T12:12:12+02:00',
            'provider'       => [
                'id'     => 'oxidstandard',
                'active' => true,
                'title'  => 'Standard',
            ],
        ];

        $I->assertEquals($expected, $delivery);
    }
}
