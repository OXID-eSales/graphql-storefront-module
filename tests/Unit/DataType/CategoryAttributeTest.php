<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\DataType;

use OxidEsales\Eshop\Application\Model\Attribute as EshopAttributeModel;
use OxidEsales\GraphQL\Storefront\Attribute\DataType\Attribute;
use OxidEsales\GraphQL\Storefront\Category\DataType\CategoryAttribute;
use PHPUnit\Framework\TestCase;

final class CategoryAttributeTest extends TestCase
{
    public function testGetEshopModel(): void
    {
        $eshopAttributeMock = $this->createMock(EshopAttributeModel::class);
        $categoryAttribute = new CategoryAttribute($eshopAttributeMock);

        $this->assertEquals($eshopAttributeMock, $categoryAttribute->getEshopModel());
    }

    public function testGetAttribute(): void
    {
        $eshopAttributeMock = $this->createMock(EshopAttributeModel::class);
        $categoryAttribute = new CategoryAttribute($eshopAttributeMock);

        $attribute = $categoryAttribute->getAttribute();
        $this->assertEquals($eshopAttributeMock, $attribute->getEshopModel());
        $this->assertInstanceOf(Attribute::class, $attribute);
    }

    public function testGetValues(): void
    {
        $eshopAttributeMock = $this->createMock(EshopAttributeModel::class);
        $eshopAttributeMock->expects($this->once())
            ->method('getValues')
            ->willReturn(['Value1', 'Value2']);

        $categoryAttribute = new CategoryAttribute($eshopAttributeMock);

        $values = $categoryAttribute->getValues();

        $this->assertEquals(['Value1', 'Value2'], $values);
    }

    public function testGetModelClass(): void
    {
        $modelClass = CategoryAttribute::getModelClass();

        $this->assertEquals(EshopAttributeModel::class, $modelClass);
    }
}
