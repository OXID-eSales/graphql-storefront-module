<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Attribute;

use Codeception\Example;
use OxidEsales\Eshop\Core\Element2ShopRelations;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group attribute
 * @group other
 * @group oe_graphql_storefront
 */
final class AttributeMultishopCest extends MultishopBaseCest
{
    private const ATTRIBUTE_ID = '6cf89d2d73e666457d167cebfc3eb492';

    public function testGetSingleNotInShopAttributeWillFail(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                attribute (attributeId: "' . self::ATTRIBUTE_ID . '") {
                    title
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseIsJson();

        $response = $I->grabJsonResponseAsArray();

        $I->assertSame(
            'Attribute was not found by id: ' . self::ATTRIBUTE_ID,
            $response['errors'][0]['message']
        );
    }

    public function testGetSingleInShopAttributeWillWork(AcceptanceTester $I): void
    {
        $this->addAttributeToShops([2]);

        $I->sendGQLQuery(
            'query {
                attribute (attributeId: "' . self::ATTRIBUTE_ID . '") {
                    title
                }
            }'
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'title' => 'Lieferumfang',
            ],
            $response['data']['attribute']
        );

        $this->removeAttributeFromShops([2]);
    }

    protected function dataProviderGetAttributeMultilanguage(): array
    {
        return [
            'shop_1_de' => [
                'shopId' => 1,
                'languageId' => 0,
                'title' => 'Lieferumfang',
            ],
            'shop_1_en' => [
                'shopId' => 1,
                'languageId' => 1,
                'title' => 'Included in delivery',
            ],
            'shop_2_de' => [
                'shopId' => 2,
                'languageId' => 0,
                'title' => 'Lieferumfang',
            ],
            'shop_2_en' => [
                'shopId' => 2,
                'languageId' => 1,
                'title' => 'Included in delivery',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderGetAttributeMultilanguage
     */
    public function testGetSingleTranslatedSecondShopAttribute(AcceptanceTester $I, Example $data): void
    {
        $this->addAttributeToShops([2]);

        $I->sendGQLQuery(
            'query {
                attribute (attributeId: "' . self::ATTRIBUTE_ID . '") {
                    title
                }
            }',
            null,
            $data['languageId'],
            $data['shopId']
        );

        $this->removeAttributeFromShops([2]);

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            [
                'title' => $data['title'],
            ],
            $response['data']['attribute']
        );
    }

    protected function dataProviderGetAttributeMultishop(): array
    {
        return [
            'shop_1_de' => [
                'shopId' => 1,
                'languageId' => 0,
                'attributes' => [
                    'EU-Größe',
                    'Washing',
                    'Lieferumfang',
                ],
            ],
            'shop_1_en' => [
                'shopId' => 1,
                'languageId' => 1,
                'attributes' => [
                    'EU-Size',
                    'Washing',
                    'Included in delivery',
                ],
            ],
            'shop_2_de' => [
                'shopId' => 2,
                'languageId' => 0,
                'attributes' => [
                    'EU-Größe',
                    'Washing',
                    'Lieferumfang',
                ],
            ],
            'shop_2_en' => [
                'shopId' => 2,
                'languageId' => 1,
                'attributes' => [
                    'EU-Size',
                    'Washing',
                    'Included in delivery',
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderGetAttributeMultishop
     *
     * @param string $languageId
     * @param array $attributes
     */
    public function testAttributeListMultishop(AcceptanceTester $I, Example $data): void
    {
        $this->addAttributesToShops([2]);

        $I->sendGQLQuery(
            'query {
                attributes {
                    title
                }
            }',
            null,
            $data['languageId'],
            $data['shopId']
        );

        $I->seeResponseIsJson();
        $response = $I->grabJsonResponseAsArray();

        foreach ($data['attributes'] as $key => $attribute) {
            $I->assertSame(
                $attribute,
                $response['data']['attributes'][$key]['title']
            );
        }

        $this->removeAttributeFromShops([2]);
    }

    /**
     * @param int[] $shops
     */
    private function addAttributeToShops(array $shops): void
    {
        $oElement2ShopRelations = oxNew(Element2ShopRelations::class, 'oxattribute');
        $oElement2ShopRelations->setShopIds($shops);
        $oElement2ShopRelations->addToShop(self::ATTRIBUTE_ID);
    }

    /**
     * @param int[] $shops
     */
    private function removeAttributeFromShops(array $shops): void
    {
        $oElement2ShopRelations = oxNew(Element2ShopRelations::class, 'oxattribute');
        $oElement2ShopRelations->setShopIds($shops);
        $oElement2ShopRelations->removeFromShop(self::ATTRIBUTE_ID);
    }

    /**
     * @param int[] $shops
     */
    private function addAttributesToShops(array $shops): void
    {
        $attributes = [
            '6b6bc9f9ab8b153d9bebc2ad6ca2aa13',
            '6b6e77de7a04de54f1aa63cfeca2f487',
            '6cf89d2d73e666457d167cebfc3eb492',
        ];

        $oElement2ShopRelations = oxNew(Element2ShopRelations::class, 'oxattribute');
        $oElement2ShopRelations->setShopIds($shops);

        foreach ($attributes as $attribute) {
            $oElement2ShopRelations->addToShop($attribute);
        }
    }
}
