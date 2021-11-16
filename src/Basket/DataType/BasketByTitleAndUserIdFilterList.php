<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\DataType;

use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Base\DataType\Filter\StringFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;

final class BasketByTitleAndUserIdFilterList extends FilterList
{
    /** @var ?StringFilter */
    private $basket;

    /** @var ?IDFilter */
    private $user;

    public function __construct(
        ?StringFilter $basket = null,
        ?IDFilter $user = null
    ) {
        $this->basket = $basket;
        $this->user   = $user;
        parent::__construct();
    }

    /**
     * @return array{
     *                oxtitle: ?StringFilter,
     *                oxuserid: ?IDFilter
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxtitle'  => $this->basket,
            'oxuserid' => $this->user,
        ];
    }
}
