<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Category\DataType;

use OxidEsales\GraphQL\Storefront\Category\DataType\CategoryIDFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\GraphQLite\Types\ID;

#[CoversClass(\OxidEsales\GraphQL\Storefront\Category\DataType\CategoryIDFilter::class)]
final class CategoryIDFilterTest extends TestCase
{
    public function testMatches(): void
    {
        $categoryId = uniqid();
        $sut = new CategoryIDFilter(new ID($categoryId));

        $this->assertTrue($sut->matches($categoryId));
        $this->assertFalse($sut->matches(uniqid('false')));
        $this->assertFalse($sut->matches(123));
        $this->assertFalse($sut->matches(true));
        $this->assertFalse($sut->matches([]));
        $this->assertFalse($sut->matches(null));
    }

    public function testEquals(): void
    {
        $categoryId = uniqid();
        $sut = new CategoryIDFilter(new ID($categoryId));

        $this->assertEquals(new ID($categoryId), $sut->equals());
    }

    public function testFromUserInput(): void
    {
        $categoryId = uniqid();
        $sut = CategoryIDFilter::fromUserInput(new ID($categoryId));

        $this->assertEquals(new CategoryIDFilter(new ID($categoryId)), $sut);
    }
}
