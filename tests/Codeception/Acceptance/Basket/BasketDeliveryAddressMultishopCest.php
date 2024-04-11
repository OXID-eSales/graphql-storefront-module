<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group oe_graphql_checkout
 * @group address
 * @group basket_1
 * @group oe_graphql_storefront
 */
final class BasketDeliveryAddressMultishopCest extends MultishopBaseCest
{
    private const USERNAME = 'standarduser@oxid-esales.com';

    private const OTHER_USERNAME = 'anotheruser@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const BASKET_TITLE = 'deliveryaddressbasketmultishop';

    private const DELIVERY_ID_1 = 'address_otheruser';

    private const DELIVERY_ID_2 = 'address_user';

    public function setDeliveryAddressToBasketFromShop1WithUserLoggedInShop2(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        $I->login(self::OTHER_USERNAME, self::PASSWORD, 1);
        $basketId = $this->basketCreate($I, 1);

        $I->login(self::OTHER_USERNAME, self::PASSWORD, 2);

        $I->sendGQLQuery(
            $this->basketSetDeliveryAddress($basketId, self::DELIVERY_ID_1),
            null,
            0,
            2
        );

        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();
        $basket = $result['data']['basketSetDeliveryAddress'];

        $I->assertSame('Marc', $basket['owner']['firstName']);
        $I->assertSame(self::DELIVERY_ID_1, $basket['deliveryAddress']['id']);

        $this->basketRemove($I, $basketId, 2);
    }

    public function setDeliveryAddressToBasketForShop2(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD, 2);
        $basketId = $this->basketCreate($I, 2);

        $I->sendGQLQuery(
            $this->basketSetDeliveryAddress($basketId, self::DELIVERY_ID_2),
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Delivery address was not found by id: ' . self::DELIVERY_ID_2,
            $result['errors'][0]['message']
        );

        $this->basketRemove($I, $basketId, 2);
    }

    private function basketCreate(AcceptanceTester $I, int $shopId)
    {
        $I->sendGQLQuery(
            'mutation {
                basketCreate(basket: {title: "' . self::BASKET_TITLE . '"}) {
                    id
                }
            }',
            null,
            0,
            $shopId
        );

        $result = $I->grabJsonResponseAsArray();

        return $result['data']['basketCreate']['id'];
    }

    private function basketRemove($I, string $basketId, int $shopId): void
    {
        $I->sendGQLQuery(
            'mutation {
                basketRemove (basketId: "' . $basketId . '")
            }',
            null,
            0,
            $shopId
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
}
