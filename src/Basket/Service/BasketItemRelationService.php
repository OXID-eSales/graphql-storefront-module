<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketItem;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @ExtendType(class=BasketItem::class)
 */
final class BasketItemRelationService
{
    /** @var ProductService */
    private $productService;

    public function __construct(
        ProductService $productService
    ) {
        $this->productService = $productService;
    }

    /**
     * @Field()
     */
    public function product(BasketItem $basketItem): ?Product
    {
        $basketItemModel = $basketItem->getEshopModel();

        try {
            return $this->productService->product(
                new ID($basketItemModel->getFieldData('oxartid'))
            );
        } catch (ProductNotFound | InvalidLogin $e) {
        }

        return null;
    }
}
