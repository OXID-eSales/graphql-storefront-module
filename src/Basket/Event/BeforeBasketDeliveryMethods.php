<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Event;

use OxidEsales\GraphQL\Storefront\DeliveryMethod\DataType\BasketDeliveryMethod;
use Symfony\Contracts\EventDispatcher\Event;
use TheCodingMachine\GraphQLite\Types\ID;

final class BeforeBasketDeliveryMethods extends Event
{
    public const NAME = self::class;

    /** @var ID */
    private $basketId;

    /** @var null|BasketDeliveryMethod[] */
    private $deliveryMethods;

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

    /**
     * @return null|BasketDeliveryMethod[]
     */
    public function getDeliveryMethods(): ?array
    {
        return $this->deliveryMethods;
    }

    public function setDeliveryMethods(?array $deliveryMethods = null): void
    {
        $this->deliveryMethods = $deliveryMethods;
    }
}
