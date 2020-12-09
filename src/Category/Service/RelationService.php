<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Category\Service;

use OxidEsales\GraphQL\Base\DataType\PaginationFilter;
use OxidEsales\GraphQL\Base\DataType\StringFilter;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Catalogue\Category\DataType\Category;
use OxidEsales\GraphQL\Catalogue\Category\DataType\CategoryFilterList;
use OxidEsales\GraphQL\Catalogue\Category\DataType\CategoryIDFilter;
use OxidEsales\GraphQL\Catalogue\Category\DataType\Sorting;
use OxidEsales\GraphQL\Catalogue\Category\Exception\CategoryNotFound;
use OxidEsales\GraphQL\Catalogue\Category\Service\Category as CategoryService;
use OxidEsales\GraphQL\Catalogue\Product\DataType\Product;
use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductFilterList;
use OxidEsales\GraphQL\Catalogue\Product\DataType\Sorting as ProductSorting;
use OxidEsales\GraphQL\Catalogue\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Catalogue\Shared\DataType\Seo;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=Category::class)
 */
final class RelationService
{
    /** @var ProductService */
    private $productService;

    /** @var CategoryService */
    private $categoryService;

    public function __construct(
        ProductService $productService,
        CategoryService $categoryService
    ) {
        $this->productService  = $productService;
        $this->categoryService = $categoryService;
    }

    /**
     * @Field()
     */
    public function getParent(Category $category): ?Category
    {
        try {
            return $this->categoryService->category(
                (string) $category->getParentId()
            );
        } catch (InvalidLogin | CategoryNotFound $e) {
        }

        return null;
    }

    /**
     * @Field()
     */
    public function getRoot(Category $category): ?Category
    {
        try {
            return $this->categoryService->category(
                (string) $category->getRootId()
            );
        } catch (InvalidLogin | CategoryNotFound $e) {
        }

        return null;
    }

    /**
     * @Field()
     *
     * @return Category[]
     */
    public function getChildren(Category $category): array
    {
        return $this->categoryService->categories(
            new CategoryFilterList(
                null,
                new StringFilter((string) $category->getId())
            ),
            Sorting::fromUserInput()
        );
    }

    /**
     * @Field()
     */
    public function getSeo(Category $category): Seo
    {
        return new Seo($category->getEshopModel());
    }

    /**
     * @Field()
     *
     * @return Product[]
     */
    public function getProducts(
        Category $category,
        ?PaginationFilter $pagination,
        ?ProductSorting $sort
    ): array {
        $defSort = new ProductSorting([]);

        if ($category->getDefSort()) {
            $defSortMode = $category->getDefSortMode() !== 0 ? ProductSorting::SORTING_DESC : ProductSorting::SORTING_ASC;
            $defSort     = new ProductSorting([$category->getDefSort() => $defSortMode]);
        }

        return $this->productService->products(
            new ProductFilterList(
                null,
                new CategoryIDFilter($category->getId())
            ),
            $pagination,
            $sort ?? $defSort
        );
    }
}
