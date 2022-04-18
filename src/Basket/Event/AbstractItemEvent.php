<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Event;

use Symfony\Contracts\EventDispatcher\Event;
use TheCodingMachine\GraphQLite\Types\ID;

abstract class AbstractItemEvent extends Event
{
    public const NAME = self::class;

    /** @var ID */
    protected $basketId;

    /** @var ID */
    protected $basketItemId;

    /** @var float */
    protected $amount;

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
}
