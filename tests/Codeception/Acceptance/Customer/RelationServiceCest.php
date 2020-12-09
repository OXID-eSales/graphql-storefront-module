<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Customer;

use Codeception\Util\HttpCode;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group customer
 */
final class RelationServiceCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const CUSTOMER_ID = 'e7af1c3b786fd02906ccd75698f4e6b9';

    private const EXISTING_USERNAME = 'existinguser@oxid-esales.com';

    private const BASKET_WISH_LIST = 'wishlist';

    private const BASKET_NOTICE_LIST = 'noticelist';

    private const BASKET_SAVED_BASKET = 'savedbasket';

    private const PRODUCT_ID = 'dc5ffdf380e15674b56dd562a7cb6aec';

    public function testGetInvoiceAddressRelation(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->queryInvoiceAddressRelation($I);

        $customer = $result['data']['customer'];
        $I->assertSame(self::CUSTOMER_ID, $customer['id']);
        $I->assertSame(self::USERNAME, $customer['email']);
        $I->assertSame('2', $customer['customerNumber']);
        $I->assertSame('Marc', $customer['firstName']);
        $I->assertSame('Muster', $customer['lastName']);

        $invoiceAddress = $customer['invoiceAddress'];
        $I->assertNotEmpty($invoiceAddress);
        $I->assertSame('MR', $invoiceAddress['salutation']);
        $I->assertSame('Marc', $invoiceAddress['firstName']);
        $I->assertSame('Muster', $invoiceAddress['lastName']);
    }

    public function testGetDeliveryAddressesRelation(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->queryDeliveryAddressesRelation($I);

        $customer = $result['data']['customer'];
        $I->assertSame(self::CUSTOMER_ID, $customer['id']);
        $I->assertSame(self::USERNAME, $customer['email']);
        $I->assertSame('2', $customer['customerNumber']);
        $I->assertSame('Marc', $customer['firstName']);
        $I->assertSame('Muster', $customer['lastName']);

        $deliveryAddresses = $customer['deliveryAddresses'];
        $I->assertNotEmpty($deliveryAddresses);
        $I->assertEquals(2, count($deliveryAddresses));
        [$deliveryAddress1, $deliveryAddress2] = $deliveryAddresses;
        $I->assertSame('MR', $deliveryAddress1['salutation']);
        $I->assertSame('Marc', $deliveryAddress1['firstName']);
        $I->assertSame('Muster', $deliveryAddress1['lastName']);
        $I->assertSame('MR', $deliveryAddress2['salutation']);
        $I->assertSame('Marc', $deliveryAddress2['firstName']);
        $I->assertSame('Muster', $deliveryAddress2['lastName']);
    }

    public function testGetEmptyBasketRelation(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->queryBasketRelation($I, self::BASKET_WISH_LIST);

        $customer = $result['data']['customer'];
        $I->assertSame(self::USERNAME, $customer['email']);

        $basket = $customer['basket'];
        $I->assertTrue($basket['public']);
        $I->assertEmpty($basket['items']);
    }

    public function testGetBasketRelation(AcceptanceTester $I): void
    {
        $I->login(self::EXISTING_USERNAME, self::PASSWORD);

        $basketId = $this->basketCreate($I, self::BASKET_WISH_LIST);
        $this->basketAddProductMutation($I, $basketId, self::PRODUCT_ID, 1);

        $result = $this->queryBasketRelation($I, self::BASKET_WISH_LIST);

        $customer = $result['data']['customer'];
        $I->assertSame(self::EXISTING_USERNAME, $customer['email']);

        $basket = $customer['basket'];
        $I->assertFalse($basket['public']);
        $I->assertNotEmpty($basket['items']);
        $I->assertSame('Kuyichi leather belt JEVER', $basket['items'][0]['product']['title']);

        $this->basketRemoveMutation($I, $basketId);
    }

    public function testGetBasketsRelation(AcceptanceTester $I): void
    {
        $I->login(self::EXISTING_USERNAME, self::PASSWORD);

        $noticeId = $this->basketCreate($I, self::BASKET_NOTICE_LIST);
        $wishId   = $this->basketCreate($I, self::BASKET_WISH_LIST);
        $savedId  = $this->basketCreate($I, self::BASKET_SAVED_BASKET);

        $this->basketAddProductMutation($I, $wishId, self::PRODUCT_ID, 1);
        $this->basketAddProductMutation($I, $noticeId, self::PRODUCT_ID, 1);
        $this->basketAddProductMutation($I, $savedId, self::PRODUCT_ID, 1);

        $result = $this->queryBasketsRelation($I);

        $customer = $result['data']['customer'];
        $I->assertSame(self::EXISTING_USERNAME, $customer['email']);

        $baskets = $customer['baskets'];
        $I->assertEquals(3, count($baskets));

        $this->basketRemoveMutation($I, $noticeId);
        $this->basketRemoveMutation($I, $wishId);
        $this->basketRemoveMutation($I, $savedId);

        $result = $this->queryBasketsRelation($I);

        $baskets = $result['data']['customer']['baskets'];
        $I->assertEquals(0, count($baskets));
    }

    private function queryInvoiceAddressRelation(AcceptanceTester $I): array
    {
        $I->sendGQLQuery('query {
            customer {
                id
                email
                customerNumber
                firstName
                lastName
                invoiceAddress {
                    salutation
                    firstName
                    lastName
                }
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function queryDeliveryAddressesRelation(AcceptanceTester $I): array
    {
        $I->sendGQLQuery('query {
            customer {
                id
                email
                customerNumber
                firstName
                lastName
                deliveryAddresses {
                    salutation
                    firstName
                    lastName
                }
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function queryBasketRelation(AcceptanceTester $I, string $title): array
    {
        $I->sendGQLQuery(
            'query {
                customer {
                    email
                    basket(title: "' . $title . '") {
                        id
                        public
                        items(pagination: {limit: 10, offset: 0}) {
                            product {
                                title
                            }
                        }
                    }
                }
            }',
            null,
            1
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function queryBasketsRelation(AcceptanceTester $I): array
    {
        $I->sendGQLQuery('query {
            customer {
                email
                baskets {
                    public
                    items(pagination: {limit: 10, offset: 0}) {
                        product {
                            title
                        }
                    }
                }
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function basketAddProductMutation(AcceptanceTester $I, string $basketId, string $productId, int $amount = 1): array
    {
        $I->sendGQLQuery('
            mutation {
                basketAddProduct(
                    basketId: "' . $basketId . '",
                    productId: "' . $productId . '",
                    amount: ' . $amount . '
                ) {
                    id
                    items {
                        product {
                            id
                        }
                        amount
                    }
                    lastUpdateDate
                }
            }
        ');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function basketRemoveProductMutation(AcceptanceTester $I, string $basketTitle, string $productId, int $amount = 0): array
    {
        $result = $this->queryBasketRelation($I, $basketTitle);

        $I->sendGQLQuery(
            'mutation {
                basketRemoveProduct(
                    basketId: "' . $result['customer']['basket']['id'] . '",
                    productId: "' . $productId . '",
                    amount: ' . $amount . '
                ) {
                    items(pagination: {limit: 10, offset: 0}) {
                        product {
                            id
                            title
                        }
                        amount
                    }
                    id
                }
            }'
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function basketCreate(AcceptanceTester $I, string $basketName): string
    {
        $I->sendGQLQuery(
            'mutation {
                basketCreate(basket: {
                    title: "' . $basketName . '",
                    public: false
                }) {
                    id
                }
            }'
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        return $result['data']['basketCreate']['id'];
    }

    private function basketRemoveMutation(AcceptanceTester $I, string $basketId): array
    {
        $I->sendGQLQuery(
            'mutation {
                basketRemove(id: "' . $basketId . '")
            }'
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
