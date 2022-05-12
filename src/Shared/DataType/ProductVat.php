<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\DataType;

use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @extendable-dataType
 */
class ProductVat implements ProductVatsInterface
{
    /** @var float */
    private $vatRate;

    /** @var float */
    private $vatPrice;

    public function __construct(float $vatRate, float $vatPrice)
    {
        $this->vatRate = $vatRate;
        $this->vatPrice = $vatPrice;
    }

    /**
     * @Field()
     */
    public function getVatRate(): float
    {
        return (float)($this->vatRate);
    }

    /**
     * @Field()
     */
    public function getVatPrice(): float
    {
        return (float)($this->vatPrice);
    }
}
