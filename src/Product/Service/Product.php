<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\Service;

use OxidEsales\GraphQL\Base\DataType\PaginationFilter;
use OxidEsales\GraphQL\Base\DataType\Sorting;
use OxidEsales\GraphQL\Base\DataType\Pagination;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product as ProductDataType;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductFilterList;
use OxidEsales\GraphQL\Storefront\Product\DataType\Sorting as ProductSorting;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Shared\DataType\SeoSlugFilter;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
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
    public function product(?ID $id, ?string $slug): ProductDataType
    {
        if ((!$id && !$slug) || ($id && $slug)) {
            throw ProductNotFound::byParameter();
        }

        try {
            if ($id) {
                /** @var ProductDataType $product */
                $product = $this->repository->getById((string) $id, ProductDataType::class);
            } else {
                $product = $this->getProductBySeoSlug($slug . '.');
            }
        } catch (ProductNotFound $e) {
            throw $e;
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
        Sorting $sort
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

    /**
     * @throws ProductNotFound
     */
    private function getProductBySeoSlug(string $slug): ProductDataType
    {
        $slugFilter = SeoSlugFilter::fromUserInput($slug);
        $slugFilter->setType('oxarticle');

        $results = $this->repository->getList(
            ProductDataType::class,
            new ProductFilterList (
                null,
                null,
                null,
                null,
                $slugFilter
            ),
            new PaginationFilter(),
            ProductSorting::fromUserInput()
        );

        if (empty($results)) {
            throw ProductNotFound::bySlug($slug);
        }
        if (1 < count($results)) {
            throw ProductNotFound::byAmbiguousBySlug($slug);
        }

        return $results[0];
    }
}
