<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\DataType;

use OxidEsales\GraphQL\Storefront\Shared\DataType\Seo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\OxidEsales\GraphQL\Storefront\Shared\DataType\Seo::class)]
final class SeoTest extends TestCase
{
    public function testNoSeoUrl(): void
    {
        $seo = new Seo(
            new NoEshopUrlContractModelStub()
        );

        $this->assertNull(
            $seo->getUrl()
        );
    }
}
