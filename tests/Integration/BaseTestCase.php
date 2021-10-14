<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration;

use OxidEsales\GraphQL\Base\Tests\Integration\TokenTestCase;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

abstract class BaseTestCase extends TokenTestCase
{
    protected function doAssertArraySubset($needle, $haystack): void
    {
        if (method_exists($this, 'assertArraySubsetOxid')) {
            parent::assertArraySubsetOxid($needle, $haystack);
        } else {
            parent::assertArraySubset($needle, $haystack);
        }
    }

    protected function doAssertContains($needle, $haystack, $message = ''): void
    {
        if (method_exists($this, 'assertStringContainsString')) {
            parent::assertStringContainsString($needle, $haystack, $message);
        } else {
            parent::assertContains($needle, $haystack, $message);
        }
    }

    protected function setActiveState(string $id, string $table = 'oxarticles', int $active = 1): void
    {
        $queryBuilderFactory = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class);
        $queryBuilder = $queryBuilderFactory->create();

        // set product to inactive
        $queryBuilder
            ->update($table)
            ->set('oxactive', $active)
            ->where('OXID = :OXID')
            ->setParameter(':OXID', $id)
            ->execute();
    }
}
