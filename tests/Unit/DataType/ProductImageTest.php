<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\DataType;

use OxidEsales\GraphQL\Storefront\Product\DataType\ProductImage;
use PHPUnit\Framework\TestCase;

/**
 * @covers OxidEsales\GraphQL\Storefront\Product\DataType\ProductImage
 */
final class ProductImageTest extends TestCase
{
    public function testProductImage(): void
    {
        $imageValue = 'image value';
        $iconValue  = 'icon value';
        $zoomValue  = 'zoom value';

        $productImage = new ProductImage($imageValue, $iconValue, $zoomValue);

        $this->assertSame(
            $imageValue,
            $productImage->getImage()
        );
        $this->assertSame(
            $iconValue,
            $productImage->getIcon()
        );
        $this->assertSame(
            $zoomValue,
            $productImage->getZoom()
        );
    }
}
