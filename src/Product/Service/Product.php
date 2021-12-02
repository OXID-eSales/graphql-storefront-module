<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\Service;

use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Base\DataType\Sorting\Sorting as BaseSorting;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product as ProductDataType;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductFilterList;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use TheCodingMachine\GraphQLite\Types\ID;

final class Product
{
    /** @var Repository */
    private $repository;

    /** @var Authorization */
    private $authorizationService;

    public function __construct(
        Repository $repository,
        Authorization $authorizationService
    ) {
        $this->repository           = $repository;
        $this->authorizationService = $authorizationService;
    }

    /**
     * @throws ProductNotFound
     * @throws InvalidLogin
     */
    public function product(ID $id): ProductDataType
    {
        try {
            /** @var ProductDataType $product */
            $product = $this->repository->getById((string) $id, ProductDataType::class);
        } catch (NotFound $e) {
            throw ProductNotFound::byId((string) $id);
        }

        if ($product->isActive()) {
            return $product;
        }

        if ($this->authorizationService->isAllowed('VIEW_INACTIVE_PRODUCT')) {
            return $product;
        }

        throw new InvalidLogin('Unauthorized');
    }

    /**
     * @return ProductDataType[]
     */
    public function products(
        ProductFilterList $filter,
        ?PaginationFilter $pagination,
        BaseSorting $sort
    ): array {
        // In case user has VIEW_INACTIVE_PRODUCT permissions
        // return all products including inactive ones
        if ($this->authorizationService->isAllowed('VIEW_INACTIVE_PRODUCT')) {
            $filter = $filter->withActiveFilter(null);
        }

        return $this->repository->getList(
            ProductDataType::class,
            $filter,
            $pagination ?? new PaginationFilter(),
            $sort
        );
    }
}
