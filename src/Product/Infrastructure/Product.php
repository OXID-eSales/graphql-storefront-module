<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\Infrastructure;

use OxidEsales\Eshop\Application\Model\Article as EshopProductModel;
use OxidEsales\Eshop\Application\Model\ArticleList as EshopProductListModel;
use OxidEsales\Eshop\Application\Model\Attribute as EshopAttributeModel;
use OxidEsales\Eshop\Application\Model\AttributeList as EshopAttributeListModel;
use OxidEsales\Eshop\Application\Model\Category as EshopCategoryModel;
use OxidEsales\Eshop\Application\Model\Manufacturer as EshopManufacturerModel;
use OxidEsales\Eshop\Application\Model\Review as EshopReviewModel;
use OxidEsales\Eshop\Application\Model\SelectList as EshopSelectionListModel;
use OxidEsales\Eshop\Application\Model\Vendor as EshopVendorModel;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Manufacturer\DataType\Manufacturer as ManufacturerDataType;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product as ProductDataType;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductAttribute as ProductAttributeDataType;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductScalePrice as ProductScalePriceDataType;
use OxidEsales\GraphQL\Storefront\Product\DataType\SelectionList as SelectionListDataType;
use OxidEsales\GraphQL\Storefront\Review\DataType\Review as ReviewDataType;
use OxidEsales\GraphQL\Storefront\Vendor\DataType\Vendor as VendorDataType;

use function array_map;
use function count;
use function is_iterable;

final class Product
{
    /**
     * get parent by id. return parent if available, otherwise current article
     *
     * @param $id
     * @return ProductDataType
     * @throws NotFound
     */
    public function getParentById($id): ProductDataType
    {
        $article = oxNew(EshopProductModel::class);

        if (!$article->load($id) || !$article->canView()) {
            throw new NotFound($id);
        }

        if ($parentId = $article->getFieldData('oxparentid')) {
            if (!$article->load($parentId) || !$article->canView()) {
                throw new NotFound($parentId);
            }
        }

        return new ProductDataType($article);
    }

    /**
     * @return ProductScalePriceDataType[]
     */
    public function getScalePrices(ProductDataType $product): array
    {
        $amountPrices = $product->getEshopModel()->loadAmountPriceInfo();

        return array_map(
            function ($amountPrice) {
                return new ProductScalePriceDataType($amountPrice);
            },
            $amountPrices
        );
    }

    public function getManufacturer(ProductDataType $product): ?ManufacturerDataType
    {
        /** @var null|EshopManufacturerModel $manufacturer */
        $manufacturer = $product->getEshopModel()->getManufacturer();

        if ($manufacturer === null) {
            return null;
        }

        return new ManufacturerDataType(
            $manufacturer
        );
    }

    public function getVendor(ProductDataType $product): ?VendorDataType
    {
        /** @var null|EshopVendorModel $vendor */
        $vendor = $product->getEshopModel()->getVendor();

        if ($vendor === null) {
            return null;
        }

        return new VendorDataType(
            $vendor
        );
    }

    /**
     * @return EshopCategoryModel
     */
    public function getMainCategory(ProductDataType $product): ?EshopCategoryModel
    {
        /** @var null|EshopCategoryModel $category */
        $category = $product->getEshopModel()->getCategory();

        if (
            $category === null ||
            !$category->getId()
        ) {
            return null;
        }

        return $category;
    }

    public function getCategories(
        ProductDataType $product
    ): array {
        return $product->getEshopModel()->getCategoryIds();
    }

    /**
     * @return ProductDataType[]
     */
    public function getCrossSelling(ProductDataType $product): array
    {
        /** @var EshopProductListModel $products */
        $products = $product->getEshopModel()->getCrossSelling();

        if (!is_iterable($products) || count($products) === 0) {
            return [];
        }

        $crossSellings = [];

        /** @var EshopProductModel $product */
        foreach ($products as $product) {
            $crossSellings[] = new ProductDataType($product);
        }

        return $crossSellings;
    }

    /**
     * @return ProductAttributeDataType[]
     */
    public function getAttributes(ProductDataType $product): array
    {
        /** @var EshopAttributeListModel $productAttributes */
        $productAttributes = $product->getEshopModel()->getAttributes();

        if (!is_iterable($productAttributes) || count($productAttributes) === 0) {
            return [];
        }

        $attributes = [];

        /** @var EshopAttributeModel $attribute */
        foreach ($productAttributes as $key => $attribute) {
            $attributes[$key] = new ProductAttributeDataType($attribute);
        }

        return $attributes;
    }

    /**
     * @return ProductDataType[]
     */
    public function getAccessories(ProductDataType $product): array
    {
        /** @var EshopProductListModel $products */
        $products = $product->getEshopModel()->getAccessoires();

        if (!is_iterable($products) || count($products) === 0) {
            return [];
        }

        $accessories = [];

        /** @var EshopProductModel $product */
        foreach ($products as $product) {
            $accessories[] = new ProductDataType($product);
        }

        return $accessories;
    }

    /**
     * @return SelectionListDataType[]
     */
    public function getSelectionLists(ProductDataType $product): array
    {
        $selections = $product->getEshopModel()->getSelections();

        if (!is_iterable($selections) || count($selections) === 0) {
            return [];
        }

        $selectionLists = [];

        /** @var EshopSelectionListModel $selection */
        foreach ($selections as $selection) {
            $selectionLists[] = new SelectionListDataType($selection);
        }

        return $selectionLists;
    }

    /**
     * @return ReviewDataType[]
     */
    public function getReviews(ProductDataType $product): array
    {
        $productReviews = $product->getEshopModel()->getReviews();

        if (!is_iterable($productReviews) || count($productReviews) === 0) {
            return [];
        }

        $reviews = [];

        /** @var EshopReviewModel $review */
        foreach ($productReviews as $review) {
            $reviews[] = new ReviewDataType($review);
        }

        return $reviews;
    }

    /**
     * @return ProductDataType[]
     */
    public function getVariants(ProductDataType $product): array
    {
        // when using getVariants() product relations are returned as SimpleVariant type
        $productVariants = $product->getEshopModel()->getFullVariants();

        if (!is_iterable($productVariants) || count($productVariants) === 0) {
            return [];
        }

        $variants = [];

        /** @var EshopProductModel $variant */
        foreach ($productVariants as $variant) {
            $variants[] = new ProductDataType($variant);
        }

        return $variants;
    }
}
