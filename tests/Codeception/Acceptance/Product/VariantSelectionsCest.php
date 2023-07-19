<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Product;

use Codeception\Example;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group product
 * @group product_variants
 * @group other
 * @group oe_graphql_storefront
 */
final class VariantSelectionsCest extends BaseCest
{
    private const PRODUCT_WITH_VARIANTS_ID = '6b66d82af984e5ad46b9cb27b1ef8aae';
    private const WRONG_PRODUCT_ID = 'wrong_product_id';
    private const NOT_PRODUCT_WITH_VARIANTS = 'dc5ffdf380e15674b56dd562a7cb6aec';

    private const PRODUCT_VARIANT_SELECTION_SIZE_1 = '5d4bc935f54e8f1f2cf08741638e1fcd'; // W 31/L 34
    private const PRODUCT_VARIANT_SELECTION_SIZE_2 = 'c033d4abea909bf1b3ccc19aa3469873'; // W 31/L 32

    private const PRODUCT_VARIANT_SELECTION_COLOR_1 = 'fffecac98899bf152fa73a094155ec58'; // Dark Red
    private const PRODUCT_VARIANT_SELECTION_COLOR_2 = '560173ba980b9f5f7f8d9d3cce1c2446'; // Dark Blue

    private const PRODUCT_VARIANT_SELECTION_WASH_1 = 'd2a044259fd6006c4f119fe16ba720df'; // Predded Green
    private const PRODUCT_VARIANT_SELECTION_WASH_2 = 'd015e249c62ca12180f76bbe0b88d7c0'; // Bangle Blue

    private const PRODUCT_ACTIVE_VARIANT_ID_1 = '6b6b9f89cb8decee837d1a4c60742875'; // W 31/L 34
    private const PRODUCT_ACTIVE_VARIANT_ID_2 = '6b66f4b02ad619cdadb7ea04b6c19cc2'; // Dark Blue

