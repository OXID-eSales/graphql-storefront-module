<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Content;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group content
 * @group oe_graphql_storefront
 * @group other
 */
final class ContentCest extends BaseCest
{
    private const CONTENT_WITH_TEMPLATE = '4d4106027b63b623b2c4ee1ea6838d7f';

    private const CONTENT_WITH_VCMS_TEMPLATE = '9f825347decfdb7008d162700be95dc1';

    public function contentWithTemplate(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
            content (contentId: "' . self::CONTENT_WITH_TEMPLATE . '") {
                id
                content
                rawContent
            }
        }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $content = $result['data']['content'];

        $I->assertEquals(
            [
                'id' => self::CONTENT_WITH_TEMPLATE,
                'content' => 'GraphQL rendered content DE',
                'rawContent' => 'GraphQL [{if true }]rendered [{/if}]content DE',
            ],
            $content
        );
    }

    public function contentWithVCMS(AcceptanceTester $I): void
    {
        if (!$this->isVCMSActive()) {
            $I->markTestSkipped('VCMS module is not active');
        }

        $I->sendGQLQuery(
            'query {
            content (contentId: "' . self::CONTENT_WITH_VCMS_TEMPLATE . '") {
                id
                content
            }
        }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
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
                'id' => self::CONTENT_WITH_VCMS_TEMPLATE,
                'content' => $expectedRenderedContent,
            ],
            $content
        );
    }

    private function isVCMSActive(): bool
    {
        $moduleActivation = ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleActivationBridgeInterface::class);

        return (bool)$moduleActivation->isActive('ddoevisualcms', 1);
    }
}
