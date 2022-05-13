<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Event;

use TheCodingMachine\GraphQLite\Types\ID;

final class BeforeAddItem extends AbstractItemEvent implements BasketModifyInterface
{

    /** @var ID */
    protected $productId;

    public function __construct(
        ID $basketId,
        ID $productId,
        float $amount
    ) {
        $this->productId = $productId;
        parent::__construct($basketId, $amount);
    }

    public function getProductId(): ID
    {
        return $this->productId;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }
}
