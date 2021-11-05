<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Category;

use Codeception\Scenario;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;
use OxidEsales\GraphQL\Storefront\Category\Exception\CategoryNotFound;

/**
 * @group category
 * @group oe_graphql_storefront
 */
final class CategoryCest extends BaseCest
{
    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        //this query ensures all seo urls are generated
        $I->sendGQLQuery('query {
                categories {
                    title
                    id
                    seo {
                        url
                    }
               }
        }');
        $I->sendGQLQuery('query {
                categories {
                    title
                    id
                    seo {
                        url
                    }
               }
        }', null, 1);
    }

    public function categoriesBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('filtering categories by parts of seo slug');

        $I->sendGQLQuery('query {
                categories (
                    filter: {
                        slug: {
                            like: "jeans"
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

        $I->assertEquals('jeans', $result['data']['categories'][0]['seo']['slug']);
        $I->assertEquals('jeans', $result['data']['categories'][1]['seo']['slug']);
    }

    public function categoryBySeoSlugInvalidParameterIdAndSLug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching category by slug and id fails');

        $I->sendGQLQuery('query {
                category (
                    categoryId: "some_id"
                    slug: "some_slug"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            CategoryNotFound::byParameter(),
            $result['errors'][0]['message']
        );
    }

    public function categoryBySeoSlugAmbiguous(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching category by slug which is not unique');

        $I->sendGQLQuery('query {
                category (
                    slug: "jeans"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            CategoryNotFound::byAmbiguousBySlug("jeans"),
            $result['errors'][0]['message']
        );
    }

    public function categoryNotFoundBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching category by slug which cannot be found');

        $I->sendGQLQuery('query {
                category (
                    slug: "this-is---nonexisting----slug"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            CategoryNotFound::bySlug("this-is---nonexisting----slug"),
            $result['errors'][0]['message']
        );
    }

    public function categoryBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching category by slug successfully');

        $searchBy = "fuer-sie";

        $I->sendGQLQuery('query {
                category (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['category']['id']);
        $categoryId = $result['data']['category']['id'];

        $I->sendGQLQuery('query {
                category (
                   categoryId: "' . $categoryId . '"
                ) {
                 seo {
                     slug
                 }
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        //fetch category by id and compare the slug
        $I->assertStringContainsString(
            strtolower($searchBy),
            $result['data']['category']['seo']['slug']
        );
    }

    public function categoryBySeoSlugByLanguage(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching category by slug successfully');

        $searchBy = "for-her";

        $I->sendGQLQuery('query {
                category (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }', null, 1);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        //category found by english slug for lang = 1
        $I->assertNotEmpty($result['data']['category']['id']);

        //query default language
        $I->sendGQLQuery('query {
                category (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            CategoryNotFound::bySlug($searchBy ),
            $result['errors'][0]['message']
        );
    }
}
