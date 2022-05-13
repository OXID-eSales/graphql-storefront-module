<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\DataType;

use OxidEsales\GraphQL\Base\DataType\Filter\BoolFilter;

abstract class FilterList
{
    /** @var ?BoolFilter */
    protected $active;

    public function __construct()
    {
        $this->active = new BoolFilter(true);
    }

    abstract public function getFilters(): array;

    public function withActiveFilter(?BoolFilter $active): self
    {
        $filterList = clone $this;
        $filterList->active = $active;

        return $filterList;
    }

    public function getActive(): ?BoolFilter
    {
        return $this->active;
    }
}
