<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Event;

use Symfony\Contracts\EventDispatcher\Event;
use TheCodingMachine\GraphQLite\Types\ID;

final class BeforeBasketRemoveOnPlaceOrder extends Event
{
    public const NAME = self::class;

    /** @var ID */
    private $basketId;

    /** @var bool */
    private $preserveBasketAfterOrder = false;

    /**
     * BeforeBasketRemoveOnPlaceOrder constructor.
     */
    public function __construct(ID $basketId)
    {
        $this->basketId = $basketId;
    }

    public function getBasketId(): ID
    {
        return $this->basketId;
    }

    public function getPreserveBasketAfterOrder(): bool
    {
        return $this->preserveBasketAfterOrder;
    }

    public function setPreserveBasketAfterOrder(bool $preserveBasketAfterOrder = false): void
    {
        $this->preserveBasketAfterOrder = $preserveBasketAfterOrder;
    }
}
