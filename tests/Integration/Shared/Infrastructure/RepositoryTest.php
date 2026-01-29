<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Shared\Infrastructure;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Storefront\Category\DataType\Category as CategoryDataType;
use OxidEsales\GraphQL\Storefront\Category\DataType\CategoryFilterList;
use OxidEsales\GraphQL\Storefront\Category\DataType\Sorting;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\ListConfiguration;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface;
use PHPUnit\Framework\TestCase;

final class RepositoryTest extends TestCase
{
    public function testRepositoryDoesNotLoadListObjectsByDefault(): void
    {
        $repository = ContainerFactory::getInstance()
            ->getContainer()
            ->get(RepositoryInterface::class);

        $list = $repository->getList(
            CategoryDataType::class,
            new CategoryFilterList(),
            new PaginationFilter(),
            new Sorting([])
        );

        foreach ($list as $categoryDataType) {
            $cat = $categoryDataType->getEshopModel();
            $this->assertFalse($cat->isLoaded());
            $this->assertNotEmpty($cat->getId());
            $this->assertFalse($cat->isLoaded());
        }
    }

    public function testRepositoryCanLoadListObjects(): void
    {
        $queryBuilder = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class);

        $listConfiguration = new ListConfiguration(['oxcategories' => 'oxcategories']);

        $repository = new Repository($queryBuilder, $listConfiguration);

        $list = $repository->getList(
            CategoryDataType::class,
            new CategoryFilterList(),
            new PaginationFilter(),
            new Sorting([])
        );

        foreach ($list as $categoryDataType) {
            $cat = $categoryDataType->getEshopModel();
            $this->assertTrue($cat->isLoaded());
            $this->assertNotEmpty($cat->getId());
        }
    }
}
