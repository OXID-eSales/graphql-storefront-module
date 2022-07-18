<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Service;

use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

abstract class AbstractActiveFilterService
{
    /** @var Repository */
    protected $repository;

    /** @var Authorization */
    protected $authorizationService;

    public function __construct(
        Repository $repository,
        Authorization $authorizationService
    ) {
        $this->repository = $repository;
        $this->authorizationService = $authorizationService;
    }

    protected function setActiveFilter(FilterList &$filter): void
    {
        if ($this->authorizationService->isAllowed($this->getInactivePermission())) {
            $filter = $filter->withActiveFilter(null);
        }
    }

    abstract protected function getInactivePermission(): string;
}
