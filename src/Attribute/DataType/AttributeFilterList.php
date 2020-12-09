<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Attribute\DataType;

use OxidEsales\GraphQL\Base\DataType\StringFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class AttributeFilterList extends FilterList
{
    /** @var null|StringFilter */
    protected $title;

    public function __construct(
        ?StringFilter $title = null
    ) {
        $this->title  = $title;
        $this->active = null;
    }

    /**
     * @return array{
     *                oxtitle: null|StringFilter
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxtitle' => $this->title,
        ];
    }

    /**
     * @Factory(name="AttributeFilterList")
     */
    public static function createAttributeFilterList(
        ?StringFilter $title = null
    ): self {
        return new self(
            $title
        );
    }
}
