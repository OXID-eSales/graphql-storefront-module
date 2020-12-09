<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Banner\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Catalogue\Banner\DataType\Banner;
use OxidEsales\GraphQL\Catalogue\Banner\Infrastructure\Banner as BannerInfrastructure;
use OxidEsales\GraphQL\Catalogue\Product\DataType\Product;
use OxidEsales\GraphQL\Catalogue\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Catalogue\Product\Service\Product as ProductService;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

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
                $productId
            );
        } catch (ProductNotFound | InvalidLogin $e) {
            return null;
        }
    }
}
