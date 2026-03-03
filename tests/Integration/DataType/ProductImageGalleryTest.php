<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\DataType;

use OxidEsales\Eshop\Application\Model\Article as EshopArticle;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\GraphQL\Storefront\Category\Service\Category as CategoryService;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductImage;
use OxidEsales\GraphQL\Storefront\Product\Infrastructure\Product as ProductInfrastructure;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Storefront\Product\Service\RelationService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\ListConfiguration;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Tests\Integration\ImageUrlAssertionTrait;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @covers OxidEsales\GraphQL\Storefront\Product\DataType\ProductImage
 * @covers OxidEsales\GraphQL\Storefront\Product\Service\RelationService
 */
final class ProductImageGalleryTest extends IntegrationTestCase
{
    use ImageUrlAssertionTrait;

    public function testGetImageGalleryIconAndThumb(): void
    {
        $article = oxNew(EshopArticle::class);
        $article->load('058de8224773a1d5fd54d523f0c823e0');
        $product = new Product(
            $article
        );
        $productRelation = $this->productRelationService();

        $imageGallery = $productRelation->getImageGallery($product);

        $this->assertUrlIsProductImageUrl($imageGallery->getThumb(), 'cabrinha_caliber_2011.jpg', 'thumb');
        $this->assertUrlIsProductImageUrl($imageGallery->getIcon(), 'cabrinha_caliber_2011.jpg', 'icon');
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

    #[DataProvider('getImageGalleryImagesContentDataProvider')]
    public function testGetImageGalleryImagesContent(int $key, string $fileName): void
    {
        $article = oxNew(EshopArticle::class);
        $article->load('058de8224773a1d5fd54d523f0c823e0');
        $product = new Product(
            $article
        );
        $productRelation = $this->productRelationService();

        $imageGallery = $productRelation->getImageGallery($product);
        $images = $imageGallery->getImages();

        $this->assertUrlIsProductImageUrl($images[$key]->getImage(), $fileName, 'image', $key);
        $this->assertUrlIsProductImageUrl($images[$key]->getIcon(), $fileName, 'icon', $key);
        $this->assertUrlIsProductImageUrl($images[$key]->getZoom(), $fileName, 'zoom', $key);
    }

    public static function getImageGalleryImagesContentDataProvider(): array
    {
        return [
            [1, 'cabrinha_caliber_2011.jpg'],
            [2, 'cabrinha_caliber_2011_deck.jpg'],
            [3, 'cabrinha_caliber_2011_bottom.jpg'],
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
                $this->createStub(Authorization::class),
                new ProductInfrastructure()
            ),
            new CategoryService(
                $repo,
                $this->createStub(Authorization::class),
            ),
            new ProductInfrastructure()
        );
    }
}
