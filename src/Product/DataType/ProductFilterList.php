<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\DataType;

use OxidEsales\GraphQL\Base\DataType\BoolFilter;
use OxidEsales\GraphQL\Base\DataType\IDFilter;
use OxidEsales\GraphQL\Base\DataType\StringFilter;
use OxidEsales\GraphQL\Storefront\Category\DataType\CategoryIDFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;
use TheCodingMachine\GraphQLite\Annotations\Factory;
use TheCodingMachine\GraphQLite\Types\ID;

final class ProductFilterList extends FilterList
{
    /** @var ?StringFilter */
    private $title;

    /** @var ?CategoryIDFilter */
    private $category;

    /** @var ?IDFilter */
    private $manufacturer;

    /** @var ?IDFilter */
    private $vendor;

    /** @var ?IDFilter */
    private $parent;

    public function __construct(
        ?StringFilter $title = null,
        ?CategoryIDFilter $category = null,
        ?IDFilter $manufacturer = null,
        ?IDFilter $vendor = null,
        ?BoolFilter $active = null
    ) {
        $this->title        = $title;
        $this->category     = $category;
        $this->manufacturer = $manufacturer;
        $this->vendor       = $vendor;
        $this->active       = $active;
        $this->parent       = new IDFilter(new ID(''));
        parent::__construct();
    }

    /**
     * @return array{
     *                oxtitle: ?StringFilter,
     *                oxcatnid : ?CategoryIDFilter,
     *                oxmanufacturerid: ?IDFilter,
     *                oxvendorid: ?IDFilter,
     *                oxparentid: ?IDFilter
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxtitle'          => $this->title,
            'oxcatnid'         => $this->category,
            'oxmanufacturerid' => $this->manufacturer,
            'oxvendorid'       => $this->vendor,
            'oxparentid'       => $this->parent,
        ];
    }

    /**
     * @Factory(name="ProductFilterList", default=true)
     */
    public static function createProductFilterList(
        ?StringFilter $title = null,
        ?CategoryIDFilter $category = null,
        ?IDFilter $manufacturer = null,
        ?IDFilter $vendor = null
    ): self {
        return new self($title, $category, $manufacturer, $vendor);
    }
}
