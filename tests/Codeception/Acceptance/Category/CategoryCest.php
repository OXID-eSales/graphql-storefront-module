<?php

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Category;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group category
 * @group oe_graphql_storefront
 * @group other
 */
class CategoryCest extends BaseCest
{
    public function testGetCategoryAttributes(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                categories(filter:{
                    title:{
                        equals: "Kites"
                    }
                }) {
                    id
                    title
                    attributes {
                        attribute{
                            title
                        }
                        values
                    }
                }
            }'
        );
        $expectAttributes = [
            ['attribute' => ['title' => 'Design'], 'values' => ['Modern']],
            ['attribute' => ['title' => 'Farbe'], 'values' => ['Blau', 'Grün']]
        ];

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $I->assertCount(1, $result['data']['categories']);
        $categorieResult = $result['data']['categories'][0];
        $I->assertEquals($expectAttributes, $categorieResult['attributes']);
    }

    public function testGetInvalidCategory(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query {
                categories(filter:{
                    title:{
                        equals: "InvalidCategory"
                    }
                }) {
                    id
                    title
                    attributes {
                        attribute{
                            title
                        }
                        values
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();
        $I->assertIsEmpty($result['data']['categories']);
    }
}
