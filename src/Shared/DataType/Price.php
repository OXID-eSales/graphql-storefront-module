<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\DataType;

use OxidEsales\Eshop\Core\Price as PriceModel;
use stdClass;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class Price
{
    /** @var PriceModel */
    private $price;

    /** @var ?stdClass */
    private $currency;

    public function __construct(PriceModel $price, ?stdClass $currency = null)
    {
        $this->price = $price;
        $this->currency = $currency;
    }

    /**
     * @Field()
     */
    public function getPrice(): float
    {
        return $this->price->getPrice();
    }

    /**
     * @Field()
     */
    public function getVat(): float
    {
        return $this->price->getVat();
    }

    /**
     * @Field()
     */
    public function getVatValue(): float
    {
        return $this->price->getVatValue();
    }

    /**
     * @Field()
     */
    public function isNettoPriceMode(): bool
    {
        return $this->price->isNettoMode();
    }

    public function getCurrencyObject(): ?stdClass
    {
        return $this->currency;
    }
}
