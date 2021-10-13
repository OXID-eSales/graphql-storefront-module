<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\DataType;

use OxidEsales\GraphQL\Base\DataType\Filter\StringFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;

final class AddressFilterList extends FilterList
{
    /** @var ?StringFilter */
    private $userId;

    public function __construct(?StringFilter $userId = null)
    {
        $this->userId = $userId;
        parent::__construct();
    }

    public function withUserFilter(StringFilter $user): self
    {
        $filter         = clone $this;
        $filter->userId = $user;

        return $filter;
    }

    /**
     * @return array{
     *                oxuserid: ?StringFilter
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxuserid' => $this->userId,
        ];
    }
}
