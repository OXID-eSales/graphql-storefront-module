<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Exception;

use OxidEsales\GraphQL\Storefront\Vendor\Exception\VendorNotFound;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\OxidEsales\GraphQL\Storefront\Vendor\Exception\VendorNotFound::class)]
final class VendorNotFoundTest extends TestCase
{
    public function testExceptionById(): void
    {
        $this->expectException(VendorNotFound::class);
        $this->expectExceptionMessage('VENDORID');

        throw new VendorNotFound('VENDORID');
    }
}
