<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use Codeception\Scenario;
use Codeception\Util\HttpCode;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group oe_graphql_checkout
 * @group delivery-method
 * @group basket
 */
final class BasketSetDeliveryMethodMutationCest extends BaseCest
{
    private const USERNAME = 'standarduser@oxid-esales.com';

    private const OTHER_USERNAME = 'anotheruser@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const BASKET_TITLE = 'deliverymethodbasket';

    private const AVAILABLE_STANDARD_DELIVERY_SET_ID = 'oxidstandard';

    private const AVAILABLE_DELIVERY_SET_ID = '_deliveryset';

    private const UNAVAILABLE_DELIVERY_SET_ID = '_unavailabledeliverymethod';

    private const NON_EXISTING_DELIVERY_SET_ID = 'non-existing-delivery-set-id';

    private const NON_EXISTING_BASKET_ID = 'non-existing-basket-id';

    private const AVAILABLE_PRODUCT_ID = 'dc5ffdf380e15674b56dd562a7cb6aec';

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

    public function setAvailableDeliveryMethodToBasket(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            $this->basketSetDelivery(self::AVAILABLE_DELIVERY_SET_ID)
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();
        $basket = $result['data']['basketSetDeliveryMethod'];

        $I->assertSame($this->basketId, $basket['id']);
        $I->assertSame(self::AVAILABLE_DELIVERY_SET_ID, $basket['deliveryMethod']['id']);
        $I->assertSame(6.66, $basket['cost']['delivery']['price']);
    }

    public function testChangeDeliverySet(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            $this->basketSetDelivery(self::AVAILABLE_DELIVERY_SET_ID)
        );
        $result = $I->grabJsonResponseAsArray();
        $basket = $result['data']['basketSetDeliveryMethod'];

        $I->assertSame($this->basketId, $basket['id']);
        $I->assertSame(self::AVAILABLE_DELIVERY_SET_ID, $basket['deliveryMethod']['id']);
        $I->assertSame(6.66, $basket['cost']['delivery']['price']);

        $I->sendGQLQuery(
            $this->basketSetDelivery(self::AVAILABLE_STANDARD_DELIVERY_SET_ID)
        );
        $result = $I->grabJsonResponseAsArray();
        $basket = $result['data']['basketSetDeliveryMethod'];

        $I->assertSame($this->basketId, $basket['id']);
        $I->assertSame(self::AVAILABLE_STANDARD_DELIVERY_SET_ID, $basket['deliveryMethod']['id']);
        $I->assertSame(3.9, $basket['cost']['delivery']['price']);

        $I->sendGQLQuery(
            $this->basketSetDelivery(self::AVAILABLE_DELIVERY_SET_ID)
        );
        $result = $I->grabJsonResponseAsArray();
        $basket = $result['data']['basketSetDeliveryMethod'];

        $I->assertSame($this->basketId, $basket['id']);
        $I->assertSame(self::AVAILABLE_DELIVERY_SET_ID, $basket['deliveryMethod']['id']);
        $I->assertSame(6.66, $basket['cost']['delivery']['price']);
    }

    public function setUnavailableDeliveryMethodToBasket(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            $this->basketSetDelivery(self::UNAVAILABLE_DELIVERY_SET_ID)
        );

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function setNonExistingDeliveryMethodToBasket(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            $this->basketSetDelivery(self::NON_EXISTING_DELIVERY_SET_ID)
        );

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function setDeliveryMethodToWrongBasket(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            $this->basketSetDelivery(self::AVAILABLE_DELIVERY_SET_ID)
        );

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);

        // Login as the basket owner, because on _after the basket will be deleted
        $I->login(self::USERNAME, self::PASSWORD);
    }

    public function setDeliveryMethodToNonExistingBasket(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            $this->basketSetDelivery(self::AVAILABLE_DELIVERY_SET_ID, self::NON_EXISTING_BASKET_ID)
        );

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    private function basketCreate(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('
            mutation {
                basketCreate(basket: {title: "' . self::BASKET_TITLE . '"}) {
                    id
                }
            }
        ');

        $I->seeResponseCodeIs(HttpCode::OK);
        $result = $I->grabJsonResponseAsArray();

        $this->basketId = $result['data']['basketCreate']['id'];

        // Add a product because basket with no products will have 0 value and skip calculations
        $I->sendGQLQuery('
            mutation {
                basketAddProduct(
                    basketId: "' . $this->basketId . '",
                    productId: "' . self::AVAILABLE_PRODUCT_ID . '",
                    amount: 1
                ) {
                    id
                }
            }
        ');

        $I->seeResponseCodeIs(HttpCode::OK);
    }

    private function basketRemove($I): void
    {
        $I->sendGQLQuery('
            mutation {
                basketRemove (id: "' . $this->basketId . '")
            }
        ');

        $I->seeResponseCodeIs(HttpCode::OK);
    }

    private function basketSetDelivery(string $deliveryMethodId, ?string $basketId = null): string
    {
        $basketId = $basketId ?: $this->basketId;

        return 'mutation {
            basketSetDeliveryMethod(basketId: "' . $basketId . '", deliveryMethodId: "' . $deliveryMethodId . '") {
                id
                deliveryMethod {
                    id
                }
                cost {
                    total
                    delivery {
                        price
                    }
                }
            }
        }';
    }
}
