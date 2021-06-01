<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Subscriber;

use OxidEsales\EshopCommunity\Internal\Framework\Event\AbstractShopAwareEventSubscriber;
use OxidEsales\GraphQL\Storefront\Basket\Event\BasketAuthorization as OriginalEvent;

class BasketAuthorization extends AbstractShopAwareEventSubscriber
{
    public function handleAuthorization(OriginalEvent $event): OriginalEvent
    {
        $basket = $event->getBasket();
        $userId = $event->getCustomerId();

        if ($basket->getUserId()->val() === $userId->val()) {
            $event->setAuthorized(true);
        }

        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            'OxidEsales\GraphQL\Storefront\Basket\Event\BasketAuthorization' => 'handleAuthorization'
        ];
    }
}
