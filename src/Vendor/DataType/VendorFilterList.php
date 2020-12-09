<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Vendor\DataType;

use OxidEsales\GraphQL\Base\DataType\BoolFilter;
use OxidEsales\GraphQL\Base\DataType\StringFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class VendorFilterList extends FilterList
{
    /** @var ?StringFilter */
    private $title;

    public function __construct(
        ?StringFilter $title = null,
        ?BoolFilter $active = null
    ) {
        $this->title  = $title;
        $this->active = $active;
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
     * @Factory(name="VendorFilterList")
     */
    public static function createVendorFilterList(
        ?StringFilter $title = null
    ): self {
        return new self(
            $title
        );
    }
}
