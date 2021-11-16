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
final class ProductRating implements ShopModelAwareInterface
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
    public function getRating(): float
    {
        return $this->product->getArticleRatingAverage(false);
    }

    /**
     * @Field
     */
    public function getCount(): int
    {
        /**
         * the upstream typehint is wrongly stated as double
         *
         * @var int
         */
        return (int) $this->product->getArticleRatingCount(false);
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return EshopProductModel::class;
    }
}
