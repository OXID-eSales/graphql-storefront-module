<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Vendor\Service;

use OxidEsales\GraphQL\Base\DataType\IDFilter;
use OxidEsales\GraphQL\Base\DataType\PaginationFilter;
use OxidEsales\GraphQL\Catalogue\Product\DataType\Product;
use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductFilterList;
use OxidEsales\GraphQL\Catalogue\Product\DataType\Sorting;
use OxidEsales\GraphQL\Catalogue\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Catalogue\Shared\DataType\Seo;
use OxidEsales\GraphQL\Catalogue\Vendor\DataType\Vendor;
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
