<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\DataType;

use OxidEsales\Eshop\Application\Model\Attribute;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\GraphQL\Storefront\Attribute\DataType\Attribute as AttributeDataType;
use OxidEsales\GraphQL\Storefront\Category\DataType\Category as CategoryDataType;
use OxidEsales\GraphQL\Storefront\Category\DataType\CategoryAttribute;
use OxidEsales\GraphQL\Storefront\Category\Infrastructure\Category as CategoryInfrastructure;
use OxidEsales\GraphQL\Storefront\Tests\Integration\DemoData;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OxidEsales\GraphQL\Storefront\Category\DataType\CategoryAttribute
 */
final class CategoryAttributeTest extends TestCase
{
    use DemoData;

    private const CATEGORY_ID = '_pro_articles';
    private const CATEGORY_WITHOUT_ATTRIBUTES_ID = '_pants';

    public function testGetAttributesForCategory(): void
    {
        $category = oxNew(Category::class);
        $category->load(self::CATEGORY_ID);

        $categoryDataType = new CategoryDataType($category);

        $categoryInfrastructure = oxNew(CategoryInfrastructure::class);
        $attributes = $categoryInfrastructure->getAttributes($categoryDataType);
        $attributeOne = $attributes['_test_attribute_one']->getAttribute();
        $attributeTwo = $attributes['_test_attribute_two']->getAttribute();

        $this->assertCount(2, $attributes);
        $this->assertArrayHasKey('_test_attribute_one', $attributes);
        $this->assertArrayHasKey('_test_attribute_two', $attributes);
        $this->assertEquals('Cooles Attribute', $attributeOne->getTitle());
        $this->assertEquals('Noch Ein Cooles Attribute', $attributeTwo->getTitle());
    }

    public function testGetAttributesForCategoryWithoutAttributes(): void
    {
        $category = oxNew(Category::class);
        $category->load(self::CATEGORY_WITHOUT_ATTRIBUTES_ID);

        $categoryDataType = new CategoryDataType($category);

        $categoryInfrastructure = oxNew(CategoryInfrastructure::class);
        $attributes = $categoryInfrastructure->getAttributes($categoryDataType);

        $this->assertEquals([], $attributes);
    }
}
