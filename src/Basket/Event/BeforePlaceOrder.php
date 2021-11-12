<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Event;

use Symfony\Component\EventDispatcher\Event;
use TheCodingMachine\GraphQLite\Types\ID;

final class BeforePlaceOrder extends Event implements BasketModifyInterface
{
    public const NAME = self::class;

    /** @var ID */
    private $basketId;

    /**
     * BeforePlaceOrder constructor.
     */
    public function __construct(ID $basketId)
    {
        $this->basketId = $basketId;
    }

    public function getBasketId(): ID
    {
        return $this->basketId;
    }
}
