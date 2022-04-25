<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Event;

use TheCodingMachine\GraphQLite\Types\ID;

final class BeforeRemoveItem extends AbstractItemEvent implements BasketModifyInterface
{
    public const NAME = self::class;

    /** @var ID */
    protected $basketItemId;

    public function __construct(
        ID $basketId,
        ID $basketItemId,
        float $amount
    ) {
        $this->basketItemId = $basketItemId;
        parent::__construct($basketId, $amount);
    }

    public function getBasketItemId(): ID
    {
        return $this->basketItemId;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }
}
