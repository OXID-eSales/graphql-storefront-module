<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\DataType;

use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;

final class BasketItemFilterList extends FilterList
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
     *                oxbasketid: ?IDFilter,
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxbasketid' => $this->basket,
        ];
    }
}
