<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Event;

use TheCodingMachine\GraphQLite\Types\ID;

interface BasketModifyInterface
{
    public function getBasketId(): ID;
}
