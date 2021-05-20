<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Event;

use Symfony\Contracts\EventDispatcher\Event;
use TheCodingMachine\GraphQLite\Types\ID;

final class BeforeRemoveItem extends Event
{
    public const NAME = self::class;

    /** @var ID */
    private $basketItemId;

    /** @var float */
    private $amount;

    /** @var ID */
    private $basketId;

    public function __construct(
        ID $basketId,
        ID $basketItemId,
        float $amount
    ) {
        $this->basketId     = $basketId;
        $this->basketItemId = $basketItemId;
        $this->amount       = $amount;
    }

    public function getBasketId(): ID
    {
        return $this->basketId;
    }

    public function getBasketItemId(): ID
    {
        return $this->basketItemId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }
}
