<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Subscriber;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\BeforeModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\BeforeModuleDeactivationEvent as OriginalEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class BeforeModuleDeactivation implements EventSubscriberInterface
{
    private array $dependencies;

    public function __construct(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    public function handle(OriginalEvent $event): OriginalEvent
    {
        if (in_array($event->getModuleId(), $this->dependencies)) {
            throw new ModuleSetupValidationException(
                'Module with id "' . $event->getModuleId() .
                '" cannot be deactivated while GraphQL Storefront module is active.'
            );
        }

        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeModuleDeactivationEvent::class => 'handle',
        ];
    }
}
