<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Event;

final class BeforeAddItem extends AbstractItemEvent implements BasketModifyInterface
{
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }
}
