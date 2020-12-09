<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\DataType;

use OxidEsales\Eshop\Application\Model\Basket as EshopBasketModel;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class BasketProductBruttoSum implements DataType
{
    /** @var EshopBasketModel */
    private $basket;

    public function __construct(
        EshopBasketModel $basket
    ) {
        $this->basket = $basket;
    }

    public function getEshopModel(): EshopBasketModel
    {
        return $this->basket;
    }

    /**
     * @Field()
     */
    public function getSum(): float
    {
        return (float) $this->basket->getBruttoSum();
    }

    public static function getModelClass(): string
    {
        return EshopBasketModel::class;
    }
}
