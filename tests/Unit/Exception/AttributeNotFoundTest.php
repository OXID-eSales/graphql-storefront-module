<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Unit\Exception;

use OxidEsales\GraphQL\Catalogue\Attribute\Exception\AttributeNotFound;
use PHPUnit\Framework\TestCase;

/**
 * @covers OxidEsales\GraphQL\Catalogue\Attribute\Exception\AttributeNotFound
 */
final class AttributeNotFoundTest extends TestCase
{
    public function testExceptionById(): void
    {
        $this->expectException(AttributeNotFound::class);
        $this->expectExceptionMessage('ATTRID');

        throw AttributeNotFound::byId('ATTRID');
    }
}
