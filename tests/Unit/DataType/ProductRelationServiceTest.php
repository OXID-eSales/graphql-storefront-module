<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\DataType;

use OxidEsales\Eshop\Application\Model\Article as EshopArticleModel;
use OxidEsales\Eshop\Application\Model\Category as EshopCategoryModel;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Storefront\Category\Service\Category as CategoryService;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\Infrastructure\Product as ProductInfrastructure;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Storefront\Product\Service\RelationService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use PHPUnit\Framework\TestCase;

/**
 * @covers OxidEsales\GraphQL\Storefront\Product\Service\RelationService
 */
final class ProductRelationServiceTest extends TestCase
{
    public function testGetNoCategoryIfNotAssignedToProduct(): void
    {
        $noCategoryProductModelStub = new class () extends EshopArticleModel {
            public function __construct()
            {
            }

            public function getCategory()
            {
                return null;
            }
        };

        $this->assertEquals(
            $this->productRelationService()->getCategories(
                new Product(
                    $noCategoryProductModelStub
                )
            ),
            []
        );
    }

    public function testGetNoCategoryIfEmptyCategoryAssignedToProduct(): void
    {
        $emptyCategoryProductModelStub = new class () extends EshopArticleModel {
            public function __construct()
            {
            }

            public function getCategory()
            {
                return new class () extends EshopCategoryModel {
                    public function __construct()
                    {
                    }

                    public function getId()
                    {
                        return '';
                    }
                };
            }
        };

        $this->assertEquals(
            $this->productRelationService()->getCategories(
                new Product(
                    $emptyCategoryProductModelStub
                )
            ),
            []
        );
    }

    private function productRelationService(): RelationService
    {
        $repo = new Repository(
            $this->createMock(QueryBuilderFactoryInterface::class)
        );

        return new RelationService(
            new ProductService(
                $repo,
                new Authorization()
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
