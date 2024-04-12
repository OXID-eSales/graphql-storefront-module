<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Product;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group product
 * @group other_1
 * @group oe_graphql_storefront
 */
final class ProductRelationsCest extends BaseCest
{
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
}
