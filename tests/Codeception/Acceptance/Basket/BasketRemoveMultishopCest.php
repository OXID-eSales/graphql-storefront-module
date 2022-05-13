<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketAccessForbidden;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group basket
 * @group oe_graphql_storefront
 */
final class BasketRemoveMultishopCest extends MultishopBaseCest
{
    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const OTHER_PASSWORD = 'useruser';

    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const PUBLIC_BASKET = '_test_basket_public'; //owned by shop1 user

    private const PRIVATE_BASKET = '_test_basket_private'; //owned by otheruser

    private const BASKET_NOTICE_LIST = 'noticelist';

    public function testRemoveNotOwnedBasketFromDifferentShop(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD, 2);

        $result = $this->removeBasket($I, self::PRIVATE_BASKET, 2);

        $I->assertSame(
            BasketAccessForbidden::byAuthenticatedUser()->getMessage(),
            $result['errors'][0]['message']
        );
    }

    public function testRemoveBasketFromDifferentShopNoToken(AcceptanceTester $I): void
    {
        $result = $this->removeBasket($I, self::PUBLIC_BASKET, 2);

        $I->assertStringStartsWith(
            'Cannot query field "basketRemove" on type "Mutation".',
            $result['errors'][0]['message']
        );
    }

    public function testRemoveOwnedBasketFromDifferentShop(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->createBasket($I, self::BASKET_NOTICE_LIST, 'false');
        $basketId = $result['data']['basketCreate']['id'];

        $I->logout();
        $I->login(self::USERNAME, self::PASSWORD, 2);
        $result = $this->removeBasket($I, $basketId, 2);

        $I->assertSame(
            BasketAccessForbidden::byAuthenticatedUser()->getMessage(),
            $result['errors'][0]['message']
        );

        $I->logout();
        $I->login(self::USERNAME, self::PASSWORD);
        $this->removeBasket($I, $basketId);
    }

    public function testRemoveBasketFromDifferentShopWithTokenForMallUser(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD, 2);

        $result = $this->createBasket($I, self::BASKET_NOTICE_LIST, 'false', 2);
        $basketId = $result['data']['basketCreate']['id'];

        $result = $this->removeBasket($I, $basketId, 2);
        $I->assertTrue($result['data']['basketRemove']);

        $result = $this->queryBasket($I, $basketId, 2);

        $I->assertSame(
            'Basket was not found by id: ' . $basketId,
            $result['errors'][0]['message']
        );
    }

    private function removeBasket(AcceptanceTester $I, string $id, int $shopId = 1): array
    {
        $I->sendGQLQuery(
            'mutation{
                basketRemove(basketId: "' . $id . '")
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function createBasket(AcceptanceTester $I, string $title, string $public = 'true', int $shopId = 1): array
    {
        $I->sendGQLQuery(
            'mutation {
                basketCreate(basket: {title: "' . $title . '", public: ' . $public . '}) {
                    id
                }
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function queryBasket(AcceptanceTester $I, string $id, int $shopId = 1): array
    {
        $I->sendGQLQuery(
            'query {
                basket(basketId: "' . $id . '"){
                    id
                }
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
