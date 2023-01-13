<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\Facts\Facts;

trait DemoData
{
    public function setUp(): void
    {
        $facts = new Facts();

        $connection = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class)
            ->create()
            ->getConnection();

        $connection->executeStatement(
            file_get_contents(
                __DIR__ . '/../Fixtures/integrationtest_' . strtolower($facts->getEdition()) . '.sql'
            )
        );

        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $connection = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class)
            ->create()
            ->getConnection();

        $connection->executeStatement(
            file_get_contents(
                __DIR__ . '/../Fixtures/remove_subshop.sql'
            )
        );
    }
}
