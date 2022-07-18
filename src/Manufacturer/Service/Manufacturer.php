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
use OxidEsales\GraphQL\Storefront\Manufacturer\DataType\Manufacturer as ManufacturerDataType;
use OxidEsales\GraphQL\Storefront\Manufacturer\DataType\ManufacturerFilterList;
use OxidEsales\GraphQL\Storefront\Manufacturer\DataType\Sorting;
use OxidEsales\GraphQL\Storefront\Manufacturer\Exception\ManufacturerNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Service\AbstractActiveFilterService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use TheCodingMachine\GraphQLite\Types\ID;

final class Manufacturer extends AbstractActiveFilterService
{
    public function __construct(
        Repository $repository,
        Authorization $authorizationService
    ) {
        parent::__construct($repository, $authorizationService);
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
                (string)$id,
                ManufacturerDataType::class
            );
        } catch (NotFound $e) {
            throw ManufacturerNotFound::byId((string)$id);
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
        $this->setActiveFilter($filter);

        return $this->repository->getList(
            ManufacturerDataType::class,
            $filter,
            new PaginationFilter(),
            $sort
        );
    }

    protected function getInactivePermission(): string
    {
        return 'VIEW_INACTIVE_MANUFACTURER';
    }
}
