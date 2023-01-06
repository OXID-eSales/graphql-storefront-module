<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Category\Service;

use OxidEsales\GraphQL\Base\DataType\Filter\StringFilter;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Storefront\Category\DataType\Category;
use OxidEsales\GraphQL\Storefront\Category\DataType\CategoryFilterList;
use OxidEsales\GraphQL\Storefront\Category\DataType\CategoryIDFilter;
use OxidEsales\GraphQL\Storefront\Category\DataType\Sorting;
use OxidEsales\GraphQL\Storefront\Category\Exception\CategoryNotFound;
use OxidEsales\GraphQL\Storefront\Category\Infrastructure\Category as CategoryInfrastructure;
use OxidEsales\GraphQL\Storefront\Category\Service\Category as CategoryService;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductFilterList;
use OxidEsales\GraphQL\Storefront\Product\DataType\Sorting as ProductSorting;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Seo;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Types\ID;
use OxidEsales\GraphQL\Storefront\Category\DataType\CategoryAttribute;

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
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    /**
     * @Field()
     */
    public function getParent(Category $category): ?Category
    {
        return $this->getCategoryById($category->getParentId());
    }

    /**
     * @Field()
     */
    public function getRoot(Category $category): ?Category
    {
        return $this->getCategoryById($category->getRootId());
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
                new StringFilter((string)$category->getId())
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
            $defSortMode = $category->getDefSortMode() !== 0
                ? ProductSorting::SORTING_DESC
                : ProductSorting::SORTING_ASC;
            $defSort = new ProductSorting([$category->getDefSort() => $defSortMode]);
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

    private function getCategoryById(ID $id): ?Category
    {
        try {
            $result = $this->categoryService->category(
                $id
            );
        } catch (InvalidLogin | CategoryNotFound $e) {
            $result = null;
        }

        return $result;
    }

    /**
     * @Field()
     *
     * @return CategoryAttribute[]
     */
    public function getAttributes(Category $category): array
    {
        $categoryAttributes = $category->getEshopModel()->getAttributes();
    
        if (!is_iterable($categoryAttributes) || count($categoryAttributes) === 0) {
            return [];
        }
    
        $attributes = [];
    
        /** @var EshopAttributeModel $attribute */
        foreach ($categoryAttributes as $key => $attribute) {
            $attributes[$key] = new CategoryAttribute($attribute);
        }
    
        return $attributes;
    }

}
