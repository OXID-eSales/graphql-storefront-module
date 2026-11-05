<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\Service;

use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\Subscriber as SubscriberDataType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Exception\SubscriberNotFound;

interface SubscriberServiceInterface
{
    /**
     * @throws SubscriberNotFound
     */
    public function subscriber(string $id): SubscriberDataType;
}
