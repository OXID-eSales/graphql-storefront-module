<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Event;

use Symfony\Component\EventDispatcher\Event;
use TheCodingMachine\GraphQLite\Types\ID;

final class BeforeAddItem extends Event implements BasketModifyInterface
{
    public const NAME = self::class;

    /** @var ID */
    private $basketId;

    /** @var ID */
    private $productId;

    /** @var float */
    private $amount;

    public function __construct(
        ID $basketId,
        ID $productId,
        float $amount
    ) {
        $this->basketId  = $basketId;
        $this->productId = $productId;
        $this->amount    = $amount;
    }

    public function getBasketId(): ID
    {
        return $this->basketId;
    }

    public function getProductId(): ID
    {
        return $this->productId;
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
