<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use Codeception\Example;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group oe_graphql_checkout
 * @group address
 * @group basket
 * @group oe_graphql_storefront
 */
final class DeliveryAddressCest extends BaseCest
{
    private const USERNAME = 'standarduser@oxid-esales.com';

    private const OTHER_USERNAME = 'anotheruser@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const BASKET_TITLE = 'deliveryaddressbasket';

    private const DELIVERY_ADDRESS_ID = 'address_user';

    private const WRONG_DELIVERY_ADDRESS_ID = 'address_otheruser';

    private const BASKET_WITH_ADDRESS_ID = 'basket_user_address_payment';

    private const BASKET_WITHOUT_ADDRESS_ID = 'basket_user_3';

    public function setDeliveryAddressToBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreate($I);

        $I->sendGQLQuery(
            $this->basketSetDeliveryAddress($basketId, self::DELIVERY_ADDRESS_ID)
        );

        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();
        $basket = $result['data']['basketSetDeliveryAddress'];

        $I->assertSame('User', $basket['owner']['firstName']);
        $I->assertSame(self::DELIVERY_ADDRESS_ID, $basket['deliveryAddress']['id']);

        $this->basketRemove($I, $basketId);
    }

    public function setDeliveryAddressToBasketWithoutToken(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreate($I);

        $I->logout();

        $I->sendGQLQuery(
            $this->basketSetDeliveryAddress($basketId, self::DELIVERY_ADDRESS_ID)
        );

        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Cannot query field "basketSetDeliveryAddress" on type "Mutation".',
            $result['errors'][0]['message']
        );

        $I->login(self::USERNAME, self::PASSWORD);
        $this->basketRemove($I, $basketId);
    }

    public function setDeliveryAddressToWrongBasket(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::PASSWORD);

        $basketId = $this->basketCreate($I);

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            $this->basketSetDeliveryAddress($basketId, self::DELIVERY_ADDRESS_ID)
        );

        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'You are not allowed to access this basket as it belongs to somebody else',
            $result['errors'][0]['message']
        );

        $I->login(self::OTHER_USERNAME, self::PASSWORD);
        $this->basketRemove($I, $basketId);
    }

    public function setDeliveryAddressToNonExistingBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            $this->basketSetDeliveryAddress('non-existing-basket-id', self::DELIVERY_ADDRESS_ID)
        );
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Basket was not found by id: non-existing-basket-id',
            $result['errors'][0]['message']
        );
    }

    public function setWrongDeliveryAddressToBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreate($I);

        $I->sendGQLQuery(
            $this->basketSetDeliveryAddress($basketId, self::WRONG_DELIVERY_ADDRESS_ID)
        );

        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();
        $I->assertSame(
            'Delivery address was not found by id: ' . self::WRONG_DELIVERY_ADDRESS_ID,
            $result['errors'][0]['message']
        );

        $this->basketRemove($I, $basketId);
    }

    public function setNonExistingDeliveryAddressToBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreate($I);

        $I->sendGQLQuery(
            $this->basketSetDeliveryAddress($basketId, 'non-existing-delivery-id')
        );

        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();
        $I->assertSame(
            'Delivery address was not found by id: non-existing-delivery-id',
            $result['errors'][0]['message']
        );

        $this->basketRemove($I, $basketId);
    }

    /**
     * @dataProvider basketDeliveryAddressProvider
     */
    public function getBasketDeliveryAddress(AcceptanceTester $I, Example $data): void
    {
        $basketId  = $data['basketId'];
        $addressId = $data['addressId'];

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            $this->basketDeliveryAddress($basketId)
        );

        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();
        $I->assertArrayNotHasKey('errors', $result);
        $basket = $result['data']['basket'];

        if ($addressId !== null) {
            $I->assertSame($addressId, $basket['deliveryAddress']['id']);
        } else {
            $I->assertNull($basket['deliveryAddress']);
        }
    }

    protected function basketDeliveryAddressProvider(): array
    {
        return [
            [
                'basketId'  => self::BASKET_WITH_ADDRESS_ID,
                'addressId' => self::DELIVERY_ADDRESS_ID,
            ],
            [
                'basketId'  => self::BASKET_WITHOUT_ADDRESS_ID,
                'addressId' => null,
            ],
        ];
    }

    private function basketCreate(AcceptanceTester $I)
    {
        $I->sendGQLQuery(
            'mutation {
                basketCreate(basket: {title: "' . self::BASKET_TITLE . '"}) {
                    id
                }
            }'
        );

        $result = $I->grabJsonResponseAsArray();

        return $result['data']['basketCreate']['id'];
    }

    private function basketRemove($I, string $basketId): void
    {
        $I->sendGQLQuery(
            'mutation {
                basketRemove (basketId: "' . $basketId . '")
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertTrue(
            $result['data']['basketRemove']
        );
    }

    private function basketSetDeliveryAddress(string $basketId, string $deliveryAddressId): string
    {
        return 'mutation {
            basketSetDeliveryAddress(basketId: "' . $basketId . '", deliveryAddressId: "' . $deliveryAddressId . '") {
                owner {
                    firstName
                }
                deliveryAddress {
                    id
                }
            }
        }';
    }

    private function basketDeliveryAddress(string $basketId): string
    {
        return 'query {
            basket(basketId: "' . $basketId . '") {
                id
                deliveryAddress {
                    id
                }
            }
        }';
    }
}
