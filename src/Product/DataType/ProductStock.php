<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Product\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\Article as EshopProductModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Catalogue\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class ProductStock implements DataType
{
    /** @var EshopProductModel */
    private $product;

    public function __construct(
        EshopProductModel $product
    ) {
        $this->product = $product;
    }

    public function getEshopModel(): EshopProductModel
    {
        return $this->product;
    }

    /**
     * @Field
     */
    public function getStock(): float
    {
        return $this->product->getStock();
    }

    /**
     * Value can be one of:
     *  0 -> (green) deliverable
     *  1 -> (orange) deliverable, but only a few left
     * -1 -> (red) not stock
     *
     * @Field
     * @TODO with the update to GraphQLite v4 update this to ENUM
     */
    public function getStockStatus(): int
    {
        return $this->product->getStockStatus();
    }

    /**
     * @Field()
     */
    public function getRestockDate(): ?DateTimeInterface
    {
        /** @var false|string */
        $restockDate = $this->product->getDeliveryDate();

        if (!$restockDate) {
            return null;
        }

        return DateTimeImmutableFactory::fromString(
            $restockDate
        );
    }

    /**
     * @return string class-string
     */
    public static function getModelClass(): string
    {
        return EshopProductModel::class;
    }
}
