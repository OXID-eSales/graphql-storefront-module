<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\DataType;

use OxidEsales\GraphQL\Base\DataType\IDFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;

final class BasketVoucherFilterList extends FilterList
{
    /** @var ?IDFilter */
    private $basket;

    public function __construct(
        ?IDFilter $basket = null
    ) {
        $this->basket = $basket;
        parent::__construct();
    }

    /**
     * @return array{
     *                oegql_basketid: ?IDFilter,
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oegql_basketid' => $this->basket,
        ];
    }
}
