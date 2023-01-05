<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Shared\Infrastructure;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupValidationException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\BeforeModuleDeactivationEvent;
use OxidEsales\GraphQL\Storefront\Shared\Subscriber\BeforeModuleDeactivation;

final class BeforeModuleDeactivationTest extends TestCase
{
    public function testCannotDeactivateBaseForActiveStorefront(): void
    {
        $container = ContainerFactory::getInstance()
            ->getContainer();

        $shopId = $container
            ->get(ContextInterface::class)
            ->getCurrentShopId();
        $event = new BeforeModuleDeactivationEvent($shopId, 'oe_graphql_base');

        $handler = $container->get(BeforeModuleDeactivation::class);

        $this->expectException(ModuleSetupValidationException::class);

        $handler->handle($event);
    }

    public function testDeactivateIndependentOtherModule(): void
    {
        $container = ContainerFactory::getInstance()
            ->getContainer();

        $shopId = $container
            ->get(ContextInterface::class)
            ->getCurrentShopId();
        $event = new BeforeModuleDeactivationEvent($shopId, 'oe_graphql_other');

        $handler = $container->get(BeforeModuleDeactivation::class);

        $this->assertSame($event, $handler->handle($event));
    }
}
