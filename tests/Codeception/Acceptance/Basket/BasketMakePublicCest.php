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
 * @group basket
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
                basketMakePublic(id: "' . $this->basketId . '"){
                    public
                }
            }'
        );

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function testMakePublicBasketNotFound(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                basketMakePublic(id: "this_is_no_saved_basket_id"){
                    public
                }
            }'
        );

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    public function testMakePublicBasketOfOtherCustomer(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                basketMakePublic(id: "' . $this->basketId . '"){
                    public
                }
            }'
        );

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    public function testMakePublicBasketWithToken(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                basketMakePublic(id: "' . $this->basketId . '"){
                    public
                }
            }'
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result      = $I->grabJsonResponseAsArray();

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

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result      = $I->grabJsonResponseAsArray();

        return $result['data']['basketCreate']['id'];
    }

    private function removeBasket(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'mutation {
                basketRemove(id: "' . $this->basketId . '")
            }'
        );

        $I->seeResponseCodeIs(HttpCode::OK);
    }
}
