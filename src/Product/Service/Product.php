<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\Service;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Base\DataType\Sorting\Sorting as BaseSorting;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product as ProductDataType;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductFilterList;
use OxidEsales\GraphQL\Storefront\Product\DataType\VariantSelections;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Product\Infrastructure\Product as ProductInfrastructure;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use TheCodingMachine\GraphQLite\Types\ID;

final class Product
{
    /** @var Repository */
    private $repository;

    /** @var Authorization */
    private $authorizationService;

    /** @var ProductInfrastructure */
    private $productInfrastructure;

    public function __construct(
        Repository $repository,
        Authorization $authorizationService,
        ProductInfrastructure $productInfrastructure
    ) {
        $this->repository = $repository;
        $this->authorizationService = $authorizationService;
        $this->productInfrastructure = $productInfrastructure;
    }

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

    /**
     * @param string $productId
     * @param string[]|null $varSelids
     * @return ?VariantSelections
     * @throws InvalidLogin
     * @throws ProductNotFound
     */
    public function variantSelections(string $productId, ?array $varSelids): ?VariantSelections
    {
        try {
            $product = $this->productInfrastructure->getParentById($productId);
        } catch (NotFound $e) {
            throw ProductNotFound::byId($productId);
        }

        $childId = null;

        if ($product->getEshopModel()->getId() !== $productId) {
            $childId = $productId;
        }

        Registry::getConfig()->setConfigParam('blLoadVariants', true);

        if ($product->isActive() || $this->authorizationService->isAllowed('VIEW_INACTIVE_PRODUCT'))
        {
            if ($variantSelections = $product->getEshopModel()->getVariantSelections($varSelids, $childId)) {
                return new VariantSelections($variantSelections);
            }

            return null;
        }

        throw new InvalidLogin('Unauthorized');
    }
}
