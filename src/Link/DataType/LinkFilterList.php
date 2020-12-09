<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Link\DataType;

use OxidEsales\GraphQL\Base\DataType\BoolFilter;
use OxidEsales\GraphQL\Base\DataType\StringFilter;
use OxidEsales\GraphQL\Catalogue\Shared\DataType\FilterList;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class LinkFilterList extends FilterList
{
    /** @var ?StringFilter */
    private $description;

    public function __construct(
        ?StringFilter $description = null,
        ?BoolFilter $active = null
    ) {
        $this->description = $description;
        $this->active      = $active;
        parent::__construct();
    }

    /**
     * @return array{
     *                oxurldesc: ?StringFilter
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxurldesc' => $this->description,
        ];
    }

    /**
     * @Factory(name="LinkFilterList")
     */
    public static function createLinkFilterList(
        ?StringFilter $description = null
    ): self {
        return new self(
            $description
        );
    }
}
