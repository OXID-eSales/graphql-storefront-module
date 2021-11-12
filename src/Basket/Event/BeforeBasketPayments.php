<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Event;

use OxidEsales\GraphQL\Storefront\Payment\DataType\BasketPayment;
use Symfony\Component\EventDispatcher\Event;
use TheCodingMachine\GraphQLite\Types\ID;

final class BeforeBasketPayments extends Event implements BasketModifyInterface
{
    public const NAME = self::class;

    /** @var ID */
    private $basketId;

    /** @var null|BasketPayment[] */
    private $payments;

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
     * @return null|BasketPayment[]
     */
    public function getPayments(): ?array
    {
        return $this->payments;
    }

    public function setPayments(?array $payments = null): void
    {
        $this->payments = $payments;
    }
}
