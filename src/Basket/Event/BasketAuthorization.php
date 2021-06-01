<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Event;

use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket;
use Symfony\Contracts\EventDispatcher\Event;
use TheCodingMachine\GraphQLite\Types\ID;

final class BasketAuthorization extends Event
{
    public const NAME = self::class;

    /** @var Basket */
    private $basket;

    /** @var ID */
    private $customerId;

    /** @var bool */
    private $authorized = false;

    public function __construct(Basket $basket, ID $customerId)
    {
        $this->basket   = $basket;
        $this->customerId = $customerId;
    }

    public function getBasket(): Basket
    {
        return $this->basket;
    }

    public function getCustomerId(): ID
    {
        return $this->customerId;
    }

    public function setAuthorized(bool $authorized): void
    {
        $this->authorized = $authorized;
    }

    public function getAuthorized(): bool
    {
        return $this->authorized;
    }
}
