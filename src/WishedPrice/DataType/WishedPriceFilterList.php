<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\DataType;

use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class WishedPriceFilterList extends FilterList
{
    /** @var ?IDFilter */
    private $userId;

    public function __construct(?IDFilter $userId = null)
    {
        $this->userId = $userId;
        parent::__construct();
    }

    public function withUserFilter(IDFilter $user): self
    {
        $filter         = clone $this;
        $filter->userId = $user;

        return $filter;
    }

    /**
     * @return array{
     *                oxuserid: ?IDFilter
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxuserid' => $this->userId,
        ];
    }

    /**
     * @Factory
     */
    public static function createWishedPriceFilterList(?IDFilter $userId): self
    {
        return new self($userId);
    }
}