    public function testWrongProductId(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                variantSelections(
                    productId: "' . self::WRONG_PRODUCT_ID . '",
                    varSelIds: null
                ) {
                    selections {
                        label
                        activeSelection {
                            name
                        }
                        fields {
                            name
                        }
                    }
                }
            }',
            [],
            1
        );

        $result = $I->grabJsonResponseAsArray();

        $expectedException = new ProductNotFound(self::WRONG_PRODUCT_ID);
        $I->assertSame(
            $expectedException->getMessage(),
            $result['errors'][0]['message']
        );
    }

    public function testVariantSelectionsOnVariantProduct(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                variantSelections(
                    productId: "' . self::PRODUCT_ACTIVE_VARIANT_ID_1 . '",
                    varSelIds: null
                ) {
                    selections {
                        label
                        activeSelection {
                            name
                        }
                        fields {
                            name
                        }
                    }
                    activeVariant {
                        id
                        title
                        variantValues
                    }
                }
            }',
            [],
            1
        );

        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            self::PRODUCT_ACTIVE_VARIANT_ID_1,
            $result['data']['variantSelections']['activeVariant']['id']
        );
    }

    public function testGetVariantSelectionsOnProductWithoutVariants(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                variantSelections(
                    productId: "' . self::NOT_PRODUCT_WITH_VARIANTS . '",
                    varSelIds: null
                ) {
                    selections {
                        label
                        activeSelection {
                            name
                        }
                        fields {
                            name
                        }
                    }
                    activeVariant {
                        id
                    }
                }
            }',
            [],
            1
        );

        $result = $I->grabJsonResponseAsArray();

        $I->assertNull(
            $result['data']['variantSelections']
        );
    }

    /**
     * @dataProvider selectionFieldsProvider
     */
    public function testCheckVariantSelectionsFields(AcceptanceTester $I, Example $data): void
    {
        $I->sendGQLQuery(
            'query {
                variantSelections(
                    productId: "' . self::PRODUCT_WITH_VARIANTS_ID . '",
                    varSelIds: ' . $data['varSelIds'] . '
                ) {
                    selections {
                        label
                        fields {
                            name
                            active
                            disabled
                        }
                    }
                }
            }',
            [],
            1
        );

        $result = $I->grabJsonResponseAsArray();

        foreach ($result['data']['variantSelections']['selections'] as $selection) {
            foreach ($selection['fields'] as $field) {
                if ($field['active']) {
                    $I->assertTrue(
                        in_array($field['name'], $data['expectedFields']['active'])
                    );
                }

                if ($field['disabled']) {
                    $I->assertTrue(
                        in_array($field['name'], $data['expectedFields']['disabled'])
                    );
                }
            }
        }
    }

    private function selectionFieldsProvider(): array
    {
        return [
            'size_selected_product' => [
                'varSelIds' => '[
                    "' . self:: PRODUCT_VARIANT_SELECTION_SIZE_1 . '"
                ]',
                'expectedFields' => [
                    'active' => [
                        'W 31/L 34'
                    ],
                    'disabled' => [
                        'Dark Red',
                        'Predded Green'
                    ]
                ]
            ],
            'color_selected_product' => [
                'varSelIds' => '[
                    "",
                    "' . self::PRODUCT_VARIANT_SELECTION_COLOR_1 . '"
                ]',
                'expectedFields' => [
                    'active' => [
                        'Dark Red'
                    ],
                    'disabled' => [
                        'W 31/L 34',
                        'W 32/L 32',
                        'W 34/L 34',
                        'Predded Green'
                    ]
                ]
            ],
            'washing_selected_product' => [
                'varSelIds' => '[
                    "",
                    "",
                    "' . self::PRODUCT_VARIANT_SELECTION_WASH_1 . '"
                ]',
                'expectedFields' => [
                    'active' => [
                        'Predded Green'
                    ],
                    'disabled' => [
                        'W 31/L 34',
                        'W 32/L 32',
                        'W 34/L 34',
                        'Dark Red'
                    ]
                ]
            ],
            'size_color_selected_product' => [
                'varSelIds' => '[
                    "' . self::PRODUCT_VARIANT_SELECTION_SIZE_2 . '",
                    "' . self::PRODUCT_VARIANT_SELECTION_COLOR_2 . '"
                ]',
                'expectedFields' => [
                    'active' => [
                        'W 31/L 32',
                        'Dark Blue'
                    ],
                    'disabled' => [
                        'Bangle Blue'
                    ]
                ]
            ],
            'size_washing_selected_product' => [
                'varSelIds' => '[
                    "' . self::PRODUCT_VARIANT_SELECTION_SIZE_2 . '",
                    "",
                    "' . self::PRODUCT_VARIANT_SELECTION_WASH_2 . '"
                ]',
                'expectedFields' => [
                    'active' => [
                        'W 31/L 32',
                        'Bangle Blue'
                    ],
                    'disabled' => [
                        'Dark Blue'
                    ]
                ]
            ],
            'color_washing_selected_product' => [
                'varSelIds' => '[
                    "",
                    "' . self::PRODUCT_VARIANT_SELECTION_COLOR_2 . '",
                    "' . self::PRODUCT_VARIANT_SELECTION_WASH_2 . '"
                ]',
                'expectedFields' => [
                    'active' => [
                        'Bangle Blue',
                        'Dark Blue'
                    ],
                    'disabled' => [
                        'W 31/L 32'
                    ]
                ]
            ],
        ];
    }

    /**
     * @dataProvider variantSelectionsProvider
     */
    public function testGetVariantSelections(AcceptanceTester $I, Example $data): void
    {
        $I->sendGQLQuery(
            'query {
                variantSelections(
                    productId: "' . self::PRODUCT_WITH_VARIANTS_ID . '",
                    varSelIds: ' . $data['varSelIds'] . '
                ) {
                    selections {
                        label
                        activeSelection {
                            name
                            active
                        }
                    }
                    activeVariant {
                        id
                    }
                }
            }',
            [],
            1
        );

        $result = $I->grabJsonResponseAsArray();

        foreach ($result['data']['variantSelections']['selections'] as $selection) {
            $I->assertEquals(
                $data['expected'][$selection['label']]['activeSelection'],
                $selection['activeSelection']
            );
        }

        $I->assertSame(
            $data['expected']['activeVariant'],
            $result['data']['variantSelections']['activeVariant']
        );
    }

    private function variantSelectionsProvider(): array
    {
        return [
            'parent_product_no_selection' => [
                'varSelIds' => "null",
                'expected' => [
                    'Size' => [
                        'activeSelection' => null
                    ],
                    'Color' => [
                        'activeSelection' => null
                    ],
                    'Washing' => [
                        'activeSelection' => null
                    ],
                    'activeVariant' => null,
                ],
            ],
            'size_selected_product' => [
                'varSelIds' => '[
                    "' . self:: PRODUCT_VARIANT_SELECTION_SIZE_1 . '"
                ]',
                'expected' => [
                    'Size' => [
                        'activeSelection' => [
                            'name' => 'W 31/L 34',
                            'active' => true
                        ]
                    ],
                    'Color' => [
                        'activeSelection' => null
                    ],
                    'Washing' => [
                        'activeSelection' => null
                    ],
                    'activeVariant' => null,
                ],
            ],
            'color_selected_product' => [
                'varSelIds' => '[
                    "",
                    "' . self::PRODUCT_VARIANT_SELECTION_COLOR_2 . '"
                ]',
                'expected' => [
                    'Size' => [
                        'activeSelection' => null
                    ],
                    'Color' => [
                        'activeSelection' => [
                            'name' => 'Dark Blue',
                            'active' => true
                        ]
                    ],
                    'Washing' => [
                        'activeSelection' => null
                    ],
                    'activeVariant' => null,
                ],
            ],
            'washing_selected_product' => [
                'varSelIds' => '[
                    "",
                    "",
                    "' . self::PRODUCT_VARIANT_SELECTION_WASH_2 . '"
                ]',
                'expected' => [
                    'Size' => [
                        'activeSelection' => null
                    ],
                    'Color' => [
                        'activeSelection' => null
                    ],
                    'Washing' => [
                        'activeSelection' => [
                            'name' => 'Bangle Blue',
                            'active' => true
                        ]
                    ],
                    'activeVariant' => null,
                ],
            ],
            'size_color_selected_product' => [
                'varSelIds' => '[
                    "' . self::PRODUCT_VARIANT_SELECTION_SIZE_2 . '",
                    "' . self::PRODUCT_VARIANT_SELECTION_COLOR_1 . '"
                ]',
                'expected' => [
                    'Size' => [
                        'activeSelection' => [
                            'name' => 'W 31/L 32',
                            'active' => true
                        ]
                    ],
                    'Color' => [
                        'activeSelection' => [
                            'name' => 'Dark Red',
                            'active' => true
                        ]
                    ],
                    'Washing' => [
                        'activeSelection' => null
                    ],
                    'activeVariant' => null,
                ],
            ],
            'perfect_fit' => [
                'varSelIds' => '[
                    "' . self::PRODUCT_VARIANT_SELECTION_SIZE_2 . '",
                    "' . self::PRODUCT_VARIANT_SELECTION_COLOR_2 . '",
                    "' . self::PRODUCT_VARIANT_SELECTION_WASH_1 . '"
                ]',
                'expected' => [
                    'Size' => [
                        'activeSelection' => [
                            'name' => 'W 31/L 32',
                            'active' => true
                        ]
                    ],
                    'Color' => [
                        'activeSelection' => [
                            'name' => 'Dark Blue',
                            'active' => true
                        ]
                    ],
                    'Washing' => [
                        'activeSelection' => [
                            'name' => 'Predded Green',
                            'active' => true
                        ]
                    ],
                    'activeVariant' => [
                        'id' => self::PRODUCT_ACTIVE_VARIANT_ID_2
                    ],
                ],
            ],
        ];
    }
}
