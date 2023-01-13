<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\GraphQL\Storefront\Tests\Integration\BaseTestCase;

final class ActionTest extends BaseTestCase
{
    private const ACTIVE_ACTION_WITH_PRODUCTS = 'oxtop5';

    private const ACTIVE_ACTION_WITH_INACTIVE_PRODUCT = 'oxcatoffer';

    private const ACTIVE_ACTION_WITHOUT_PRODUCTS = 'oxnewsletter';

    private const INACTIVE_ACTION = 'oxstart';

    private const WRONG_TYPE_ACTION = 'b5639c6431b26687321f6ce654878fa5';

    private const PRODUCT_RELATED_TO_ACTIVE_ACTION = 'ed6a4182ae58874e4fdaa4775566af6c';

    public function tearDown(): void
    {
        $this->setActiveState(self::PRODUCT_RELATED_TO_ACTIVE_ACTION, 'oxarticles', 1);

        parent::tearDown();
    }

    public function testGetSingleActiveActionWithoutProducts(): void
    {
        $result = $this->query(
            'query {
                action(actionId: "' . self::ACTIVE_ACTION_WITHOUT_PRODUCTS . '") {
                    id
                    active
                    title
                    products {
                        id
                    }
                }
            }'
        );

        $action = $result['body']['data']['action'];

