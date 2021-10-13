<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\Controller;

use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product as ProductDataType;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductFilterList;
use OxidEsales\GraphQL\Storefront\Product\DataType\Sorting;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Types\ID;

final class Product
{
    /** @var ProductService */
    private $productService;

    public function __construct(
        ProductService $productService
    ) {
        $this->productService = $productService;
    }

    /**
     * @Query()
     */
    public function product(ID $productId): ProductDataType
    {
        return $this->productService->product($productId);
    }

    /**
     * @Query()
     *
     * @return ProductDataType[]
     */
    public function products(
        ?ProductFilterList $filter = null,
        ?PaginationFilter $pagination = null,
        ?Sorting $sort = null
    ): array {
        return $this->productService->products(
            $filter ?? new ProductFilterList(),
            $pagination,
            $sort ?? Sorting::fromUserInput()
        );
    }
}
