<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\File\Service;

use OxidEsales\GraphQL\Storefront\File\DataType\File;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @ExtendType(class=File::class)
 */
final class FileRelations
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
    public function getProduct(File $file): Product
    {
        return $this->productService->product(
            new ID($file->productId())
        );
    }
}
