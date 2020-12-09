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
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group review
 */
final class ReviewCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const PRODUCT_WITH_EXISTING_REVIEW_ID = 'b56597806428de2f58b1c6c7d3e0e093';

    private const PRODUCT_ID = 'b56597806428de2f58b1c6c7d3e0e093';

    private const TEST_PRODUCT_ID = '_test_product_for_rating_5_';

    private const TEXT = 'Best product ever';

    private const TEST_DATA_REVIEW = '94415306f824dc1aa2fce0dc4f12783d';

    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const OTHER_PASSWORD = 'useruser';

    private const DIFFERENT_USERNAME = 'differentuser@oxid-esales.com';

    private const DIFFERENT_USER_PASSWORD = 'useruser';

    private const REVIEW_TEXT = 'Some text, containing a review for this product.';

    private const PRODUCT_WITH_AVERAGE_RATING = '_test_product_for_rating_avg';

    private $createdReviews = [];

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $I->updateConfigInDatabase('blAllowUsersToManageTheirReviews', true, 'bool');
    }

    public function _after(AcceptanceTester $I): void
    {
        //admin can delete every review
        $I->login('admin', 'admin');

        foreach ($this->createdReviews as $id) {
            $this->reviewDelete($I, $id);
        }
        $I->updateConfigInDatabase('blAllowUsersToManageTheirReviews', false, 'bool');
    }

    public function testSetReviewWithoutToken(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('mutation {
            reviewSet(review: {
                rating: 5,
                text: "' . self::TEXT . '",
                productId: "' . self::TEST_PRODUCT_ID . '"
            }){
                id
            }
        }');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @dataProvider setReviewDataProvider
     */
    public function testSetReview(AcceptanceTester $I, Example $data): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->reviewSet(
            $I,
            self::TEST_PRODUCT_ID,
            $data['text'],
            $data['rating']
        );
        $I->seeResponseCodeIs(HttpCode::OK);

        $reviewData = $result['data']['reviewSet'];
        $id         = $reviewData['id'];

        $I->assertIsString('%s', $id);
        $I->assertSame(self::TEST_PRODUCT_ID, $reviewData['product']['id']);
        $I->assertEquals($data['text'], $reviewData['text']);
        $I->assertEquals((int) $data['rating'], $reviewData['rating']);

        $result = $this->queryReview($I, $id);
        $I->assertEquals($data['text'], $result['data']['review']['text']);
        $I->assertEquals((int) $data['rating'], $result['data']['review']['rating']);
    }

    public function setReviewDataProvider()
    {
        return [
            'text_only' => [
                'text'   => self::TEXT,
                'rating' => '',
            ],
            'rating_only' => [
                'text'   => '',
                'rating' => '5',

            ],
            'text_and_rating' => [
                'text'   => self::TEXT,
                'rating' => '5',
            ],
        ];
    }

    public function testSetReviewInvalidInput(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->reviewSet($I, self::TEST_PRODUCT_ID, null, null);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->assertSame(
            'Review input cannot have both empty text and rating value.',
            $result['errors'][0]['message']
        );
    }

    public function testSetReviewRatingOutOfBounds(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->reviewSet($I, self::TEST_PRODUCT_ID, self::TEXT, '6');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->assertSame(
            'Rating must be between 1 and 5, was 6',
            $result['errors'][0]['message']
        );
    }

    public function testSetReviewWrongProduct(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->reviewSet($I, 'some_not_existing_product', self::TEXT, '5');

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->assertSame(
            'Product was not found by id: some_not_existing_product',
            $result['errors'][0]['message']
        );
    }

    public function testSetMultipleReviewsForOneProduct(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->reviewSet($I, self::PRODUCT_WITH_EXISTING_REVIEW_ID, self::TEXT, '4');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->assertSame(
            'Review for product with id: ' . self::PRODUCT_WITH_EXISTING_REVIEW_ID . ' already exists',
            $result['errors'][0]['message']
        );
    }

    /**
     *  NOTE: When querying a customer for reviews, all reviews disregarding of current
     *        langauge are shown. TODO: add Language DataType and relate to customer reviews
     */
    public function testUserReviews(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('query{
            customer {
                reviews{
                    id
                    text
                    rating
                }
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(3, count($result['data']['customer']['reviews']));
    }

    /**
     * NOTE: When querying a product for reviews, only reviews in the
     *       requested language are shown.
     *
     * @dataProvider providerProductMultiLanguageReview
     */
    public function testProductReviewsByLanguage(AcceptanceTester $I, Example $data): void
    {
        $result = $this->queryProduct($I, self::PRODUCT_WITH_EXISTING_REVIEW_ID, $data['language']);

        $I->seeResponseCodeIs(HttpCode::OK);

        $I->assertEquals($data['expected'], count($result['data']['product']['reviews']));
    }

    public function providerProductMultiLanguageReview()
    {
        return [
            'english' => [
                'language' => 1,
                'expected' => 1,
            ],
            'german' => [
                'language' => 0,
                'expected' => 0,
            ],
        ];
    }

    public function testProductAverageRating(AcceptanceTester $I): void
    {
        $I->login('admin', 'admin');

        //query, expected result: 2 ratings, average 2.0
        $result = $this->queryProduct($I, self::PRODUCT_WITH_AVERAGE_RATING);
        $I->seeResponseCodeIs(HttpCode::OK);
        $productRating = $result['data']['product']['rating'];
        $I->assertSame(2, $productRating['count']);
        $I->assertEquals(2.0, $productRating['rating']);

        //create
        $result = $this->reviewSet($I, self::PRODUCT_WITH_AVERAGE_RATING, self::TEXT, '5');
        $I->seeResponseCodeIs(HttpCode::OK);
        $review = $result['data']['reviewSet'];
        $I->assertSame(5, $review['rating']);

        //query, expected result: 3 ratings, average 3.0
        $result = $this->queryProduct($I, self::PRODUCT_WITH_AVERAGE_RATING);
        $I->seeResponseCodeIs(HttpCode::OK);
        $productRating = $result['data']['product']['rating'];
        $I->assertEquals(3, $productRating['rating']);
        $I->assertSame(3, $productRating['count']);

        //delete
        $this->reviewDelete($I, $review['id']);

        //query, expected result: 2 ratings, average 2.0
        $result = $this->queryProduct($I, self::PRODUCT_WITH_AVERAGE_RATING);
        $I->seeResponseCodeIs(HttpCode::OK);
        $productRating = $result['data']['product']['rating'];
        $I->assertEquals(2, $productRating['rating']);
        $I->assertSame(2, $productRating['count']);

        //rate again
        $result = $this->reviewSet($I, self::PRODUCT_WITH_AVERAGE_RATING, self::TEXT, '4');
        $I->seeResponseCodeIs(HttpCode::OK);
        $rating = $result['data']['reviewSet']['rating'];
        $I->assertSame(4, $rating);

        //query, expected result: 3 ratings, average 2.7
        $result = $this->queryProduct($I, self::PRODUCT_WITH_AVERAGE_RATING);
        $I->seeResponseCodeIs(HttpCode::OK);
        $productRating = $result['data']['product']['rating'];
        $I->assertSame(2.7, $productRating['rating']);
        $I->assertSame(3, $productRating['count']);
    }

    public function testProductAverageRatingSettingReviewWithoutRating(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        //Add review without rating
        $result = $this->reviewSet($I, self::TEST_PRODUCT_ID, self::TEXT, null);
        $I->seeResponseCodeIs(HttpCode::OK);
        $review = $result['data']['reviewSet'];
        $I->assertSame(0, $review['rating']);

        //Make sure the product is without rating
        $result = $this->queryProduct($I, self::TEST_PRODUCT_ID);
        $I->seeResponseCodeIs(HttpCode::OK);
        $productRating = $result['data']['product']['rating'];
        $I->assertSame(0, $productRating['count']);
        $I->assertEquals(0, $productRating['rating']);

        //Add review with user Y
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);

        //Add review with rating
        $result = $this->reviewSet($I, self::TEST_PRODUCT_ID, self::TEXT, '5');
        $I->seeResponseCodeIs(HttpCode::OK);
        $review = $result['data']['reviewSet'];
        $I->assertSame(5, $review['rating']);

        //Check product's average rating
        $result = $this->queryProduct($I, self::TEST_PRODUCT_ID);
        $I->seeResponseCodeIs(HttpCode::OK);
        $productRating = $result['data']['product']['rating'];
        $I->assertSame(1, $productRating['count']);
        $I->assertEquals(5, $productRating['rating']);
    }

    public function testDeleteReviewWithoutToken(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('mutation {
            reviewDelete(id: "' . self::TEST_DATA_REVIEW . '")
        }');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function testDeleteReviewByOtherUser(AcceptanceTester $I): void
    {
        $I->login(self::DIFFERENT_USERNAME, self::DIFFERENT_USER_PASSWORD);
        $result = $this->reviewSet($I, self::PRODUCT_ID, self::REVIEW_TEXT, '4');
        $I->seeResponseCodeIs(HttpCode::OK);
        $reviewId = $result['data']['reviewSet']['id'];

        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('mutation {
            reviewDelete(id: "' . $reviewId . '")
        }');

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    public function testDeleteNonExistentReview(AcceptanceTester $I): void
    {
        $I->login('admin', 'admin');

        $I->sendGQLQuery('mutation {
            reviewDelete(id: "something-that-does-not-exist")
        }');

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    public function testDeleteFailsIfManageFlagSetToFalse(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);
        $result = $this->reviewSet($I, self::TEST_PRODUCT_ID, self::REVIEW_TEXT, '4');
        $I->seeResponseCodeIs(HttpCode::OK);
        $reviewId = $result['data']['reviewSet']['id'];

        $I->updateConfigInDatabase('blAllowUsersToManageTheirReviews', false, 'bool');

        $I->sendGQLQuery('mutation {
            reviewDelete(id: "' . $reviewId . '")
        }');

        $I->updateConfigInDatabase('blAllowUsersToManageTheirReviews', true, 'bool');

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    public function testDeleteReviewWithoutRating(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        //Add review without rating
        $result = $this->reviewSet($I, self::TEST_PRODUCT_ID, self::TEXT, null);
        $I->seeResponseCodeIs(HttpCode::OK);
        $review = $result['data']['reviewSet'];
        $I->assertSame(0, $review['rating']);

        $this->reviewDelete($I, $review['id']);
    }

    private function reviewSet(AcceptanceTester $I, string $productId, ?string $text, ?string $rating): array
    {
        $query = 'mutation {
                    reviewSet(review: {
                        productId: "' . $productId . '",
                 ';

        if (!empty($text)) {
            $query .= ' text: "' . $text . '"
                       ';
        }

        if (!empty($rating)) {
            $query .= " rating: {$rating} ";
        }
        $query .= ' }
                      ){
                            id
                            product{
                                id
                            }
                            text
                            rating
                        }
                    }';

        $I->sendGQLQuery($query);
        $result = $I->grabJsonResponseAsArray();

        if (isset($result['data'])) {
            $newId                        = $result['data']['reviewSet']['id'];
            $this->createdReviews[$newId] = $newId;
        }

        return $result;
    }

    private function reviewDelete(AcceptanceTester $I, string $id): void
    {
        if (isset($this->createdReviews[$id])) {
            unset($this->createdReviews[$id]);
        }

        $I->sendGQLQuery('mutation {
             reviewDelete(id: "' . $id . '")
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(true, $result['data']['reviewDelete']);
    }

    private function queryReview(AcceptanceTester $I, string $id): array
    {
        $I->sendGQLQuery('query {
            review(id: "' . $id . '") {
                id
                text
                rating
            }
        }');

        return $I->grabJsonResponseAsArray();
    }

    private function queryProduct(AcceptanceTester $I, string $productId, int $language = 0): array
    {
        $I->sendGQLQuery(
            'query {
                product(id: "' . $productId . '") {
                    rating {
                        rating
                        count
                    }
                    reviews {
                        active
                        id
                        text
                        rating
                    }
                }
            }',
            [],
            $language
        );

        return $I->grabJsonResponseAsArray();
    }
}
