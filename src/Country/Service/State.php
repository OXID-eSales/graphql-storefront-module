<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Country\Service;

use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Country\DataType\State as StateDataType;
use OxidEsales\GraphQL\Storefront\Country\DataType\StateFilterList;
use OxidEsales\GraphQL\Storefront\Country\DataType\StateSorting;
use OxidEsales\GraphQL\Storefront\Country\Exception\StateNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

final class State
{
    /** @var Repository */
    private $repository;

    public function __construct(
        Repository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @return StateDataType[]
     */
    public function states(
        StateFilterList $filter,
        StateSorting $sorting
    ): array {
        return $this->repository->getList(
            StateDataType::class,
            $filter,
            new PaginationFilter(),
            $sorting
        );
    }

    public function state(string $id): StateDataType
    {
        try {
            /** @var StateDataType $state */
            $state = $this->repository->getById(
                $id,
                StateDataType::class,
                false
            );
        } catch (NotFound $e) {
            throw new StateNotFound($id);
        }

        return $state;
    }
}
