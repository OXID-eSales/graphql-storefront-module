<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\DataType;

use OxidEsales\Eshop\Application\Model\Article as EshopArticle;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;
use OxidEsales\GraphQL\Storefront\Category\Service\Category as CategoryService;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductAttribute;
use OxidEsales\GraphQL\Storefront\Product\Infrastructure\Product as ProductInfrastructure;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Storefront\Product\Service\RelationService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\ListConfiguration;
use OxidEsales\GraphQL\Base\Service\Authorization;

/**
 * @covers OxidEsales\GraphQL\Storefront\Product\DataType\ProductAttribute
 * @covers OxidEsales\GraphQL\Storefront\Product\Service\RelationService
 */
final class ProductAttributeTest extends TestCase
{
    public function testGetProductAttributesTypeAndCount(): void
    {
        $article = oxNew(EshopArticle::class);
        $article->load('096e38032896a847682651d565966c45');
        $product = new Product($article);

        $productRelation = $this->productRelationService();
        $productAttributes = $productRelation->getAttributes($product);

        $this->assertCount(2, $productAttributes);

        foreach ($productAttributes as $attribute) {
            $this->assertInstanceOf(ProductAttribute::class, $attribute);
        }
    }

    /**
     * @dataProvider getProductAttributesContentDataProvider
     */
    public function testGetProductAttributesContent(string $languageId, string $key, string $title, string $value): void
    {
        $this->setGETRequestParameter('lang', $languageId);

        $article = oxNew(EshopArticle::class);
        $article->load('096e38032896a847682651d565966c45');
        $product = new Product($article);

        $productRelation = $this->productRelationService();
        $productAttributes = $productRelation->getAttributes($product);

        $this->assertEquals($title, $productAttributes[$key]->getAttribute()->getTitle());
        $this->assertEquals($value, $productAttributes[$key]->getValue());
    }

    public static function getProductAttributesContentDataProvider(): array
    {
        return [
            [
                '0',
                '9438ac75bac3e344628b14bf7ed82c15',
                'Farbe',
                'Schwarz',
            ],
            [
                '1',
                '9438ac75bac3e344628b14bf7ed82c15',
                'Color',
                'Black',
            ],
            [
                '1',
                '943d32fd45d6eba3e5c8cce511cc0e74',
                'Size',
                'W 34/L 34',
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
                $this->createStub(Authorization::class),
                new ProductInfrastructure()
            ),
            new CategoryService(
                $repo,
                $this->createStub(Authorization::class)
            ),
            new ProductInfrastructure()
        );
    }
}
