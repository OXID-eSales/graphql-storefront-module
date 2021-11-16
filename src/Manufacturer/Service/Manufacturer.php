<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Manufacturer\Service;

use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Manufacturer\DataType\Manufacturer as ManufacturerDataType;
use OxidEsales\GraphQL\Storefront\Manufacturer\DataType\ManufacturerFilterList;
use OxidEsales\GraphQL\Storefront\Manufacturer\DataType\Sorting;
use OxidEsales\GraphQL\Storefront\Manufacturer\Exception\ManufacturerNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use TheCodingMachine\GraphQLite\Types\ID;

final class Manufacturer
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
     * @throws ManufacturerNotFound
     * @throws InvalidLogin
     */
    public function manufacturer(ID $id): ManufacturerDataType
    {
        try {
            /** @var ManufacturerDataType $manufacturer */
            $manufacturer = $this->repository->getById(
                (string) $id,
                ManufacturerDataType::class
            );
        } catch (NotFound $e) {
            throw ManufacturerNotFound::byId((string) $id);
        }

        if ($manufacturer->isActive()) {
            return $manufacturer;
        }

        if ($this->authorizationService->isAllowed('VIEW_INACTIVE_MANUFACTURER')) {
            return $manufacturer;
        }

        throw new InvalidLogin('Unauthorized');
    }

    /**
     * @return ManufacturerDataType[]
     */
    public function manufacturers(
        ManufacturerFilterList $filter,
        Sorting $sort
    ): array {
        // In case user has VIEW_INACTIVE_MANUFACTURER permissions
        // return all manufacturers including inactive ones
        if ($this->authorizationService->isAllowed('VIEW_INACTIVE_MANUFACTURER')) {
            $filter = $filter->withActiveFilter(null);
        }

        return $this->repository->getList(
            ManufacturerDataType::class,
            $filter,
            new PaginationFilter(),
            $sort
        );
    }
}
