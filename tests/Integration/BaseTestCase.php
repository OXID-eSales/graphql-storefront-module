<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\Tests\Integration\TokenTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

abstract class BaseTestCase extends TokenTestCase
{
    use DemoData;

    public const MANUFACTURER_MEDIA_PATH = 'out/pictures/master/manufacturer';

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

    protected function copyAssets(): void
    {
        $shopAssetDir = Path::join(
            Registry::getConfig()->getConfigParam('sShopDir'),
            self::MANUFACTURER_MEDIA_PATH
        );

        $fixtureAssetDir = Path::join(
            __DIR__,
            '../Fixtures',
            self::MANUFACTURER_MEDIA_PATH
        );

        $fileSystem = new Filesystem();
        $fileSystem->mirror($fixtureAssetDir, $shopAssetDir);
    }
}
