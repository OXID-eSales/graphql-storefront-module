<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Exception;

use OxidEsales\GraphQL\Storefront\Manufacturer\Exception\ManufacturerNotFound;
use PHPUnit\Framework\TestCase;

/**
 * @covers OxidEsales\GraphQL\Storefront\Manufacturer\Exception\ManufacturerNotFound
 */
final class ManufacturerNotFoundTest extends TestCase
{
    public function testExceptionById(): void
    {
        $this->expectException(ManufacturerNotFound::class);
        $this->expectExceptionMessage('MANUID');

        throw ManufacturerNotFound::byId('MANUID');
    }
}
