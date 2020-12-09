<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use Codeception\Util\HttpCode;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group basket
 */
final class BasketRemoveCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const OTHER_PASSWORD = 'useruser';

    public function testRemoveBasketNoToken(AcceptanceTester $I): void
    {
        $basketId = $this->createBasket($I);

        $this->basketRemoveMutation($I, $basketId);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);

        $this->deleteBasket($I, $basketId);
    }

    public function testRemoveBasketNotFound(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $this->basketRemoveMutation($I, 'this_is_no_saved_basket_id');

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    public function testRemoveBasketOfOtherCustomer(AcceptanceTester $I): void
    {
        $basketId = $this->createBasket($I);

        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);
        $this->basketRemoveMutation($I, $basketId);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);

        $this->deleteBasket($I, $basketId);
    }

    public function testRemoveBasketWithToken(AcceptanceTester $I): void
    {
        $basketId = $this->createBasket($I);

        $I->login(self::USERNAME, self::PASSWORD);
        $result = $this->basketRemoveMutation($I, $basketId);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->assertTrue($result['data']['basketRemove']);

        $this->deleteBasket($I, $basketId);
    }

    public function testRemoveBasketWithAdminToken(AcceptanceTester $I): void
    {
        $basketId = $this->createBasket($I);

        $I->login('admin', 'admin');
        $result = $this->basketRemoveMutation($I, $basketId);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->assertTrue($result['data']['basketRemove']);

        $this->deleteBasket($I, $basketId);
    }

    private function createBasket(AcceptanceTester $I): string
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('mutation {
            basketCreate(basket: {title: "new-basket-list"}) {
                id
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result =  $I->grabJsonResponseAsArray();

        $I->logout();

        return $result['data']['basketCreate']['id'];
    }

    private function deleteBasket(AcceptanceTester $I, string $basketId): void
    {
        $I->login(self::USERNAME, self::PASSWORD);
        $this->basketRemoveMutation($I, $basketId);
    }

    private function basketRemoveMutation(AcceptanceTester $I, string $basketId): array
    {
        $I->sendGQLQuery(
            'mutation {
                basketRemove(id: "' . $basketId . '")
            }'
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
