<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Vendor\Service;

use OxidEsales\GraphQL\Base\DataType\IDFilter;
use OxidEsales\GraphQL\Base\DataType\PaginationFilter;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductFilterList;
use OxidEsales\GraphQL\Storefront\Product\DataType\Sorting;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Seo;
use OxidEsales\GraphQL\Storefront\Vendor\DataType\Vendor;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=Vendor::class)
 */
final class RelationService
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
    public function getSeo(Vendor $vendor): Seo
    {
        return new Seo($vendor->getEshopModel());
    }

    /**
     * @Field()
     *
     * @return Product[]
     */
    public function getProducts(
        Vendor $vendor,
        ?PaginationFilter $pagination,
        ?Sorting $sort
    ): array {
        return $this->productService->products(
            new ProductFilterList(
                null,
                null,
                null,
                new IDFilter($vendor->getId())
            ),
            $pagination,
            $sort ?? Sorting::fromUserInput()
        );
    }
}
