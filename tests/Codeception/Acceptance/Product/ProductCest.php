<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Product;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group product
 * @group oe_graphql_storefront
 */
final class ProductCest extends BaseCest
{
    private const PRODUCT_ID = 'dc5ffdf380e15674b56dd562a7cb6aec';

    public function productsBySeoSlug(AcceptanceTester $I): void
    {
        //this query ensure all seo urls are generated
        $I->sendGQLQuery('query {
                products {
                    title
                    id
                    seo {
                        url
                    }
               }
        }');

        $I->sendGQLQuery('query {
                products (
                    filter: {
                        slug: {
                            like: "Bindung-LIQUID-FORCE"
                        }
                    }
                    sort: {
                       title: "ASC"
                   }
                ) {
                    title
                    id
                    seo {
                        url
                        slug
                    }
               }
        }');

        $I->seeResponseIsJson();
        $result  = $I->grabJsonResponseAsArray();

        $I->assertEquals('Bindung-LIQUID-FORCE-INDEX-BOOT-2010.html', $result['data']['products'][0]['seo']['slug']);
        $I->assertEquals('Bindung-LIQUID-FORCE-TRANSIT-BOOT-2010.html', $result['data']['products'][1]['seo']['slug']);
    }
}
