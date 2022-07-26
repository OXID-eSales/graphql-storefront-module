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
use OxidEsales\GraphQL\Storefront\Shared\Service\AbstractActiveFilterService;
use TheCodingMachine\GraphQLite\Types\ID;

final class Product extends AbstractActiveFilterService
{
    /**
     * @throws ProductNotFound
     * @throws InvalidLogin
     */
    public function product(ID $id): ProductDataType
    {
        try {
            /** @var ProductDataType $product */
            $product = $this->repository->getById((string)$id, ProductDataType::class);
        } catch (NotFound $e) {
            throw ProductNotFound::byId((string)$id);
        }

        if ($product->isActive()) {
            return $product;
        }

        if ($this->authorizationService->isAllowed($this->getInactivePermission())) {
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
        $this->setActiveFilter($filter);

        return $this->repository->getList(
            ProductDataType::class,
            $filter,
            $pagination ?? new PaginationFilter(),
            $sort
        );
    }

    protected function getInactivePermission(): string
    {
        return 'VIEW_INACTIVE_PRODUCT';
    }
}
