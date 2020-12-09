<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Unit\Shared\Service;

use OxidEsales\GraphQL\Catalogue\Shared\Service\NamespaceMapper;
use PHPUnit\Framework\TestCase;

final class NamespaceMapperTest extends TestCase
{
    /**
     * @covers OxidEsales\GraphQL\Catalogue\Shared\Service\NamespaceMapper
     */
    public function testNamespaceCounts(): void
    {
        $namespaceMapper = new NamespaceMapper();
        $this->assertCount(
            13,
            $namespaceMapper->getControllerNamespaceMapping()
        );
        $this->assertCount(
            25,
            $namespaceMapper->getTypeNamespaceMapping()
        );
    }
}
