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

    const CATEGORY_ID = '2b6711be251807f1bd0fa5b9e03398e2';
    const CATEGORY_WITHOUT_ATTRIBUTE_ID = '6f265749cbd4cd26f5d358b09a5e862b';
    const ATTRIBUTE_CONDITION_ID = 'attribute_condition';

    public function testCategoryAttributeDataType(): void
    {
        $category = oxNew(Category::class);
        $category->load(self::CATEGORY_ID);
        $attributes = $category->getAttributes();
        $someAttribute = $attributes->current();

        $categoryAttributeDatatype = new CategoryAttribute($someAttribute);

        $this->assertInstanceOf(AttributeDataType::class, $categoryAttributeDatatype->getAttribute());
        $this->assertEquals($someAttribute, $categoryAttributeDatatype->getAttribute()->getEshopModel());
        $this->assertEquals($someAttribute->getValues(), $categoryAttributeDatatype->getValues());
        $this->assertInstanceOf(Attribute::class, $categoryAttributeDatatype->getEshopModel());
    }

    public function testGetAttributesForCategory(): void
    {
        $category = oxNew(Category::class);
        $category->load(self::CATEGORY_ID);

        $categoryDataType = new CategoryDataType($category);

        $categoryInfrastructure = oxNew(CategoryInfrastructure::class);
        $attributes = $categoryInfrastructure->getAttributes($categoryDataType);
        /** @var CategoryAttribute $attribute */
        $attribute = current($attributes);

        $this->assertCount(3, $attributes);
        //Check why the other category2object relations are missing
        $this->assertEquals('Material', $attribute->getAttribute()->getTitle());
    }

    public function testGetAttributesForCategoryWithoutAttributes(): void
    {
        $category = oxNew(Category::class);
        $category->load(self::CATEGORY_WITHOUT_ATTRIBUTE_ID);

        $categoryDataType = new CategoryDataType($category);

        $categoryInfrastructure = oxNew(CategoryInfrastructure::class);
        $attributes = $categoryInfrastructure->getAttributes($categoryDataType);

        $this->assertEquals([], $attributes);
    }
}







