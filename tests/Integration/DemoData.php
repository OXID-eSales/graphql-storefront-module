<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\Edition;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\EditionDirectoriesLocator;

trait DemoData
{
    public function setUp(): void
    {
        $connection = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class)
            ->create()
            ->getConnection();

        $path = __DIR__ . '/../Fixtures/integrationtest_ce.sql';

        if ((new EditionDirectoriesLocator())->getEditionRootPath(Edition::Enterprise)){
            $path = __DIR__ . '/../Fixtures/integrationtest_ee.sql';
        }

        $connection->executeStatement(
            file_get_contents($path)
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
