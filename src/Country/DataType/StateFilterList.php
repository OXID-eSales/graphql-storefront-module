<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Country\DataType;

use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class StateFilterList extends FilterList
{
    /** @var null|IDFilter */
    private $country;

    public function __construct(
        ?IDFilter $country = null
    ) {
        $this->country = $country;
        parent::__construct();
    }

    /**
     * @return array{
     *                oxcountryid : ?IDFilter
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxcountryid' => $this->country,
        ];
    }

    /**
     * @Factory(name="StateFilterList", default=true)
     */
    public static function createStateFilterList(
        ?IDFilter $country = null
    ): self {
        return new self($country);
    }
}
