<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Product;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductVariant;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group product
 * @group other
 * @group oe_graphql_storefront
 */
final class ProductRelationsCest extends BaseCest
{
    private const ACTIVE_PRODUCT_WITH_VARIANTS = '6b66d82af984e5ad46b9cb27b1ef8aae';
    private const ACTIVE_MAIN_BUNDLE_PRODUCT = '_test_active_main_bundle';
    private const BUNDLE_PRODUCT = '_test_inactive_bundle';

    public function testGetInvisibleProductBundleItemRelation(AcceptanceTester $I): void
    {
        $I->login('admin', 'admin');

        $I->sendGQLQuery(
            'query {
                product (productId: "' . self::ACTIVE_MAIN_BUNDLE_PRODUCT . '") {
                    id
                    bundleProduct {
                        id
                    }
                }
            }',
            null
        );

        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            self::BUNDLE_PRODUCT,
            $result['data']['product']['bundleProduct']['id']
        );
    }

    public function testProductVariant(AcceptanceTester $I): void
    {
        Registry::getConfig()->saveShopConfVar('bool', 'blLoadVariants', true, 1);

        $I->sendGQLQuery(
            'query {
                product (productId: "' . self::ACTIVE_PRODUCT_WITH_VARIANTS . '") {
                    id
                    variants {
                        id
                    }
                }
            }',
            null
        );

        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            self::ACTIVE_PRODUCT_WITH_VARIANTS,
            $result['data']['product']['id']
        );
        $I->assertArrayNotHasKey('errors', $result);
    }

    public function testProductVariantWhenLoadingDisabled(AcceptanceTester $I): void
    {
        Registry::getConfig()->saveShopConfVar('bool', 'blLoadVariants', false, 1);

        $I->sendGQLQuery(
            'query {
                product (productId: "' . self::ACTIVE_PRODUCT_WITH_VARIANTS . '") {
                    id
                    variants {
                        id
                    }
                }
            }',
            null,
        );

        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            self::ACTIVE_PRODUCT_WITH_VARIANTS,
            $result['data']['product']['id']
        );
        $I->assertSame(
            ProductVariant::loadingDisabled(self::ACTIVE_PRODUCT_WITH_VARIANTS)->getMessage(),
            $result['errors'][0]['message']
        );
    }

    public function testProductsVariant(AcceptanceTester $I): void
    {
        Registry::getConfig()->saveShopConfVar('bool', 'blLoadVariants', true, 1);

        $I->sendGQLQuery(
            'query {
                products (filter: {
                    title: {
                        contains: "SUGAR"
                    }
                }) {
                    id
                    variants {
                        id
                    }
                }
            }',
            null
        );

        $result = $I->grabJsonResponseAsArray();
        $products = $result['data']['products'];

        $I->assertSame(
            self::ACTIVE_PRODUCT_WITH_VARIANTS,
            $products[0]['id']
        );
        $I->assertArrayNotHasKey('errors', $result);
    }

    public function testProductsVariantWhenLoadingDisabled(AcceptanceTester $I): void
    {
        Registry::getConfig()->saveShopConfVar('bool', 'blLoadVariants', false, 1);

        $I->sendGQLQuery(
            'query {
                products (filter: {
                    title: {
                        contains: "SUGAR"
                    }
                }) {
                    id
                    variants {
                        id
                    }
                }
            }',
            null
        );

        $result = $I->grabJsonResponseAsArray();
        $products = $result['data']['products'];

        $I->assertSame(
            self::ACTIVE_PRODUCT_WITH_VARIANTS,
            $products[0]['id']
        );
        $I->assertSame(
            ProductVariant::loadingDisabled(self::ACTIVE_PRODUCT_WITH_VARIANTS)->getMessage(),
            $result['errors'][0]['message']
        );
    }
}
