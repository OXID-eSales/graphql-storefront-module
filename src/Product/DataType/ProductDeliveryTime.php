<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\DataType;

use OxidEsales\Eshop\Application\Model\Article as EshopProductModel;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class ProductDeliveryTime implements ShopModelAwareInterface
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
    public function getMinDeliveryTime(): int
    {
        return (int)$this->product->getRawFieldData('oxmindeltime');
    }

    /**
     * @Field
     */
    public function getMaxDeliveryTime(): int
    {
        return (int)$this->product->getRawFieldData('oxmaxdeltime');
    }

    /**
     * Value can be one of:
     * - DAY
     * - WEEK
     * - MONTH
     *
     * @Field
     * @TODO with the update to GraphQLite v4 update this to ENUM
     */
    public function getDeliveryTimeUnit(): string
    {
        return (string)$this->product->getRawFieldData('oxdeltimeunit');
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return EshopProductModel::class;
    }
}
