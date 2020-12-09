<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Unit\Exception;

use OxidEsales\GraphQL\Catalogue\Category\Exception\CategoryNotFound;
use PHPUnit\Framework\TestCase;

/**
 * @covers OxidEsales\GraphQL\Catalogue\Category\Exception\CategoryNotFound
 */
final class CategoryNotFoundTest extends TestCase
{
    public function testExceptionById(): void
    {
        $this->expectException(CategoryNotFound::class);
        $this->expectExceptionMessage('CATID');

        throw CategoryNotFound::byId('CATID');
    }
}
