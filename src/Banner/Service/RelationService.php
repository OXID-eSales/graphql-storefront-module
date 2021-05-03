<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Banner\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Storefront\Banner\DataType\Banner;
use OxidEsales\GraphQL\Storefront\Banner\Infrastructure\Banner as BannerInfrastructure;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @ExtendType(class=Banner::class)
 */
final class RelationService
{
    /** @var ProductService */
    private $productService;

    /** @var BannerInfrastructure */
    private $bannerInfrastructure;

    public function __construct(
        ProductService $productService,
        BannerInfrastructure $bannerInfrastructure
    ) {
        $this->productService        = $productService;
        $this->bannerInfrastructure  = $bannerInfrastructure;
    }

    /**
     * @Field()
     */
    public function getProduct(Banner $banner): ?Product
    {
        $productId = $this->bannerInfrastructure->getProductId($banner);

        if ($productId === null) {
            return null;
        }

        try {
            return $this->productService->product(
                new ID($productId)
            );
        } catch (ProductNotFound | InvalidLogin $e) {
            return null;
        }
    }
}
