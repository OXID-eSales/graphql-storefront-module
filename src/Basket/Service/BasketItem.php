<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Service;

use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketItem as BasketItemDataType;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketItemFilterList;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

final class BasketItem
{
    /** @var Repository */
    private $repository;

    public function __construct(
        Repository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @return BasketItemDataType[]
     */
    public function basketItems(BasketItemFilterList $filter, ?PaginationFilter $pagination = null): array
    {
        return $this->repository->getByFilter(
            $filter,
            BasketItemDataType::class,
            $pagination
        );
    }
}
