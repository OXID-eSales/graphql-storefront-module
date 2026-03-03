<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Exception;

use OxidEsales\GraphQL\Storefront\Category\Exception\CategoryNotFound;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\OxidEsales\GraphQL\Storefront\Category\Exception\CategoryNotFound::class)]
final class CategoryNotFoundTest extends TestCase
{
    public function testExceptionById(): void
    {
        $this->expectException(CategoryNotFound::class);
        $this->expectExceptionMessage('CATID');

        throw new CategoryNotFound('CATID');
    }
}
