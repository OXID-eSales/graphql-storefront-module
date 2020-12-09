<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Product\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Catalogue\Category\DataType\Category;
use OxidEsales\GraphQL\Catalogue\Category\Service\Category as CategoryService;
use OxidEsales\GraphQL\Catalogue\Manufacturer\DataType\Manufacturer;
use OxidEsales\GraphQL\Catalogue\Product\DataType\Product;
use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductAttribute;
use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductDeliveryTime;
use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductDimensions;
use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductImageGallery;
use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductRating;
use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductScalePrice;
use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductStock;
use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductUnit;
use OxidEsales\GraphQL\Catalogue\Product\DataType\SelectionList;
use OxidEsales\GraphQL\Catalogue\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Catalogue\Product\Infrastructure\Product as ProductInfrastructure;
use OxidEsales\GraphQL\Catalogue\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Catalogue\Review\DataType\Review;
use OxidEsales\GraphQL\Catalogue\Shared\DataType\Price;
use OxidEsales\GraphQL\Catalogue\Shared\DataType\Seo;
use OxidEsales\GraphQL\Catalogue\Vendor\DataType\Vendor;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

use function strlen;

/**
 * @ExtendType(class=Product::class)
 */
final class RelationService
{
    /** @var ProductService */
    private $productService;

    /** @var CategoryService */
    private $categoryService;

    /** @var ProductInfrastructure */
    private $productInfrastructure;

    public function __construct(
        ProductService $productService,
        CategoryService $categoryService,
        ProductInfrastructure $productInfrastructure
    ) {
        $this->productService        = $productService;
        $this->categoryService       = $categoryService;
        $this->productInfrastructure = $productInfrastructure;
    }

    /**
     * @Field()
     */
    public function getDimensions(Product $product): ProductDimensions
    {
        return new ProductDimensions(
            $product->getEshopModel()
        );
    }

    /**
     * @Field()
     */
    public function getPrice(Product $product): Price
    {
        return new Price(
            $product->getEshopModel()->getPrice()
        );
    }

    /**
     * @Field()
     */
    public function getListPrice(Product $product): Price
    {
        return new Price(
            $product->getEshopModel()->getTPrice()
        );
    }

    /**
     * @Field()
     */
    public function getStock(Product $product): ProductStock
    {
        return new ProductStock(
            $product->getEshopModel()
        );
    }

    /**
     * @Field()
     */
    public function getImageGallery(Product $product): ProductImageGallery
    {
        return new ProductImageGallery(
            $product->getEshopModel()
        );
    }

    /**
     * @Field()
     */
    public function getRating(Product $product): ProductRating
    {
        return new ProductRating(
            $product->getEshopModel()
        );
    }

    /**
     * @Field()
     */
    public function getDeliveryTime(Product $product): ProductDeliveryTime
    {
        return new ProductDeliveryTime(
            $product->getEshopModel()
        );
    }

    /**
     * @Field()
     *
     * @return ProductScalePrice[]
     */
    public function getScalePrices(Product $product): array
    {
        return $this->productInfrastructure->getScalePrices($product);
    }

    /**
     * @Field()
     */
    public function getBundleProduct(Product $product): ?Product
    {
        $bundleProductId = $product->getBundleId();

        if (!strlen($bundleProductId)) {
            return null;
        }

        try {
            return $this->productService->product(
                $bundleProductId
            );
        } catch (ProductNotFound | InvalidLogin $e) {
        }

        return null;
    }

    /**
     * @Field()
     */
    public function getManufacturer(Product $product): ?Manufacturer
    {
        return $this->productInfrastructure->getManufacturer($product);
    }

    /**
     * @Field()
     */
    public function getVendor(Product $product): ?Vendor
    {
        return $this->productInfrastructure->getVendor($product);
    }

    /**
     * @Field()
     *
     * @return Category[]
     */
    public function getCategories(
        Product $product,
        bool $onlyMainCategory = true
    ): array {
        $categories = [];

        if ($onlyMainCategory) {
            $category = $this->productInfrastructure->getMainCategory($product);

            if (!$category) {
                return [];
            }

            $categories[] = new Category($category);
        } else {
            /** @var array $categoryIds */
            $categoryIds = $this->productInfrastructure->getCategories($product);

            foreach ($categoryIds as $categoryId) {
                $categories[] = $this->categoryService->category($categoryId);
            }
        }

        return $categories;
    }

    /**
     * @Field()
     */
    public function getUnit(Product $product): ?ProductUnit
    {
        if (!$product->getEshopModel()->getUnitPrice()) {
            return null;
        }

        return new ProductUnit(
            $product->getEshopModel()
        );
    }

    /**
     * @Field()
     */
    public function getSeo(Product $product): Seo
    {
        return new Seo($product->getEshopModel());
    }

    /**
     * @Field()
     *
     * @return Product[]
     */
    public function getCrossSelling(Product $product): array
    {
        return $this->productInfrastructure->getCrossSelling($product);
    }

    /**
     * @Field()
     *
     * @return ProductAttribute[]
     */
    public function getAttributes(Product $product): array
    {
        return $this->productInfrastructure->getAttributes($product);
    }

    /**
     * @Field()
     *
     * @return Product[]
     */
    public function getAccessories(Product $product): array
    {
        return $this->productInfrastructure->getAccessories($product);
    }

    /**
     * @Field()
     *
     * @return SelectionList[]
     */
    public function getSelectionLists(Product $product): array
    {
        return $this->productInfrastructure->getSelectionLists($product);
    }

    /**
     * @Field()
     *
     * @return Review[]
     */
    public function getReviews(Product $product): array
    {
        return $this->productInfrastructure->getReviews($product);
    }

    /**
     * @Field()
     *
     * @return Product[]
     */
    public function getVariants(Product $product): array
    {
        return $this->productInfrastructure->getVariants($product);
    }
}
