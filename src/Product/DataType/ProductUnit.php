<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\DataType;

use OxidEsales\Eshop\Application\Model\Article as EshopProductModel;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Price;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class ProductUnit implements ShopModelAwareInterface
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
    public function getPrice(): Price
    {
        /** @var \OxidEsales\Eshop\Core\Price */
        $unitPrice = $this->product->getUnitPrice();

        return new Price(
            $unitPrice
        );
    }

    /**
     * @Field()
     */
    public function getName(): string
    {
        return $this->product->getUnitName();
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return EshopProductModel::class;
    }
}
