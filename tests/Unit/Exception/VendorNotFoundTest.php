<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Exception;

use OxidEsales\GraphQL\Storefront\Vendor\Exception\VendorNotFound;
use PHPUnit\Framework\TestCase;

/**
 * @covers OxidEsales\GraphQL\Storefront\Vendor\Exception\VendorNotFound
 */
final class VendorNotFoundTest extends TestCase
{
    public function testExceptionById(): void
    {
        $this->expectException(VendorNotFound::class);
        $this->expectExceptionMessage('VENDORID');

        throw VendorNotFound::byId('VENDORID');
    }
}