        $this->assertEquals([
            'id' => self::ACTIVE_ACTION_WITHOUT_PRODUCTS,
            'active' => true,
            'title' => 'Newsletter',
            'products' => [],
        ], $action);
    }

    public function testGetSingleActiveActionWithProducts(): void
    {
        $result = $this->query(
            'query {
                action(actionId: "' . self::ACTIVE_ACTION_WITH_PRODUCTS . '") {
                    id
                    products{
                      id
                    }
                }
            }'
        );

        $products = $result['body']['data']['action']['products'];

        $this->assertCount(2, $products);

        $this->assertEquals([
            [
                'id' => 'fadc492a5807c56eb80b0507accd756b',
            ],
            [
                'id' => '058de8224773a1d5fd54d523f0c823e0',
            ],
        ], $products);
    }

    public function testGetSingleInactiveAction(): void
    {
        $result = $this->query(
            'query {
                action (actionId: "' . self::INACTIVE_ACTION . '") {
                    id
                    active
                }
            }'
        );

        $this->assertSame(
            'Unauthorized',
            $result['body']['errors'][0]['message']
        );
    }

    public function testGetSingleNonExistingAction(): void
    {
        $result = $this->query(
            'query {
                action (actionId: "non_existing_id") {
                    id
                    active
                }
            }'
        );

        $this->assertSame(
            'Action was not found by id: non_existing_id',
            $result['body']['errors'][0]['message']
        );
    }

    public function testGetSingleWrongTypeAction(): void
    {
        $result = $this->query(
            'query {
                action (actionId: "' . self::WRONG_TYPE_ACTION . '") {
                    id
                    active
                }
            }'
        );

        $this->assertSame(
            'Action was not found by id: ' . self::WRONG_TYPE_ACTION,
            $result['body']['errors'][0]['message']
        );
    }

    public function testGetSingleInactiveActionForAdminGroupUser(): void
    {
        $this->prepareToken();

        $result = $this->query(
            'query {
                action (actionId: "' . self::INACTIVE_ACTION . '") {
                    id
                    title
                    active
                }
            }'
        );

        $this->assertEquals([
            'id' => self::INACTIVE_ACTION,
            'title' => 'Startseite unten',
            'active' => false,
        ], $result['body']['data']['action']);
    }

    public function testGetActionsList(): void
    {
        $result = $this->query(
            'query {
                actions {
                    id,
                    title
                }
            }'
        );

        $this->assertCount(6, $result['body']['data']['actions']);

        $this->assertSame([
            [
                'id' => 'oxbargain',
                'title' => 'Angebote der Woche',
            ],
            [
                'id' => 'oxcatoffer',
                'title' => 'Kategorien-Topangebot',
            ],
            [
                'id' => 'oxnewest',
                'title' => 'Frisch eingetroffen',
            ],
            [
                'id' => 'oxnewsletter',
                'title' => 'Newsletter',
            ],
            [
                'id' => 'oxtop5',
                'title' => 'Topseller',
            ],
            [
                'id' => 'oxtopstart',
                'title' => 'Topangebot Startseite',
            ],
        ], $result['body']['data']['actions']);
    }

    public function testGetActionsListForAdminGroupUser(): void
    {
        $this->prepareToken();

        $result = $this->query(
            'query {
                actions {
                    id,
                    title,
                    active
                }
            }'
        );

        $this->assertCount(7, $result['body']['data']['actions']);

        $this->assertEquals([
            [
                'id' => 'oxbargain',
                'title' => 'Angebote der Woche',
                'active' => true,
            ],
            [
                'id' => 'oxcatoffer',
                'title' => 'Kategorien-Topangebot',
                'active' => true,
            ],
            [
                'id' => 'oxnewest',
                'title' => 'Frisch eingetroffen',
                'active' => true,
            ],
            [
                'id' => 'oxnewsletter',
                'title' => 'Newsletter',
                'active' => true,
            ],
            [
                'id' => 'oxstart',
                'title' => 'Startseite unten',
                'active' => false,
            ],
            [
                'id' => 'oxtop5',
                'title' => 'Topseller',
                'active' => true,
            ],
            [
                'id' => 'oxtopstart',
                'title' => 'Topangebot Startseite',
                'active' => true,
            ],
        ], $result['body']['data']['actions']);
    }

    /**
     * @dataProvider actionsListFilterProvider
     */
    public function testGetActionsListWithFilter(string $contains, array $expected): void
    {
        $result = $this->query(
            'query {
                actions(filter: {actionId: {contains: "' . $contains . '"}}) {
                    id
                    products{
                        id
                    }
                }
            }'
        );

        $this->assertEquals($expected, $result['body']['data']['actions']);
    }

    public function actionsListFilterProvider(): array
    {
        return [
            [
                'new',
                [
                    [
                        'id' => 'oxnewest',
                        'products' => [
                            [
                                'id' => 'f4f73033cf5045525644042325355732',
                            ],
                            [
                                'id' => 'f4f2d8eee51b0fd5eb60a46dff1166d8',
                            ],
                            [
                                'id' => 'b56369b1fc9d7b97f9c5fc343b349ece',
                            ],
                            [
                                'id' => 'd86e244c8114c8214fbf83da8d6336b3',
                            ],
                            [
                                'id' => '531b537118f5f4d7a427cdb825440922',
                            ],
                            [
                                'id' => 'b56597806428de2f58b1c6c7d3e0e093',
                            ],
                            [
                                'id' => '05848170643ab0deb9914566391c0c63',
                            ],
                            [
                                'id' => 'adcb9deae73557006a8ac748f45288b4',
                            ],
                        ],
                    ],
                    [
                        'id' => 'oxnewsletter',
                        'products' => [],
                    ],
                ],
            ],
            [
                'bar',
                [
                    [
                        'id' => 'oxbargain',
                        'products' => [
                            [
                                'id' => 'f4fe052346b4ec271011e25c052682c5',
                            ],
                            [
                                'id' => '058de8224773a1d5fd54d523f0c823e0',
                            ],
                            [
                                'id' => 'ed6573c0259d6a6fb641d106dcb2faec',
                            ],
                            [
                                'id' => 'f4fc98f99e3660bd2ecd7450f832c41a',
                            ]
                        ],
                    ],
                ],
            ],
            [
                'somethingThatDoesNotExist',
                [],
            ],
        ];
    }

    public function getActionProductListWithToken()
    {
        return [
            [
                'withToken' => false,
                'expectedProducts' => [
                    [
                        'id' => 'd86e244c8114c8214fbf83da8d6336b3',
                        'active' => true,
                    ],
                    [
                        'id' => 'ed6573c0259d6a6fb641d106dcb2faec',
                        'active' => true,
                    ],
                ],
            ],
            [
                'withToken' => true,
                'expectedProducts' => [
                    [
                        'id' => 'd86e244c8114c8214fbf83da8d6336b3',
                        'active' => true,
                    ],
                    [
                        'id' => 'ed6573c0259d6a6fb641d106dcb2faec',
                        'active' => true,
                    ],
                    // TODO: Using a valid token, this list should also contain inactive products
                    // [
                    //     'id'     => 'ed6a4182ae58874e4fdaa4775566af6c',
                    //     'active' => false
                    // ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getActionProductListWithToken
     *
     * @param mixed $withToken
     * @param mixed $expectedProducts
     */
    public function testActionsProductList($withToken, $expectedProducts): void
    {
        // set product to inactive
        $this->setActiveState(self::PRODUCT_RELATED_TO_ACTIVE_ACTION, 'oxarticles', 0);

        if ($withToken) {
            $this->prepareToken();
        }

        $result = $this->query(
            'query {
            actions(filter: {
                actionId: {
                    contains: "' . self::ACTIVE_ACTION_WITH_INACTIVE_PRODUCT . '"
                }
            }) {
                id
                products{
                    id
                    active
                }
            }
        }'
        );

        $this->assertCount(
            count($expectedProducts),
            $result['body']['data']['actions'][0]['products']
        );

        $this->assertEquals(
            $expectedProducts,
            $result['body']['data']['actions'][0]['products']
        );
    }

    /**
     * @dataProvider getActionProductListWithToken
     *
     * @param mixed $withToken
     * @param mixed $expectedProducts
     */
    public function testActionProductList($withToken, $expectedProducts): void
    {
        // set product to inactive
        $this->setActiveState(self::PRODUCT_RELATED_TO_ACTIVE_ACTION, 'oxarticles', 0);

        if ($withToken) {
            $this->prepareToken();
        }

        $result = $this->query(
            'query {
            action (actionId: "' . self::ACTIVE_ACTION_WITH_INACTIVE_PRODUCT . '") {
                id
                products {
                    id
                    active
                }
            }
        }'
        );

        $this->assertCount(
            count($expectedProducts),
            $result['body']['data']['action']['products']
        );

        $this->assertEquals(
            $expectedProducts,
            $result['body']['data']['action']['products']
        );
    }

    public function filterActionsByIdProvider(): array
    {
        return [
            [
                'withToken' => false,
                'isActionActive' => false,
                'expected' => [],
            ],
            [
                'withToken' => true,
                'isActionActive' => false,
                'expected' => [
                    [
                        'id' => self::INACTIVE_ACTION,
                        'active' => false,
                    ],
                ],
            ],
            [
                'withToken' => false,
                'isActionActive' => true,
                'expected' => [
                    [
                        'id' => self::ACTIVE_ACTION_WITH_PRODUCTS,
                        'active' => true,
                    ],
                ],
            ],
            [
                'withToken' => false,
                'isActionActive' => true,
                'expected' => [
                    [
                        'id' => self::ACTIVE_ACTION_WITH_PRODUCTS,
                        'active' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider filterActionsByIdProvider
     */
    public function testFilterActionsById(bool $withToken, bool $isActionActive, array $expected): void
    {
        if ($withToken) {
            $this->prepareToken();
        }

        $actionId = $isActionActive ? self::ACTIVE_ACTION_WITH_PRODUCTS : self::INACTIVE_ACTION;

        $result = $this->query(
            'query {
            actions(filter: {actionId: {equals: "' . $actionId . '"}}) {
                id
                active
            }
        }'
        );

        $this->assertEquals($expected, $result['body']['data']['actions']);
    }
}
