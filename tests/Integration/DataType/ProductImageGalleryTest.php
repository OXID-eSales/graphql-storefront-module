<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Integration\DataType;

use OxidEsales\Eshop\Application\Model\Article as EshopArticle;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Catalogue\Category\Service\Category as CategoryService;
use OxidEsales\GraphQL\Catalogue\Product\DataType\Product;
use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductImage;
use OxidEsales\GraphQL\Catalogue\Product\Infrastructure\Product as ProductInfrastructure;
use OxidEsales\GraphQL\Catalogue\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Catalogue\Product\Service\RelationService;
use OxidEsales\GraphQL\Catalogue\Shared\Infrastructure\Repository;
use PHPUnit\Framework\TestCase;

/**
 * @covers OxidEsales\GraphQL\Catalogue\Product\DataType\ProductImage
 * @covers OxidEsales\GraphQL\Catalogue\Product\Service\RelationService
 */
final class ProductImageGalleryTest extends TestCase
{
    public function testGetImageGalleryIconAndThumb(): void
    {
        $article = oxNew(EshopArticle::class);
        $article->load('058de8224773a1d5fd54d523f0c823e0');
        $product = new Product(
            $article
        );
        $productRelation = $this->productRelationService();

        $imageGallery = $productRelation->getImageGallery($product);

        $this->assertRegExp(
            '@^http.*?/out/pictures/generated/product/1/390_245_75/cabrinha_caliber_2011.jpg$@msi',
            $imageGallery->getThumb()
        );

        $this->assertRegExp(
            '@^http.*?/out/pictures/generated/product/1/87_87_75/cabrinha_caliber_2011.jpg$@msi',
            $imageGallery->getIcon()
        );
    }

    public function testGetImageGalleryImagesTypeAndCount(): void
    {
        $article = oxNew(EshopArticle::class);
        $article->load('058de8224773a1d5fd54d523f0c823e0');
        $product = new Product(
            $article
        );
        $productRelation = $this->productRelationService();

        $imageGallery = $productRelation->getImageGallery($product);

        $images = $imageGallery->getImages();
        $this->assertCount(3, $images);

        foreach ($images as $oneImage) {
            $this->assertInstanceOf(ProductImage::class, $oneImage);
        }
    }

    /**
     * @param $key
     * @param $image
     * @param $icon
     * @param $zoom
     *
     * @dataProvider getImageGalleryImagesContentDataProvider
     */
    public function testGetImageGalleryImagesContent($key, $image, $icon, $zoom): void
    {
        $article = oxNew(EshopArticle::class);
        $article->load('058de8224773a1d5fd54d523f0c823e0');
        $product = new Product(
            $article
        );
        $productRelation = $this->productRelationService();

        $imageGallery = $productRelation->getImageGallery($product);

        /** @var ProductImage[] $images */
        $images = $imageGallery->getImages();

        $this->assertRegExp($image, $images[$key]->getImage());
        $this->assertRegExp($icon, $images[$key]->getIcon());
        $this->assertRegExp($zoom, $images[$key]->getZoom());
    }

    public function getImageGalleryImagesContentDataProvider()
    {
        return [
            [
                1,
                '@^http.*?/out/pictures/generated/product/1/540_340_75/cabrinha_caliber_2011.jpg$@msi',
                '@^http.*?/out/pictures/generated/product/1/87_87_75/cabrinha_caliber_2011.jpg$@msi',
                '@^http.*?/out/pictures/generated/product/1/665_665_75/cabrinha_caliber_2011.jpg$@msi',
            ],
            [
                2,
                '@^http.*?/out/pictures/generated/product/2/540_340_75/cabrinha_caliber_2011_deck.jpg$@msi',
                '@^http.*?/out/pictures/generated/product/2/87_87_75/cabrinha_caliber_2011_deck.jpg$@msi',
                '@^http.*?/out/pictures/generated/product/2/665_665_75/cabrinha_caliber_2011_deck.jpg$@msi',
            ],
            [
                3,
                '@^http.*?/out/pictures/generated/product/3/540_340_75/cabrinha_caliber_2011_bottom.jpg$@msi',
                '@^http.*?/out/pictures/generated/product/3/87_87_75/cabrinha_caliber_2011_bottom.jpg$@msi',
                '@^http.*?/out/pictures/generated/product/3/665_665_75/cabrinha_caliber_2011_bottom.jpg$@msi',
            ],
        ];
    }

    private function productRelationService(): RelationService
    {
        $repo = new Repository(
            $this->createMock(QueryBuilderFactoryInterface::class)
        );

        return new RelationService(
            new ProductService(
                $repo,
                $this->createMock(Authorization::class)
            ),
            new CategoryService(
                $repo,
                $this->createMock(Authorization::class)
            ),
            new ProductInfrastructure(
                new CategoryService(
                    $repo,
                    $this->createMock(Authorization::class)
                )
            )
        );
    }
}
