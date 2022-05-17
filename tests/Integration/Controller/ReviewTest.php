<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\Tests\Integration\TokenTestCase;

final class ReviewTest extends TokenTestCase
{
    private const ACTIVE_REVIEW = '94415306f824dc1aa2fce0dc4f12783d';

    private const INACTIVE_REVIEW = 'bcb341381858129f7412beb11c827a25';

    private const REVIEW_PRODUCT = 'b56597806428de2f58b1c6c7d3e0e093';

    private const WRONG_USER = '_test_wrong_user';

    private const WRONG_PRODUCT = '_test_wrong_product';

    private const WRONG_OBJECT_TYPE = '_test_wrong_object_type';

    public function testGetSingleActiveReviewWithoutToken(): void
    {
        $result = $this->query(
            'query {
                review(reviewId: "' . self::ACTIVE_REVIEW . '") {
                    id
                    active
                    text
                    rating
                    createAt
                    reviewer {
                        firstName
                    }
                    product {
                        id
                        title
                    }
                }
            }'
        );

        $review = $result['body']['data']['review'];

        $this->assertSame([
            'id' => self::ACTIVE_REVIEW,
            'active' => true,
            'text' => 'Fantastic kite with great performance!',
            'rating' => 5,
            'createAt' => '2011-03-25T16:51:05+01:00',
            'reviewer' => [
                'firstName' => 'Marc',
            ],
            'product' => [
                'id' => self::REVIEW_PRODUCT,
                'title' => 'Kite NBK EVO 2010',
            ],
        ], $review);
    }

    public function testGetSingleActiveReviewWithAdminToken(): void
    {
        $this->prepareToken();

        $result = $this->query(
            'query {
                review(reviewId: "' . self::ACTIVE_REVIEW . '") {
                    id
                    active
                    text
                    rating
                    createAt
                    reviewer {
                        firstName
                    }
                    product {
                        id
                        title
                    }
                }
            }'
        );

        $review = $result['body']['data']['review'];

        $this->assertSame([
            'id' => self::ACTIVE_REVIEW,
            'active' => true,
            'text' => 'Fantastic kite with great performance!',
            'rating' => 5,
            'createAt' => '2011-03-25T16:51:05+01:00',
            'reviewer' => [
                'firstName' => 'Marc',
            ],
            'product' => [
                'id' => self::REVIEW_PRODUCT,
                'title' => 'Kite NBK EVO 2010',
            ],
        ], $review);
    }

    /**
     * @dataProvider getInactiveReviewDataProvider
     *
     * @param bool $moderation
     * @param bool $withToken
     * @param bool $expectError
     * @param bool $active
     */
    public function testGetInactiveReview($moderation, $withToken, $expectError, $active): void
    {
        $this->getConfig()->setConfigParam('blGBModerate', $moderation);

        if ($withToken) {
            $this->prepareToken();
        }

        $result = $this->query(
            'query {
                review (reviewId: "' . self::INACTIVE_REVIEW . '") {
                    id
                    active
                }
            }'
        );

        if ($expectError === true) {
            $this->assertSame(
                'Unauthorized',
                $result['body']['errors'][0]['message']
            );
        } else {
            $this->assertEquals(
                [
                    'id' => self::INACTIVE_REVIEW,
                    'active' => $active,
                ],
                $result['body']['data']['review']
            );
        }
    }

    public function getInactiveReviewDataProvider()
    {
        return [
            [
                'moderation' => true,
                'withToken' => false,
                'expectError' => true,
                'expectedActive' => false,
            ],
            [
                'moderation' => false,
                'withToken' => false,
                'expectError' => false,
                'expectedActive' => true,
            ],
            [
                'moderation' => true,
                'withToken' => true,
                'expectError' => false,
                'expectedActive' => false,
            ],
            [
                'moderation' => false,
                'withToken' => true,
                'expectError' => false,
                'expectedActive' => true,
            ],
        ];
    }

    public function testGetSingleNonExistingReview(): void
    {
        $result = $this->query(
            'query {
                review (reviewId: "DOES-NOT-EXIST") {
                    id
                }
            }'
        );

        $this->assertSame(
            'Review was not found by id: DOES-NOT-EXIST',
            $result['body']['errors'][0]['message']
        );
    }

    public function providerGetReviewFromNotExistingReviewer()
    {
        return [
            'admin' => [
                'username' => 'admin',
                'password' => 'admin',
            ],
            'user' => [
                'username' => 'user@oxid-esales.com',
                'password' => 'useruser',
            ],
        ];
    }

    /**
     * Case that the user related to review does not exist (inconsistent data).
     * Normal user is not allowed to query user data.
     *
     * @dataProvider providerGetReviewFromNotExistingReviewer
     */
    public function testGetReviewFromNotExistingReviewer(string $username, string $password): void
    {
        $this->prepareToken($username, $password);

        $result = $this->query(
            'query {
            review(reviewId: "' . self::WRONG_USER . '") {
                id
                reviewer {
                    firstName
                }
            }
        }'
        );

        $this->assertNull(
            $result['body']['data']['review']['reviewer']
        );
    }

    /**
     * @dataProvider nullProductIdsDataProvider
     */
    public function testGetWrongProductCase(string $username, string $password, string $id): void
    {
        $this->prepareToken($username, $password);

        $result = $this->query(
            'query {
            review(reviewId: "' . $id . '") {
                id
                product {
                    id
                }
            }
        }'
        );

        $review = $result['body']['data']['review'];

        $this->assertSame([
            'id' => $id,
            'product' => null,
        ], $review);
    }

    public function nullProductIdsDataProvider()
    {
        return [
            'admin_wrong_product' => [
                'username' => 'admin',
                'password' => 'admin',
                'oxid' => self::WRONG_PRODUCT,
            ],
            'user_wrong_product' => [
                'username' => 'user@oxid-esales.com',
                'password' => 'useruser',
                'oxid' => self::WRONG_PRODUCT,
            ],
            'admin_wrong_type' => [
                'username' => 'admin',
                'password' => 'admin',
                'oxid' => self::WRONG_OBJECT_TYPE,
            ],
            'user_wrong_type' => [
                'username' => 'user@oxid-esales.com',
                'password' => 'useruser',
                'oxid' => self::WRONG_OBJECT_TYPE,
            ],
        ];
    }

    /**
     * @dataProvider getReviewProductDataProvider
     *
     * @param array $token
     * @param mixed $product
     */
    public function testReviewWithInactiveProduct($token, $product): void
    {
        $queryBuilderFactory = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class);
        $queryBuilder = $queryBuilderFactory->create();

        // set product to inactive
        $queryBuilder
            ->update('oxarticles')
            ->set('oxactive', 0)
            ->where('OXID = :OXID')
            ->setParameter(':OXID', self::REVIEW_PRODUCT)
            ->execute();

        if ($token) {
            $this->prepareToken($token['username'], $token['password']);
        }

        $result = $this->query(
            'query {
            review(reviewId: "' . self::ACTIVE_REVIEW . '") {
                id
                product {
                    id
                    active
                }
            }
        }'
        );

        $this->assertSame([
            'id' => self::ACTIVE_REVIEW,
            'product' => $product,
        ], $result['body']['data']['review']);
    }

    public function getReviewProductDataProvider()
    {
        return [
            [
                'token' => null,
                'expectedProduct' => null,
            ],
            [
                'token' => [
                    'username' => 'user@oxid-esales.com',
                    'password' => 'useruser',
                ],
                'expectedProduct' => null,
            ],
            [
                'token' => [
                    'username' => 'admin',
                    'password' => 'admin',
                ],
                'expectedProduct' => [
                    'id' => self::REVIEW_PRODUCT,
                    'active' => false,
                ],
            ],
        ];
    }
}
