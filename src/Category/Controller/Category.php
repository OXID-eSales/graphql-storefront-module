<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Category\Controller;

use OxidEsales\GraphQL\Storefront\Category\DataType\Category as CategoryDataType;
use OxidEsales\GraphQL\Storefront\Category\DataType\CategoryFilterList;
use OxidEsales\GraphQL\Storefront\Category\DataType\Sorting;
use OxidEsales\GraphQL\Storefront\Category\Service\Category as CategoryService;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Types\ID;

final class Category
{
    /** @var CategoryService */
    private $categoryService;

    public function __construct(
        CategoryService $categoryService
    ) {
        $this->categoryService = $categoryService;
    }

    /**
     * @Query()
     */
    public function category(?ID $categoryId, ?string $slug): CategoryDataType
    {
        return $this->categoryService->category($categoryId, $slug);
    }

    /**
     * @Query()
     *
     * @return CategoryDataType[]
     */
    public function categories(
        ?CategoryFilterList $filter = null,
        ?Sorting $sort = null
    ): array {
        return $this->categoryService->categories(
            $filter ?? new CategoryFilterList(),
            $sort ?? Sorting::fromUserInput()
        );
    }
}
