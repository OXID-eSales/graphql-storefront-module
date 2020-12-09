<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Unit\DataType;

use OxidEsales\GraphQL\Catalogue\Shared\DataType\Seo;
use PHPUnit\Framework\TestCase;

/**
 * @covers OxidEsales\GraphQL\Catalogue\Shared\DataType\Seo
 */
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
