<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Input;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Input;
use TheCodingMachine\GraphQLite\Types\ID;

#[Input(name: 'WishedPriceInput', default: true)]
final class WishedPriceInput implements WishedPriceInputInterface
{
    #[Field]
    private ID $productId;

    #[Field]
    private string $currencyName;

    #[Field]
    private float $price;

    public function __construct(
        ID $productId,
        string $currencyName,
        float $price
    ) {
        $this->productId = $productId;
        $this->currencyName = $currencyName;
        $this->price = $price;
    }

    public function getProductId(): ID
    {
        return $this->productId;
    }

    public function getCurrencyName(): string
    {
        return $this->currencyName;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}
