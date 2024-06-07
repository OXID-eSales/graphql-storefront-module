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
use OxidEsales\GraphQL\Storefront\Product\Infrastructure\Product as ProductInfrastructure;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductFilterList;
use OxidEsales\GraphQL\Storefront\Product\DataType\VariantSelections;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Service\AbstractActiveFilterService;
use OxidEsales\GraphQL\Base\Service\Authorization;
use TheCodingMachine\GraphQLite\Types\ID;

final class Product extends AbstractActiveFilterService
{
    /** @var ProductInfrastructure */
    private $productInfrastructure;

    public function __construct(
        Repository $repository,
        Authorization $authorizationService,
        ProductInfrastructure $productInfrastructure
    ) {
        parent::__construct($repository, $authorizationService);

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
            throw new ProductNotFound((string)$id);
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

    /**
     * @param string $productId
     * @param string[]|null $varSelIds
     * @return ?VariantSelections
     * @throws InvalidLogin
     * @throws ProductNotFound
     */
    public function variantSelections(string $productId, ?array $varSelIds): ?VariantSelections
    {
        try {
            $product = $this->productInfrastructure->getParentById($productId);
        } catch (NotFound $e) {
            throw new ProductNotFound($productId);
        }

        $childId = null;

        if ($product->getEshopModel()->getId() !== $productId) {
            $childId = $productId;
        }

        $this->productInfrastructure->setLoadVariants();

        if ($product->isActive() || $this->authorizationService->isAllowed('VIEW_INACTIVE_PRODUCT')) {
            $variantSelections = $product->getEshopModel()->getVariantSelections($varSelIds, $childId);
            if ($variantSelections) {
                return new VariantSelections($variantSelections);
            }

            return null;
        }

        throw new InvalidLogin('Unauthorized');
    }

    protected function getInactivePermission(): string
    {
        return 'VIEW_INACTIVE_PRODUCT';
    }
}
