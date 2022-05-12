<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Category\Service;

use OxidEsales\GraphQL\Base\DataType\Filter\BoolFilter;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Category\DataType\Category as CategoryDataType;
use OxidEsales\GraphQL\Storefront\Category\DataType\CategoryFilterList;
use OxidEsales\GraphQL\Storefront\Category\DataType\Sorting;
use OxidEsales\GraphQL\Storefront\Category\Exception\CategoryNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use TheCodingMachine\GraphQLite\Types\ID;

final class Category
{
    /** @var Repository */
    private $repository;

    /** @var Authorization */
    private $authorizationService;

    public function __construct(
        Repository $repository,
        Authorization $authorizationService
    ) {
        $this->repository = $repository;
        $this->authorizationService = $authorizationService;
    }

    /**
     * @throws CategoryNotFound
     * @throws InvalidLogin
     */
    public function category(ID $id): CategoryDataType
    {
        try {
            /** @var CategoryDataType $category */
            $category = $this->repository->getById((string)$id, CategoryDataType::class);
        } catch (NotFound $e) {
            throw CategoryNotFound::byId((string)$id);
        }

        if ($category->isActive()) {
            return $category;
        }

        if (!$this->authorizationService->isAllowed('VIEW_INACTIVE_CATEGORY')) {
            throw new InvalidLogin('Unauthorized');
        }

        return $category;
    }

    /**
     * @return CategoryDataType[]
     */
    public function categories(
        CategoryFilterList $filter,
        Sorting $sort
    ): array {
        if (!$this->authorizationService->isAllowed('VIEW_INACTIVE_CATEGORY')) {
            $filter = $filter->withActiveFilter(new BoolFilter(true));
        }

        return $this->repository->getList(
            CategoryDataType::class,
            $filter,
            new PaginationFilter(),
            $sort
        );
    }
}
