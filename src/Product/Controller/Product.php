<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Product\Controller;

use OxidEsales\GraphQL\Base\DataType\PaginationFilter;
use OxidEsales\GraphQL\Catalogue\Product\DataType\Product as ProductDataType;
use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductFilterList;
use OxidEsales\GraphQL\Catalogue\Product\DataType\Sorting;
use OxidEsales\GraphQL\Catalogue\Product\Service\Product as ProductService;
use TheCodingMachine\GraphQLite\Annotations\Query;

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
    public function product(string $id): ProductDataType
    {
        return $this->productService->product($id);
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
