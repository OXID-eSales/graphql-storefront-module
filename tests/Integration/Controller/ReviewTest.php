<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Integration\Controller;

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
        $result = $this->query('query {
            review(id: "' . self::ACTIVE_REVIEW . '") {
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
        }');

        $this->assertResponseStatus(
            200,
            $result
        );

        $review = $result['body']['data']['review'];

        $this->assertSame([
            'id'            => self::ACTIVE_REVIEW,
            'active'        => true,
            'text'          => 'Fantastic kite with great performance!',
            'rating'        => 5,
            'createAt'      => '2011-03-25T16:51:05+01:00',
            'reviewer'      => [
                'firstName' => 'Marc',
            ],
            'product'       => [
                'id'    => self::REVIEW_PRODUCT,
                'title' => 'Kite NBK EVO 2010',
            ],
        ], $review);
    }

    public function testGetSingleActiveReviewWithAdminToken(): void
    {
        $this->prepareToken();

        $result = $this->query('query {
            review(id: "' . self::ACTIVE_REVIEW . '") {
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
        }');

        $this->assertResponseStatus(
            200,
            $result
        );

        $review = $result['body']['data']['review'];

        $this->assertSame([
            'id'            => self::ACTIVE_REVIEW,
            'active'        => true,
            'text'          => 'Fantastic kite with great performance!',
            'rating'        => 5,
            'createAt'      => '2011-03-25T16:51:05+01:00',
            'reviewer'      => [
                'firstName' => 'Marc',
            ],
            'product' => [
                'id'    => self::REVIEW_PRODUCT,
                'title' => 'Kite NBK EVO 2010',
            ],
        ], $review);
    }

    /**
     * @dataProvider getInactiveReviewDataProvider
     *
     * @param mixed $moderation
     * @param mixed $withToken
     * @param mixed $code
     * @param mixed $active
     */
    public function testGetInactiveReview($moderation, $withToken, $code, $active): void
    {
        $this->getConfig()->setConfigParam('blGBModerate', $moderation);

        if ($withToken) {
            $this->prepareToken();
        }

        $result = $this->query('query {
            review (id: "' . self::INACTIVE_REVIEW . '") {
                id
                active
            }
        }');

        $this->assertResponseStatus(
            $code,
            $result
        );

        if ($code === 200) {
            $this->assertEquals(
                [
                    'id'     => self::INACTIVE_REVIEW,
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
                'moderation'     => true,
                'withToken'      => false,
                'expectedCode'   => 401,
                'expectedActive' => false,
            ],
            [
                'moderation'     => false,
                'withToken'      => false,
                'expectedCode'   => 200,
                'expectedActive' => true,
            ],
            [
                'moderation'     => true,
                'withToken'      => true,
                'expectedCode'   => 200,
                'expectedActive' => false,
            ],
            [
                'moderation'     => false,
                'withToken'      => true,
                'expectedCode'   => 200,
                'expectedActive' => true,
            ],
        ];
    }

    public function testGetSingleNonExistingReview(): void
    {
        $result = $this->query('query {
            review (id: "DOES-NOT-EXIST") {
                id
            }
        }');

        $this->assertEquals(404, $result['status']);
    }

    public function providerGetReviewFromNotExistingReviewer()
    {
        return [
            'admin' => [
                'username' => 'admin',
                'password' => 'admin',
            ],
            'user'  => [
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

        $result = $this->query('query {
            review(id: "' . self::WRONG_USER . '") {
                id
                reviewer {
                    firstName
                }
            }
        }');

        $this->assertNull(
            $result['body']['data']['review']['reviewer']
        );
    }

    /**
     * @dataProvider nullProductIdsDataProvider
     */
    public function testGetWrongProductCase(string $username, string $password, string $id, int $expected): void
    {
        $this->prepareToken($username, $password);

        $result = $this->query('query {
            review(id: "' . $id . '") {
                id
                product {
                    id
                }
            }
        }');

        $this->assertResponseStatus(
            $expected,
            $result
        );

        $review = $result['body']['data']['review'];

        $this->assertSame([
            'id'      => $id,
            'product' => null,
        ], $review);
    }

    public function nullProductIdsDataProvider()
    {
        return [
            'admin_wrong_product' => [
                'username' => 'admin',
                'password' => 'admin',
                'oxid'     => self::WRONG_PRODUCT,
                'expected' => 200,
            ],
            'user_wrong_product'  => [
                'username' => 'user@oxid-esales.com',
                'password' => 'useruser',
                'oxid'     => self::WRONG_PRODUCT,
                'expected' => 200,
            ],
            'admin_wrong_type' => [
                'username' => 'admin',
                'password' => 'admin',
                'oxid'     => self::WRONG_OBJECT_TYPE,
                'expected' => 200,
            ],
            'user_wrong_type'  => [
                'username' => 'user@oxid-esales.com',
                'password' => 'useruser',
                'oxid'     => self::WRONG_OBJECT_TYPE,
                'expected' => 200,
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

        $result = $this->query('query {
            review(id: "' . self::ACTIVE_REVIEW . '") {
                id
                product {
                    id
                    active
                }
            }
        }');

        $this->assertResponseStatus(
            200,
            $result
        );

        $this->assertSame([
            'id'      => self::ACTIVE_REVIEW,
            'product' => $product,
        ], $result['body']['data']['review']);
    }

    public function getReviewProductDataProvider()
    {
        return [
            [
                'token'           => null,
                'expectedProduct' => null,
            ],
            [
                'token'           => [
                    'username' => 'user@oxid-esales.com',
                    'password' => 'useruser',
                ],
                'expectedProduct' => null,
            ],
            [
                'token'           => [
                    'username' => 'admin',
                    'password' => 'admin',
                ],
                'expectedProduct' => [
                    'id'     => self::REVIEW_PRODUCT,
                    'active' => false,
                ],
            ],
        ];
    }
}
