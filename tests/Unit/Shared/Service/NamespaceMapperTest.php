<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Shared\Service;

use OxidEsales\GraphQL\Storefront\Shared\Service\NamespaceMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\OxidEsales\GraphQL\Storefront\Shared\Service\NamespaceMapper::class)]
final class NamespaceMapperTest extends TestCase
{
    public function testNamespaceCounts(): void
    {
        $namespaceMapper = new NamespaceMapper();
        $this->assertCount(
            20,
            $namespaceMapper->getControllerNamespaceMapping()
        );
        $this->assertCount(
            45,
            $namespaceMapper->getTypeNamespaceMapping()
        );
    }
}
