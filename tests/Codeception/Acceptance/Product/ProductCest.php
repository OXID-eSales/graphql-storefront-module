<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Product;

use Codeception\Scenario;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;

/**
 * @group product
 * @group oe_graphql_storefront
 */
final class ProductCest extends BaseCest
{
    private const PRODUCT_ID = 'dc5ffdf380e15674b56dd562a7cb6aec';

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

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
                products {
                    title
                    id
                    seo {
                        url
                    }
               }
        }', null, 1);
    }

    public function productsBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('filtering products by parts of seo slug');

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
        $result = $I->grabJsonResponseAsArray();

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
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('Größe', $result['data']['products'][0]['variants'][0]['variantSelect'][0]['name']);
        $I->assertEquals('Farbe', $result['data']['products'][0]['variants'][0]['variantSelect'][1]['name']);
    }

    public function productBySeoSlugInvalidParameterIdAndSLug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching product by slug and id fails');

        $I->sendGQLQuery('query {
                product (
                    productId: "some_id"
                    slug: "some_slug"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            ProductNotFound::byParameter(),
            $result['errors'][0]['message']
        );
    }

    public function productBySeoSlugAmbiguous(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching product by slug which is not unique');

        $I->sendGQLQuery('query {
                product (
                    slug: "Kuyichi-Jeans"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            ProductNotFound::byAmbiguousBySlug("Kuyichi-Jeans"),
            $result['errors'][0]['message']
        );
    }

    public function productNotFoundBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching product by slug which cannot be found');

        $I->sendGQLQuery('query {
                product (
                    slug: "this-is---nonexisting----slug"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            ProductNotFound::bySlug("this-is---nonexisting----slug"),
            $result['errors'][0]['message']
        );
    }

    public function productBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching product by slug successfully');

        $searchBy = "Kuyichi-JEANS-anna";

        $I->sendGQLQuery('query {
                product (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['product']['id']);
        $productId = $result['data']['product']['id'];

        $I->sendGQLQuery('query {
                product (
                   productId: "' . $productId . '"
                ) {
                 seo {
                     slug
                 }
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        //fetch product by id and compare the slug
        $I->assertStringContainsString(
            strtolower($searchBy),
            $result['data']['product']['seo']['slug']
        );
    }

    public function productBySeoSlugByLanguage(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching product by slug successfully');

        $searchBy = "Binding-O-BRIEN-DECADE-CT-2010";

        $I->sendGQLQuery('query {
                product (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }', null, 1);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        //product found by english slug for lang = 1
        $I->assertNotEmpty($result['data']['product']['id']);

        //query default language
        $I->sendGQLQuery('query {
                product (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            ProductNotFound::bySlug($searchBy ),
            $result['errors'][0]['message']
        );
    }
}
