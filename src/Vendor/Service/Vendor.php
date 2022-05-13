<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Vendor\Service;

use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Vendor\DataType\Sorting;
use OxidEsales\GraphQL\Storefront\Vendor\DataType\Vendor as VendorDataType;
use OxidEsales\GraphQL\Storefront\Vendor\DataType\VendorFilterList;
use OxidEsales\GraphQL\Storefront\Vendor\Exception\VendorNotFound;
use TheCodingMachine\GraphQLite\Types\ID;

final class Vendor
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
     * @throws VendorNotFound
     * @throws InvalidLogin
     */
    public function vendor(ID $id): VendorDataType
    {
        try {
            $vendor = $this->repository->getById(
                (string)$id,
                VendorDataType::class
            );
        } catch (NotFound $e) {
            throw VendorNotFound::byId((string)$id);
        }

        if ($vendor->isActive()) {
            return $vendor;
        }

        if ($this->authorizationService->isAllowed('VIEW_INACTIVE_VENDOR')) {
            return $vendor;
        }

        throw new InvalidLogin('Unauthorized');
    }

    /**
     * @return VendorDataType[]
     */
    public function vendors(
        VendorFilterList $filter,
        Sorting $sort
    ): array {
        // In case user has VIEW_INACTIVE_VENDOR permissions
        // return all vendors including inactive
        if ($this->authorizationService->isAllowed('VIEW_INACTIVE_VENDOR')) {
            $filter = $filter->withActiveFilter(null);
        }

        return $this->repository->getList(
            VendorDataType::class,
            $filter,
            new PaginationFilter(),
            $sort
        );
    }
}
