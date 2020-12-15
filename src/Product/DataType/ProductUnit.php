<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\DataType;

use OxidEsales\Eshop\Application\Model\Article as EshopProductModel;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Price;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class ProductUnit
{
    /** @var EshopProductModel */
    private $product;

    public function __construct(
        EshopProductModel $product
    ) {
        $this->product = $product;
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
}
