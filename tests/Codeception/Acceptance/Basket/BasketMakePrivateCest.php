<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use Codeception\Scenario;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group basket
 */
final class BasketMakePrivateCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const OTHER_USERNAME = 'admin';

    private const OTHER_PASSWORD = 'admin';

    private $basketId;

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $this->basketId = $this->prepareBasket($I);
    }

    public function _after(AcceptanceTester $I): void
    {
        $this->removeBasket($I);
    }

    public function testMakePrivateBasketNotFound(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                basketMakePrivate(basketId: "this_is_no_saved_basket_id"){
                    public
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Basket was not found by id: this_is_no_saved_basket_id',
            $result['errors'][0]['message']
        );
    }

    public function testMakePrivateBasketOfOtherCustomer(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                basketMakePrivate(basketId: "' . $this->basketId . '"){
                    public
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'You are not allowed to access this basket as it belongs to somebody else',
            $result['errors'][0]['message']
        );
    }

    public function testMakePrivateBasketWithToken(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                basketMakePrivate(basketId: "' . $this->basketId . '"){
                    public
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertFalse($result['data']['basketMakePrivate']['public']);
    }

    private function prepareBasket(AcceptanceTester $I): string
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                basketCreate(basket: {
                    title: "test_basket",
                    public: true
                }) {
                    id
                }
            }'
        );

        $I->seeResponseIsJson();
        $result      = $I->grabJsonResponseAsArray();

        return $result['data']['basketCreate']['id'];
    }

    private function removeBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                basketRemove(basketId: "' . $this->basketId . '")
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertTrue(
            $result['data']['basketRemove']
        );
    }
}
