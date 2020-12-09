<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\DataType;

use OxidEsales\Eshop\Application\Model\Article as EshopProductModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class ProductImageGallery
{
    /** @var EshopProductModel */
    private $productModel;

    public function __construct(EshopProductModel $productModel)
    {
        $this->productModel = $productModel;
    }

    /**
     * @Field()
     *
     * @return ProductImage[]
     */
    public function getImages(): array
    {
        $gallery = $this->productModel->getPictureGallery();
        $images  = [];

        foreach ($gallery['Pics'] as $key => $imageUrl) {
            $images[$key] = new ProductImage(
                $imageUrl,
                $gallery['Icons'][$key] ?? '',
                $gallery['ZoomPics'][$key]['file'] ?? ''
            );
        }

        return $images;
    }

    /**
     * @Field()
     */
    public function getIcon(): string
    {
        return $this->productModel->getIconUrl();
    }

    /**
     * @Field()
     */
    public function getThumb(): string
    {
        return $this->productModel->getThumbnailUrl();
    }
}
