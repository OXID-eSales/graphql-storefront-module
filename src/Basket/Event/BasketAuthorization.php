<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Event;

use Symfony\Contracts\EventDispatcher\Event;
use TheCodingMachine\GraphQLite\Types\ID;

final class BasketAuthorization extends Event
{
    public const NAME = self::class;

    /** @var ID */
    private $basketId;

    /** @var ID */
    private $customerId;

    /** @var ?bool */
    private $authorized;

    public function __construct(ID $basketId, ID $customerId)
    {
        $this->basketId   = $basketId;
        $this->customerId = $customerId;
    }

    public function getBasketId(): ID
    {
        return $this->basketId;
    }

    public function getCustomerId(): ID
    {
        return $this->customerId;
    }

    public function setAuthorized(?bool $authorized = null): void
    {
        $this->authorized = $authorized;
    }

    public function getAuthorized(): ?bool
    {
        return $this->authorized;
    }
}
