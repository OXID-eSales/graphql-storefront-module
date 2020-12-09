<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Content\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Category\DataType\Category as CategoryDataType;
use OxidEsales\GraphQL\Storefront\Category\Service\Category as CategoryService;
use OxidEsales\GraphQL\Storefront\Content\DataType\Content;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Seo;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=Content::class)
 */
final class RelationService
{
    /** @var Repository */
    private $repository;

    /** @var CategoryService */
    private $categoryService;

    public function __construct(
        Repository $repository,
        CategoryService $categoryService
    ) {
        $this->repository      = $repository;
        $this->categoryService = $categoryService;
    }

    /**
     * @Field()
     */
    public function getSeo(Content $content): Seo
    {
        return new Seo($content->getEshopModel());
    }

    /**
     * @Field()
     */
    public function getCategory(Content $content): ?CategoryDataType
    {
        $id = (string) $content->getEshopModel()->getCategoryId();

        if (!$id || $content->getEshopModel()->getType() !== Content::TYPE_CATEGORY) {
            return null;
        }

        try {
            return $this->categoryService->category($id);
        } catch (NotFound | InvalidLogin $e) {
            return null;
        }
    }
}
