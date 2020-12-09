<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\DataType;

use DateTimeImmutable;
use OxidEsales\GraphQL\Storefront\Category\DataType\Category;
use PHPUnit\Framework\TestCase;

/**
 * @covers OxidEsales\GraphQL\Storefront\Category\DataType\Category
 */
final class CategoryTest extends TestCase
{
    public function testIsActive(): void
    {
        $category = new Category(
            new CategoryStub()
        );
        $this->assertTrue(
            $category->isActive()
        );

        $category = new Category(
            new CategoryStub('0')
        );
        $this->assertFalse(
            $category->isActive()
        );

        $category = new Category(
            new CategoryStub(
                '1',
                '2018-01-01 12:00:00',
                '2018-01-01 19:00:00'
            )
        );
        $this->assertTrue(
            $category->isActive()
        );

        $category = new Category(
            new CategoryStub(
                '0',
                '2018-01-01 12:00:00',
                '2018-01-01 19:00:00'
            )
        );
        $this->assertFalse(
            $category->isActive()
        );

        $category = new Category(
            new CategoryStub(
                '0',
                '2018-01-01 12:00:00',
                '2018-01-01 19:00:00'
            )
        );
        $this->assertTrue(
            $category->isActive(new DateTimeImmutable('2018-01-01 16:00:00'))
        );
    }
}
