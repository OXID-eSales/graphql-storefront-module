<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\DataType;

use OxidEsales\Eshop\Application\Model\Article as EshopArticle;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Storefront\Category\Service\Category as CategoryService;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductImage;
use OxidEsales\GraphQL\Storefront\Product\Infrastructure\Product as ProductInfrastructure;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Storefront\Product\Service\RelationService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\ListConfiguration;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use PHPUnit\Framework\TestCase;

/**
 * @covers OxidEsales\GraphQL\Storefront\Product\DataType\ProductImage
 * @covers OxidEsales\GraphQL\Storefront\Product\Service\RelationService
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

        $this->assertMatchesRegularExpression(
            '@^http.*?/out/pictures/generated/product/1/500_500_75/cabrinha_caliber_2011.jpg$@msi',
            $imageGallery->getThumb()
        );

        $this->assertMatchesRegularExpression(
            '@^http.*?/out/pictures/generated/product/1/100_100_75/cabrinha_caliber_2011.jpg$@msi',
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

        $this->assertMatchesRegularExpression($image, $images[$key]->getImage());
        $this->assertMatchesRegularExpression($icon, $images[$key]->getIcon());
        $this->assertMatchesRegularExpression($zoom, $images[$key]->getZoom());
    }

    public static function getImageGalleryImagesContentDataProvider()
    {
        return [
            [
                1,
                '@^http.*?/out/pictures/generated/product/1/800_600_75/cabrinha_caliber_2011.jpg$@msi',
                '@^http.*?/out/pictures/generated/product/1/100_100_75/cabrinha_caliber_2011.jpg$@msi',
                '@^http.*?/out/pictures/generated/product/1/1200_1200_75/cabrinha_caliber_2011.jpg$@msi',
            ],
            [
                2,
                '@^http.*?/out/pictures/generated/product/2/800_600_75/cabrinha_caliber_2011_deck.jpg$@msi',
                '@^http.*?/out/pictures/generated/product/2/100_100_75/cabrinha_caliber_2011_deck.jpg$@msi',
                '@^http.*?/out/pictures/generated/product/2/1200_1200_75/cabrinha_caliber_2011_deck.jpg$@msi',
            ],
            [
                3,
                '@^http.*?/out/pictures/generated/product/3/800_600_75/cabrinha_caliber_2011_bottom.jpg$@msi',
                '@^http.*?/out/pictures/generated/product/3/100_100_75/cabrinha_caliber_2011_bottom.jpg$@msi',
                '@^http.*?/out/pictures/generated/product/3/1200_1200_75/cabrinha_caliber_2011_bottom.jpg$@msi',
            ],
        ];
    }

    private function productRelationService(): RelationService
    {
        $repo = new Repository(
            $this->createMock(QueryBuilderFactoryInterface::class),
            new ListConfiguration()
        );

        return new RelationService(
            new ProductService(
                $repo,
                new Authorization(),
                new ProductInfrastructure()
            ),
            new CategoryService(
                $repo,
                new Authorization()
            ),
            new ProductInfrastructure(
                new CategoryService(
                    $repo,
                    new Authorization()
                )
            )
        );
    }
}
