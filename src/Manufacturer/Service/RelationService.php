<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Manufacturer\Service;

use OxidEsales\GraphQL\Base\DataType\IDFilter;
use OxidEsales\GraphQL\Base\DataType\PaginationFilter;
use OxidEsales\GraphQL\Catalogue\Manufacturer\DataType\Manufacturer;
use OxidEsales\GraphQL\Catalogue\Product\DataType\Product as ProductDataType;
use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductFilterList;
use OxidEsales\GraphQL\Catalogue\Product\DataType\Sorting;
use OxidEsales\GraphQL\Catalogue\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Catalogue\Shared\DataType\Seo;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=Manufacturer::class)
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
    public function getSeo(Manufacturer $manufacturer): Seo
    {
        return new Seo($manufacturer->getEshopModel());
    }

    /**
     * @Field()
     *
     * @return ProductDataType[]
     */
    public function getProducts(
        Manufacturer $manufacturer,
        ?PaginationFilter $pagination,
        ?Sorting $sort
    ): array {
        return $this->productService->products(
            new ProductFilterList(
                null,
                null,
                new IDFilter(
                    $manufacturer->getId()
                )
            ),
            $pagination,
            $sort ?? Sorting::fromUserInput()
        );
    }
}
