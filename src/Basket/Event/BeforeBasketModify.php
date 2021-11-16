<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Event;

use Symfony\Contracts\EventDispatcher\Event;
use TheCodingMachine\GraphQLite\Types\ID;

final class BeforeBasketModify extends Event implements BasketModifyInterface
{
    public const TYPE_NOT_SPECIFIED = 0;

    public const TYPE_SET_DELIVERY_ADDRESS = 1;

    public const TYPE_SET_DELIVERY_METHOD = 2;

    public const TYPE_SET_PAYMENT_METHOD = 3;

    public const NAME = self::class;

    /** @var ID */
    private $basketId;

    /** @var int */
    private $type;

    public function __construct(
        ID $basketId,
        int $type = self::TYPE_NOT_SPECIFIED
    ) {
        $this->basketId = $basketId;
        $this->type     = $type;
    }

    public function getBasketId(): ID
    {
        return $this->basketId;
    }

    public function getEventType(): int
    {
        return $this->type;
    }
}
