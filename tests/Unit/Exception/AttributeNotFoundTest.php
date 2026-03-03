<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Exception;

use OxidEsales\GraphQL\Storefront\Attribute\Exception\AttributeNotFound;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\OxidEsales\GraphQL\Storefront\Attribute\Exception\AttributeNotFound::class)]
final class AttributeNotFoundTest extends TestCase
{
    public function testExceptionById(): void
    {
        $this->expectException(AttributeNotFound::class);
        $this->expectExceptionMessage('ATTRID');

        throw new AttributeNotFound('ATTRID');
    }
}
