<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Manufacturer;

use Codeception\Scenario;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;
use OxidEsales\GraphQL\Storefront\Manufacturer\Exception\ManufacturerNotFound;

/**
 * @group manufacturer
 * @group oe_graphql_storefront
 */
final class ManufacturerCest extends BaseCest
{
    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        //this query ensures all seo urls are generated
        $I->sendGQLQuery('query {
                manufacturers {
                    title
                    id
                    seo {
                        url
                    }
               }
        }');
        $I->sendGQLQuery('query {
                manufacturers {
                    title
                    id
                    seo {
                        url
                    }
               }
        }', null, 1);
    }

    public function manufacturersBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('filtering manufacturers by parts of seo slug');

        $I->sendGQLQuery('query {
                manufacturers (
                    filter: {
                        slug: {
                            like: "ma"
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

        $I->assertEquals('big-matsol', $result['data']['manufacturers'][0]['seo']['slug']);
        $I->assertEquals('mauirippers', $result['data']['manufacturers'][1]['seo']['slug']);
    }

    public function manufacturerBySeoSlugInvalidParameterIdAndSLug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching manufacturer by slug and id fails');

        $I->sendGQLQuery('query {
                manufacturer (
                    manufacturerId: "some_id"
                    slug: "some_slug"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            ManufacturerNotFound::byParameter(),
            $result['errors'][0]['message']
        );
    }

    public function manufacturerBySeoSlugAmbiguous(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching manufacturer by slug which is not unique');

        $I->updateInDatabase('oxseo', ['oxseourl' => 'Nach-was-Anderem/Big-Matsol/'], ['oxseourl' => 'Nach-Hersteller/Mauirippers/']);

        $I->sendGQLQuery('query {
                manufacturer (
                    slug: "big-matsol"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            ManufacturerNotFound::byAmbiguousBySlug("big-matsol"),
            $result['errors'][0]['message']
        );
    }

    public function manufacturerNotFoundBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching manufacturer by slug which cannot be found');

        $I->sendGQLQuery('query {
                manufacturer (
                    slug: "this-is---nonexisting----slug"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            ManufacturerNotFound::bySlug("this-is---nonexisting----slug"),
            $result['errors'][0]['message']
        );
    }

    public function manufacturerBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching manufacturer by slug successfully');

        $searchBy = "liquid-force";

        $I->sendGQLQuery('query {
                manufacturer (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['manufacturer']['id']);
        $manufacturerId = $result['data']['manufacturer']['id'];

        $I->sendGQLQuery('query {
                manufacturer (
                   manufacturerId: "' . $manufacturerId . '"
                ) {
                 seo {
                     slug
                 }
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        //fetch manufacturer by id and compare the slug
        $I->assertStringContainsString(
            strtolower($searchBy),
            $result['data']['manufacturer']['seo']['slug']
        );
    }

    public function manufacturerBySeoSlugByLanguage(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching manufacturer by slug successfully');

        $searchBy = "liquid-force-kite";

        $I->sendGQLQuery('query {
                manufacturer (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }', null, 1);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        //manufacturer found by english slug for lang = 1
        $I->assertNotEmpty($result['data']['manufacturer']['id']);

        //query default language
        $I->sendGQLQuery('query {
                manufacturer (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            ManufacturerNotFound::bySlug($searchBy ),
            $result['errors'][0]['message']
        );
    }
}
