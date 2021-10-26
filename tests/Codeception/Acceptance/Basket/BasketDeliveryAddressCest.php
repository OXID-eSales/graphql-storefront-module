<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use Codeception\Example;
use OxidEsales\GraphQL\Storefront\Address\Exception\DeliveryAddressNotFound;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketAccessForbidden;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketNotFound;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group oe_graphql_checkout
 * @group address
 * @group basket
 * @group oe_graphql_storefront
 */
final class BasketDeliveryAddressCest extends BaseCest
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
        $result   = $this->basketSetDeliveryAddress($I, $basketId, self::DELIVERY_ADDRESS_ID);
        $basket   = $result['data']['basketSetDeliveryAddress'];

        $I->assertSame('User', $basket['owner']['firstName']);
        $I->assertSame(self::DELIVERY_ADDRESS_ID, $basket['deliveryAddress']['id']);

        $this->basketRemove($I, $basketId);
    }

    public function setDeliveryAddressToBasketWithoutToken(AcceptanceTester $I): void
    {
        $result = $this->basketSetDeliveryAddress($I, 'some-basket-id', self::DELIVERY_ADDRESS_ID);

        $I->assertStringStartsWith(
            'Cannot query field "basketSetDeliveryAddress" on type "Mutation".',
            $result['errors'][0]['message']
        );
    }

    public function setDeliveryAddressToWrongBasket(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::PASSWORD);

        $basketId = $this->basketCreate($I);

        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->basketSetDeliveryAddress($I, $basketId, self::DELIVERY_ADDRESS_ID);

        $expectedException = BasketAccessForbidden::byAuthenticatedUser();

        $I->assertSame(
            $expectedException->getMessage(),
            $result['errors'][0]['message']
        );

        $I->login(self::OTHER_USERNAME, self::PASSWORD);
        $this->basketRemove($I, $basketId);
    }

    public function setDeliveryAddressToNonExistingBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->basketSetDeliveryAddress($I, 'non-existing-basket-id', self::DELIVERY_ADDRESS_ID);

        $expectedException = BasketNotFound::byId('non-existing-basket-id');

        $I->assertSame(
            $expectedException->getMessage(),
            $result['errors'][0]['message']
        );
    }

    public function setWrongDeliveryAddressToBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreate($I);
        $result   = $this->basketSetDeliveryAddress($I, $basketId, self::WRONG_DELIVERY_ADDRESS_ID);

        $expectedException = DeliveryAddressNotFound::byId(self::WRONG_DELIVERY_ADDRESS_ID);
        $I->assertSame(
            $expectedException->getMessage(),
            $result['errors'][0]['message']
        );

        $this->basketRemove($I, $basketId);
    }

    public function setNonExistingDeliveryAddressToBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreate($I);
        $result   = $this->basketSetDeliveryAddress($I, $basketId, 'non-existing-delivery-id');

        $expectedException = DeliveryAddressNotFound::byId('non-existing-delivery-id');
        $I->assertSame(
            $expectedException->getMessage(),
            $result['errors'][0]['message']
        );

        $this->basketRemove($I, $basketId);
    }

    public function setNullDeliveryAddressToBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreate($I);
        $result   = $this->basketSetDeliveryAddress($I, $basketId, null);
        $basket   = $result['data']['basketSetDeliveryAddress'];

        $I->assertNull($basket['deliveryAddress']);

        $this->basketRemove($I, $basketId);
    }

    public function setEmptyStringAsDeliveryAddressToBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreate($I);
        $result   = $this->basketSetDeliveryAddress($I, $basketId, '');

        $expectedException = DeliveryAddressNotFound::byId('');
        $I->assertSame(
            $expectedException->getMessage(),
            $result['errors'][0]['message']
        );

        $this->basketRemove($I, $basketId);
    }

    public function resetDeliveryAddressToBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $basketId = $this->basketCreate($I);
        $result   = $this->basketSetDeliveryAddress($I, $basketId, self::DELIVERY_ADDRESS_ID);
        $basket   = $result['data']['basketSetDeliveryAddress'];
        $I->assertSame(self::DELIVERY_ADDRESS_ID, $basket['deliveryAddress']['id']);

        $basket = $this->basketSetDeliveryAddress($I, $basketId)['data']['basketSetDeliveryAddress'];
        $I->assertNull($basket['deliveryAddress']);

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

    private function basketSetDeliveryAddress(AcceptanceTester $I, string $basketId, ?string $deliveryAddressId = null): array
    {
        $optionalArguments = '';

        if ($deliveryAddressId !== null) {
            $optionalArguments = ', deliveryAddressId: "' . $deliveryAddressId . '"';
        }

        $I->sendGQLQuery(
            'mutation {
                basketSetDeliveryAddress(basketId: "' . $basketId . '"' . $optionalArguments . ') {
                    owner {
                        firstName
                    }
                    deliveryAddress {
                        id
                    }
                }
            }'
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
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
