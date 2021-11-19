<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Content;

use Codeception\Scenario;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\GraphQL\Storefront\Content\Exception\ContentNotFound;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group content
 * @group oe_graphql_storefront
 */
final class ContentCest extends BaseCest
{
    private const CONTENT_WITH_TEMPLATE  = '4d4106027b63b623b2c4ee1ea6838d7f';

    private const CONTENT_WITH_VCMS_TEMPLATE  = '9f825347decfdb7008d162700be95dc1';

    public function _after(AcceptanceTester  $I): void
    {
        $I->updateInDatabase('oxseo', ['oxseourl' => 'Benutzer-geblockt/'], ['oxseourl' => 'Nach-was-Anderem/Wie-bestellen/']);

        parent::_after($I);
    }

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        //this query ensures all seo urls are generated
        $I->sendGQLQuery('query {
                contents {
                    title
                    id
                    seo {
                        url
                    }
               }
        }');

        $I->sendGQLQuery('query {
                contents {
                    title
                    id
                    seo {
                        url
                    }
               }
        }', null, 1);
    }

    public function contentWithTemplate(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {
            content (contentId: "' . self::CONTENT_WITH_TEMPLATE . '") {
                id
                content
                rawContent
            }
        }');

        $I->seeResponseIsJson();
        $result  = $I->grabJsonResponseAsArray();
        $content = $result['data']['content'];

        $I->assertEquals(
            [
                'id'            => self::CONTENT_WITH_TEMPLATE,
                'content'       => 'GraphQL rendered content DE',
                'rawContent'    => 'GraphQL [{if true }]rendered [{/if}]content DE',
            ],
            $content
        );
    }

    public function contentWithVCMS(AcceptanceTester $I): void
    {
        if (!$this->isVCMSActive()) {
            $I->markTestSkipped('VCMS module is not active');
        }

        $I->sendGQLQuery('query {
            content (contentId: "' . self::CONTENT_WITH_VCMS_TEMPLATE . '") {
                id
                content
            }
        }');

        $I->seeResponseIsJson();
        $result  = $I->grabJsonResponseAsArray();
        $content = $result['data']['content'];

        $expectedRenderedContent =
            '<div class="container-fluid dd-ve-container clearfix">' .
                '<div class="row">' .
                    '<div class="col-sm-12">' .
                        '<div class="dd-shortcode-text">GraphQL VCMS rendered content DE</div>' .
                    '</div>' .
                '</div>' .
            '</div>';

        $I->assertEquals(
            [
                'id'            => self::CONTENT_WITH_VCMS_TEMPLATE,
                'content'       => $expectedRenderedContent,
            ],
            $content
        );
    }

    public function contentsBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('filtering contents by parts of seo slug');

        $I->sendGQLQuery('query {
                contents (
                    filter: {
                        slug: {
                            like: "descr"
                        }
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

        $I->assertEquals('meta-description-startseite', $result['data']['contents'][0]['seo']['slug']);
        $I->assertEquals('registration-description', $result['data']['contents'][1]['seo']['slug']);
    }

    public function contentsBySeoSlugInvalidParameterIdAndSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching contents by slug and id fails');

        $I->sendGQLQuery('query {
                content (
                    contentId: "some_id"
                    slug: "some_slug"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            ContentNotFound::byParameter(),
            $result['errors'][0]['message']
        );
    }

    public function contentsBySeoSlugAmbiguous(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching contents by slug which is not unique');

        $I->updateInDatabase('oxseo', ['oxseourl' => 'Nach-was-Anderem/Wie-bestellen/'], ['oxseourl' => 'Benutzer-geblockt/']);

        $I->sendGQLQuery('query {
                content (
                    slug: "wie-bestellen"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            ContentNotFound::byAmbiguousBySlug('wie-bestellen'),
            $result['errors'][0]['message']
        );
    }

    public function contentsNotFoundBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching contents by slug which cannot be found');

        $I->sendGQLQuery('query {
                content (
                    slug: "this-is---nonexisting----slug"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            ContentNotFound::bySlug('this-is---nonexisting----slug'),
            $result['errors'][0]['message']
        );
    }

    public function contentBySeoSlug(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching contents by slug successfully');

        $searchBy = 'wie-bestellen';

        $I->sendGQLQuery('query {
                content (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['content']['id']);
        $contentsId = $result['data']['content']['id'];

        $I->sendGQLQuery('query {
                content (
                   contentId: "' . $contentsId . '"
                ) {
                 seo {
                     slug
                 }
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        //fetch contents by id and compare the slug
        $I->assertStringContainsString(
            strtolower($searchBy),
            $result['data']['content']['seo']['slug']
        );
    }

    public function contentBySeoSlugByLanguage(AcceptanceTester $I): void
    {
        $I->wantToTest('fetching contents by slug successfully');

        $searchBy = 'how-to-order';

        $I->sendGQLQuery('query {
                content (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }', null, 1);

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        //contents found by english slug for lang = 1
        $I->assertNotEmpty($result['data']['content']['id']);

        //query default language
        $I->sendGQLQuery('query {
                content (
                    slug: "' . $searchBy . '"
                ) {
                id
               }
        }');

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            ContentNotFound::bySlug($searchBy),
            $result['errors'][0]['message']
        );
    }

    private function isVCMSActive(): bool
    {
        $moduleActivation = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleActivationBridgeInterface::class);

        return (bool) $moduleActivation->isActive('ddoevisualcms', 1);
    }
}
