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
 * @group basket_1
 * @group basket_make_public
 * @group oe_graphql_storefront
 */
final class BasketMakePublicCest extends BaseCest
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
        $I->logout();
    }

    public function _after(AcceptanceTester $I): void
    {
        $this->removeBasket($I);
    }

    public function testMakePublicBasketNoToken(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'mutation {
                basketMakePublic(basketId: "' . $this->basketId . '"){
                    public
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertStringStartsWith(
            'Cannot query field "basketMakePublic" on type "Mutation".',
            $result['errors'][0]['message']
        );
    }

    public function testMakePublicBasketNotFound(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                basketMakePublic(basketId: "this_is_no_saved_basket_id"){
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

    public function testMakePublicBasketOfOtherCustomer(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                basketMakePublic(basketId: "' . $this->basketId . '"){
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

    public function testMakePublicBasketWithToken(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                basketMakePublic(basketId: "' . $this->basketId . '"){
                    public
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertTrue($result['data']['basketMakePublic']['public']);
    }

    private function prepareBasket(AcceptanceTester $I): string
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                basketCreate(basket: {
                    title: "test_basket",
                    public: false
                }) {
                    id
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

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
