<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Review;

use Codeception\Example;
use Codeception\Scenario;
use Codeception\Util\HttpCode;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group review
 */
final class ReviewMultiShopCest extends MultishopBaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const PRODUCT_ID_SHOP_1 = '_test_product_wp1_';

    private const PRODUCT_ID_SHOP_2 = '_test_product_wp2_';

    private const PRODUCT_ID_BOTH_SHOPS = '_test_product_for_rating_5_';

    private const TEXT = 'shiny nice review text';

    private $createdReviews = [];

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $I->updateConfigInDatabaseForShops('blMallUsers', false, 'bool', [1, 2]);
        $I->updateConfigInDatabaseForShops('blAllowUsersToManageTheirReviews', true, 'bool', [1, 2]);
    }

    public function _after(AcceptanceTester $I): void
    {
        foreach ($this->createdReviews as $oneReview) {
            $I->logout();
            $this->reviewDelete($I, $oneReview['id'], $oneReview['shopId'], $oneReview['user']);
            $I->logout();
        }

        $this->createdReviews = [];

        parent::_after($I);
    }

    /**
     * @dataProvider dataProviderReviewPerShop
     */
    public function testReviewPerShop(AcceptanceTester $I, Example $data): void
    {
        $shopId = $data['shopId'];
        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $result = $this->reviewSet($I, $data['productId'], $shopId);
        $I->seeResponseCodeIs($data['expectedStatus']);

        $this->reviewSet($I, $data['productId'], $shopId);
        $I->seeResponseCodeIs($data['retryStatus']);

        if (isset($result['data']['reviewSet']['id'])) {
            $this->createdReviews[] = [
                'id'     => $result['data']['reviewSet']['id'],
                'shopId' => $shopId,
                'user'   => self::USERNAME,
            ];
        }
    }

    public function testMallUserReview(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        $I->login(self::OTHER_USERNAME, self::PASSWORD, 1);

        $result = $this->reviewSet($I, self::PRODUCT_ID_SHOP_1);
        $I->seeResponseCodeIs(HttpCode::OK);
        $reviewIdWithShop1Product = $result['data']['reviewSet']['id'];
        $this->createdReviews[]   = [
            'id'     => $reviewIdWithShop1Product,
            'shopId' => 1,
            'user'   => self::OTHER_USERNAME,
        ];

        $result = $this->reviewSet($I, self::PRODUCT_ID_BOTH_SHOPS);
        $I->seeResponseCodeIs(HttpCode::OK);
        $this->createdReviews[]   = [
            'id'     => $result['data']['reviewSet']['id'],
            'shopId' => 1,
            'user'   => self::OTHER_USERNAME,
        ];

        //let mall user set a review for same product in subshop
        $I->logout();
        $I->login(self::OTHER_USERNAME, self::PASSWORD, 2);

        //user already did give a review in subshop 1 so he cannot add a another one for same product
        $this->reviewSet($I, self::PRODUCT_ID_BOTH_SHOPS, 2);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);

        //review another product
        $result = $this->reviewSet($I, self::PRODUCT_ID_SHOP_2, 2);
        $I->seeResponseCodeIs(HttpCode::OK);
        $this->createdReviews[]   = [
            'id'     => $result['data']['reviewSet']['id'],
            'shopId' => 2,
            'user'   => self::OTHER_USERNAME,
        ];

        //get reviews for subshop
        $allReviews = $this->getReviews($I, false, 2);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->assertEquals(3, count($allReviews['data']['customer']['reviews']));

        //Here we have the case, that one of the products is not available in the subshop
        $allReviews = $this->getReviews($I, true, 2);
        $I->seeResponseCodeIs(HttpCode::OK);

        $reviews = $this->restructureResult($allReviews['data']['customer']['reviews']);
        $I->assertNull($reviews[$reviewIdWithShop1Product]);
    }

    protected function dataProviderReviewPerShop(): array
    {
        return [
            'shop1' => [
                'shopId'         => 1,
                'productId'      => self::PRODUCT_ID_SHOP_1,
                'expectedStatus' => 200,
                'retryStatus'    => 400,
            ],
            'shop2' => [
                'shopId'         => 2,
                'productId'      => self::PRODUCT_ID_SHOP_2,
                'expectedStatus' => 200,
                'retryStatus'    => 400,
            ],
            'shop1_with_shop2product' => [
                'shopId'         => 1,
                'productId'      => self::PRODUCT_ID_SHOP_2,
                'expectedStatus' => 404,
                'retryStatus'    => 404,
            ],
            'shop2_with_inheritedproduct' => [
                'shopId'         => 2,
                'productId'      => self::PRODUCT_ID_BOTH_SHOPS,
                'expectedStatus' => 200,
                'retryStatus'    => 400,
            ],
        ];
    }

    private function reviewSet(AcceptanceTester $I, string $productId, int $shopId = 1): array
    {
        $I->sendGQLQuery(
            'mutation {
                reviewSet(review: {
                    productId: "' . $productId . '",
                    text: "' . self::TEXT . '",
                    rating: 5
                }){
                    id
                    product{
                        id
                    }
                    text
                    rating
                }
            }',
            [],
            0,
            $shopId
        );

        return $I->grabJsonResponseAsArray();
    }

    private function reviewDelete(AcceptanceTester $I, string $id, int $shopId = 1, string $user = self::OTHER_USERNAME): void
    {
        $I->login($user, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'mutation {
                 reviewDelete(id: "' . $id . '")
            }',
            [],
            0,
            $shopId
        );
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    private function getReviews(AcceptanceTester $I, bool $queryProducts, int $shopId = 1)
    {
        $query = 'query {
                customer {
                    reviews {
                        id
                 ';

        if ($queryProducts) {
            $query .= ' product {
                            id
                        }';
        }
        $query .= '  }
                }
            }';

        $I->sendGQLQuery($query, null, 0, $shopId);

        return $I->grabJsonResponseAsArray();
    }

    private function restructureResult(array $reviews): array
    {
        $result = [];

        foreach ($reviews as $sub) {
            $result[$sub['id']] = $sub['product'];
        }

        return $result;
    }
}
