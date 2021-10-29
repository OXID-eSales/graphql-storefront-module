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
        $I->wantToTest('filtering products by parts of seo slug');

        //this query ensures all seo urls are generated
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

        $I->assertEquals('bindung-liquid-force-index-boot-2010', $result['data']['products'][0]['seo']['slug']);
        $I->assertEquals('bindung-liquid-force-transit-boot-2010', $result['data']['products'][1]['seo']['slug']);
    }

    public function productVariantSelect(AcceptanceTester $I): void
    {
        $I->wantToTest('variant select');

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
                            like: "Kuyichi-Jeans-ANNA"
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
                variants {
                   id
                   variantSelect {
                      name
                      value

                  }
                }
              }
        }');

        $I->seeResponseIsJson();
        $result  = $I->grabJsonResponseAsArray();

        $I->assertEquals('Größe', $result['data']['products'][0]['variants'][0]['variantSelect'][0]['name']);
        $I->assertEquals('Farbe', $result['data']['products'][0]['variants'][0]['variantSelect'][1]['name']);
    }
}
