<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Subscriber;

use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\BeforeModuleDeactivationEvent as OriginalEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\GraphQL\Storefront\Shared\Service\ModuleDependencyService;

final class ModuleDeactivation extends AbstractShopAwareEventSubscriber
{
    /** @var ModuleDependencyService */
    private $dependencies;

    public function __construct(ModuleDependencyService $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    public function handle(OriginalEvent $event): OriginalEvent
    {
        if (array_key_exists($event->getModuleId(), $this->dependencies->getDependencies())) {
            throw new ModuleSetupException(
                'Module with id "' . $event->getModuleId() .
                '" cannot be deactivated while GraphQL Storefront module is active.'
            );
        }

        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            'OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\BeforeModuleDeactivationEvent' => 'handle',
        ];
    }
}
