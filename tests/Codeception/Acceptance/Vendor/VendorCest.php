<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Vendor;

use Codeception\Scenario;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;
use OxidEsales\GraphQL\Storefront\Vendor\Exception\VendorNotFound;

/**
 * @group vendor
 * @group oe_graphql_storefront
 */
final class VendorCest extends BaseCest
{
    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $this->prepareTestData($I);
    }

    public function vendorsBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('filtering vendors by parts of seo slug');

        $I->sendGQLQuery('query {
                vendors (
                    filter: {
                        slug: {
                            like: "brush"
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

        $I->assertEquals('fake-brush', $result['data']['vendors'][0]['seo']['slug']);
        $I->assertEquals('true-brush', $result['data']['vendors'][1]['seo']['slug']);
    }

    public function vendorBySeoSlugInvalidParameterIdAndSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching vendor by slug and id fails');

        $I->sendGQLQuery('query {
                vendor (
                    vendorId: "some_id"
                    slug: "some_slug"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            VendorNotFound::byParameter(),
            $result['errors'][0]['message']
        );
    }

    public function vendorBySeoSlugAmbiguous(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching vendor by slug which is not unique');

        $I->updateInDatabase('oxseo', ['oxseourl' => 'Nach-was-Anderem/fake-brush/'], ['oxseourl' => 'Nach-Lieferant/true-brush/']);

        $I->sendGQLQuery('query {
                vendor (
                    slug: "fake-brush"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            VendorNotFound::byAmbiguousBySlug('fake-brush'),
            $result['errors'][0]['message']
        );
    }

    public function vendorNotFoundBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching vendor by slug which cannot be found');

        $I->sendGQLQuery('query {
                vendor (
                    slug: "this-is---nonexisting----slug"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            VendorNotFound::bySlug('this-is---nonexisting----slug'),
            $result['errors'][0]['message']
        );
    }

    public function vendorBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching vendor by slug successfully');

        $searchBy = 'true-brush';

        $I->sendGQLQuery('query {
                vendor (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['vendor']['id']);
        $vendorId = $result['data']['vendor']['id'];

        $I->sendGQLQuery('query {
                vendor (
                   vendorId: "' . $vendorId . '"
                ) {
                 seo {
                     slug
                 }
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        //fetch vendor by id and compare the slug
        $I->assertStringContainsString(
            strtolower($searchBy),
            $result['data']['vendor']['seo']['slug']
        );
    }

    public function vendorBySeoSlugByLanguage(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching vendor by slug by language successfully');

        $searchBy = 'fake-en';

        $I->sendGQLQuery('query {
                vendor (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }', null, 1);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        //vendor found by english slug for lang = 1
        $I->assertNotEmpty($result['data']['vendor']['id']);

        //query default language
        $I->sendGQLQuery('query {
                vendor (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            VendorNotFound::bySlug($searchBy),
            $result['errors'][0]['message']
        );
    }

    private function prepareTestData(AcceptanceTester $I): void
    {
        $vendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
        $vendor->setId('_testvendor1');
        $vendor->assign(
            [
                'oxid'        => '_testvendor',
                'oxactive'    => 1,
                'oxtitle'     => 'true brush',
                'oxshortdesc' => 'Original Retro Electronics',
            ]
        );
        $vendor->save();
        $vendor->loadInLang(1, '_testvendor');
        $vendor->assign(
            [
                'oxtitle'     => 'brush en',
                'oxshortdesc' => 'Original Retro Electronics EN',
            ]
        );
        $vendor->save();

        $vendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
        $vendor->setId('_testvendor2');
        $vendor->assign(
            [
                'oxid'        => '_testvendor2',
                'oxactive'    => 1,
                'oxtitle'     => 'fake brush',
                'oxshortdesc' => 'Faked Retro Electronics',
            ]
        );
        $vendor->save();
        $vendor->loadInLang(1, '_testvendor2');
        $vendor->assign(
            [
                'oxtitle'     => 'fake en',
                'oxshortdesc' => 'Faked Retro Electronics EN',
            ]
        );
        $vendor->save();

        $I->seeInDatabase('oxvendor', ['oxid' => '_testvendor']);

        //this query ensures all seo urls are generated
        $I->sendGQLQuery('query {
                vendors {
                    title
                    id
                    seo {
                        url
                    }
               }
        }');

        $I->sendGQLQuery('query {
                vendors {
                    title
                    id
                    seo {
                        url
                    }
               }
        }', null, 1);

        $I->updateInDatabase('oxseo', ['oxseourl' => 'Nach-Lieferant/true-brush/'], ['oxseourl' => 'Nach-was-Anderem/fake-brush/']);
    }
}
