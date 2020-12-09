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
final class BasketOwnerRelationCest extends BaseCest
{
    private const USERNAME = 'deletebytest@oxid-esales.com';

    private const USER_ID = '309db395b6c85c3881fcb9b437a73ff5';

    private const OTHER_USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    /** @var string */
    private $basketId;

    public function _after(AcceptanceTester $I): void
    {
        $I->deleteFromDatabase(
            'oxuserbaskets',
            [
                'OXID' => $this->basketId,
            ]
        );
    }

    public function testGetPublicBasketWhichOwnerDoesNotExist(AcceptanceTester $I): void
    {
        $this->basketId = $this->createPublicBasket($I);
        $this->deleteUser($I, self::USER_ID);

        $I->login(self::OTHER_USERNAME, self::PASSWORD);
        $I->sendGQLQuery(
            'query {
                basket(id: "' . $this->basketId . '") {
                    id
                    owner {
                        firstName
                    }
                }
            }'
        );

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    private function createPublicBasket(AcceptanceTester $I): string
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('mutation {
            basketCreate(basket: {title: "new-basket-list", public: true}) {
                id
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result      = $I->grabJsonResponseAsArray();

        $I->logout();

        return $result['data']['basketCreate']['id'];
    }

    private function deleteUser(AcceptanceTester $I, string $userId): void
    {
        $I->deleteFromDatabase(
            'oxuser',
            [
                'OXID' => $userId,
            ]
        );
    }
}
