<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Unit\DataType;

use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductScalePrice;
use PHPUnit\Framework\TestCase;

/**
 * @covers OxidEsales\GraphQL\Catalogue\Product\DataType\ProductScalePrice
 */
final class ProductScalePriceTest extends TestCase
{
    public function testAbsoluteScalePrice(): void
    {
        $productScalePrice = new ProductScalePrice(
            new ProductScalePriceModelStub(
                '10.5',
                '',
                '10',
                '19'
            )
        );

        $this->assertTrue(
            $productScalePrice->isAbsoluteScalePrice()
        );
        $this->assertSame(
            10.5,
            $productScalePrice->getAbsolutePrice()
        );
        $this->assertNull(
            $productScalePrice->getDiscount()
        );
        $this->assertSame(
            10,
            $productScalePrice->getAmountFrom()
        );
        $this->assertSame(
            19,
            $productScalePrice->getAmountTo()
        );
    }

    public function testDiscountedScalePrice(): void
    {
        $productScalePrice = new ProductScalePrice(
            new ProductScalePriceModelStub(
                '',
                '10.5',
                '10',
                '19'
            )
        );

        $this->assertFalse(
            $productScalePrice->isAbsoluteScalePrice()
        );
        $this->assertNull(
            $productScalePrice->getAbsolutePrice()
        );
        $this->assertSame(
            10.5,
            $productScalePrice->getDiscount()
        );
        $this->assertSame(
            10,
            $productScalePrice->getAmountFrom()
        );
        $this->assertSame(
            19,
            $productScalePrice->getAmountTo()
        );
    }
}
