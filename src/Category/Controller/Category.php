<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Category\Controller;

use OxidEsales\GraphQL\Catalogue\Category\DataType\Category as CategoryDataType;
use OxidEsales\GraphQL\Catalogue\Category\DataType\CategoryFilterList;
use OxidEsales\GraphQL\Catalogue\Category\DataType\Sorting;
use OxidEsales\GraphQL\Catalogue\Category\Service\Category as CategoryService;
use TheCodingMachine\GraphQLite\Annotations\Query;

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
    public function category(string $id): CategoryDataType
    {
        return $this->categoryService->category($id);
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
