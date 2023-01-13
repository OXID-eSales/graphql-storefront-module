<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Product;

use Codeception\Example;
use Codeception\Scenario;
use OxidEsales\Eshop\Core\Element2ShopRelations;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group product
 * @group oe_graphql_storefront
 * @group other
 */
final class ProductMultishopCest extends MultishopBaseCest
{
    private const PRODUCT_ID = '058e613db53d782adfc9f2ccb43c45fe';

    private const PRODUCT_MAP_ID = '906';

    private const ACTIVE_PRODUCT_WITH_VARIANTS = '531b537118f5f4d7a427cdb825440922';

    private const ACTIVE_PRODUCT_WITH_VARIANTS_MAP_ID = '956';

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $I->updateInDatabase(
            'oxarticles',
            ['oxactive' => 1],
            ['oxid' => self::PRODUCT_ID]
        );
    }

    public function testGetSingleNotInShopActiveProductWillFail(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                product (productId: "' . self::PRODUCT_ID . '") {
                    id
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Product was not found by id: ' . self::PRODUCT_ID,
            $response['errors'][0]['message']
        );
    }

    public function testGetSingleInShopActiveProductWillWork(AcceptanceTester $I): void
    {
        $this->addProductToShop($I, 2, self::PRODUCT_MAP_ID);

        $I->sendGQLQuery(
            'query {
                product (productId: "' . self::PRODUCT_ID . '") {
                    id,
                    title
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'id' => self::PRODUCT_ID,
                'title' => 'Bindung O\'BRIEN DECADE CT',
            ],
            $response['data']['product']
        );

        $this->removeProductFromShop($I, 2, self::PRODUCT_MAP_ID);
    }

    /**
     * @dataProvider providerGetProductMultilanguage
     */
    public function testGetSingleTranslatedSecondShopProduct(AcceptanceTester $I, Example $data): void
    {
        $this->addProductToShop($I, 2, self::PRODUCT_MAP_ID);

        $I->sendGQLQuery(
            'query {
                product (productId: "' . self::PRODUCT_ID . '") {
                    id
                    title
                }
            }',
            null,
            $data['languageId'],
            $data['shopId']
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'id' => self::PRODUCT_ID,
                'title' => $data['title'],
            ],
            $response['data']['product']
        );

        $this->removeProductFromShop($I, 2, self::PRODUCT_MAP_ID);
    }

    protected function providerGetProductMultilanguage(): array
    {
        return [
            'shop_1_de' => [
                'shopId' => 1,
                'languageId' => 0,
                'title' => 'Bindung O\'BRIEN DECADE CT',
            ],
            'shop_1_en' => [
                'shopId' => 1,
                'languageId' => 1,
                'title' => 'Binding O\'BRIEN DECADE CT',
            ],
            'shop_2_de' => [
                'shopId' => 2,
                'languageId' => 0,
                'title' => 'Bindung O\'BRIEN DECADE CT',
            ],
            'shop_2_en' => [
                'shopId' => 2,
                'languageId' => 1,
                'title' => 'Binding O\'BRIEN DECADE CT',
            ],
        ];
    }

    /**
     * @dataProvider providerGetProductVariantsSubshop
     */
    public function testGetProductVariantsSubshop(AcceptanceTester $I, Example $data): void
    {
        $this->addProductToShop($I, 2, self::ACTIVE_PRODUCT_WITH_VARIANTS_MAP_ID);

        $I->sendGQLQuery(
            'query {
                product (productId: "' . self::ACTIVE_PRODUCT_WITH_VARIANTS . '") {
                    variantLabels
                    variants {
                        id
                        variantValues
                    }
                }
            }',
            null,
            $data['languageId'],
            $data['shopId']
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertSame(
            $response['data']['product']['variantLabels'],
            $data['labels']
        );

        $I->assertSame(
            $response['data']['product']['variants'][0] ?? $response['data']['product']['variants'],
            $data['variants']
        );

        $this->removeProductFromShop($I, 2, self::ACTIVE_PRODUCT_WITH_VARIANTS_MAP_ID);
    }

    protected function providerGetProductVariantsSubshop(): array
    {
        return [
            'shop_1_de' => [
                'shopId' => 1,
                'languageId' => 0,
                'labels' => [
                    'Größe',
                    'Farbe',
                ],
                'variants' => [
                    'id' => '6b6efaa522be53c3e86fdb41f0542a8a',
                    'variantValues' => [
                        'W 30/L 30',
                        'Blau',
                    ],
                ],
            ],
            'shop_1_en' => [
                'shopId' => 1,
                'languageId' => 1,
                'labels' => [
                    'Size',
                    'Color',
                ],
                'variants' => [
                    'id' => '6b6efaa522be53c3e86fdb41f0542a8a',
                    'variantValues' => [
                        'W 30/L 30',
                        'Blue ',
                    ],
                ],
            ],
            'shop_2_de' => [
                'shopId' => 2,
                'languageId' => 0,
                'labels' => [
                    'Größe',
                    'Farbe',
                ],
                'variants' => [],
            ],
            'shop_2_en' => [
                'shopId' => 2,
                'languageId' => 1,
                'labels' => [
                    'Size',
                    'Color',
                ],
                'variants' => [],
            ],
        ];
    }

    private function addProductToShop(AcceptanceTester $I, int $shopId, string $productId): void
    {
        $I->haveInDatabase(
            'oxarticles2shop',
            [
                'oxshopid' => $shopId,
                'oxmapobjectid' => $productId,
            ]
        );
    }

    private function removeProductFromShop(AcceptanceTester $I, int $shopId, string $productId): void
    {
        $I->deleteFromDatabase(
            'oxarticles2shop',
            [
                'oxshopid' => $shopId,
                'oxmapobjectid' => $productId,
            ]
        );
    }
}
