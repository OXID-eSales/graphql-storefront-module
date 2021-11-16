<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Country\DataType;

use OxidEsales\GraphQL\Base\DataType\Filter\StringFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class CountryFilterList extends FilterList
{
    /** @var ?StringFilter */
    private $title;

    public function __construct(
        ?StringFilter $title = null
    ) {
        $this->title  = $title;
        parent::__construct();
    }

    /**
     * @return array{
     *                oxtitle: ?StringFilter
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxtitle' => $this->title,
        ];
    }

    /**
     * @Factory(name="CountryFilterList", default=true)
     */
    public static function createCountryFilterList(
        ?StringFilter $title = null
    ): self {
        return new self(
            $title
        );
    }
}
