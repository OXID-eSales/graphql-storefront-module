<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Manufacturer\DataType;

use OxidEsales\GraphQL\Base\DataType\BoolFilter;
use OxidEsales\GraphQL\Base\DataType\StringFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;
use OxidEsales\GraphQL\Storefront\Shared\DataType\SeoSlugFilter;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class ManufacturerFilterList extends FilterList
{
    /** @var ?StringFilter */
    private $title;

    /** @var ?SeoSlugFilter */
    private $slug;

    public function __construct(
        ?StringFilter $title = null,
        ?BoolFilter $active = null,
        ?SeoSlugFilter $slug = null
    ) {
        $this->title    = $title;
        $this->active   = $active;
        $this->slug     = $slug;
        null === $this->slug ?: $this->slug->setType('oxmanufacturer');

        parent::__construct();
    }

    /**
     * @return array{
     *                oxtitle: ?StringFilter
     *                oxseourl: ?SeoSlugFilter,
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxtitle'    => $this->title,
            'oxseourl'   => $this->slug,
        ];
    }

    /**
     * @Factory(name="ManufacturerFilterList")
     */
    public static function createManufacturerFilterList(
        ?StringFilter $title = null,
        ?SeoSlugFilter $slug = null
    ): self {
        return new self(
            $title,
            null,
            $slug
        );
    }
}
