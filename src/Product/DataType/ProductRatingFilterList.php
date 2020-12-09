<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Product\DataType;

use OxidEsales\GraphQL\Base\DataType\StringFilter;
use OxidEsales\GraphQL\Catalogue\Shared\DataType\FilterList;

final class ProductRatingFilterList extends FilterList
{
    /** @var null|StringFilter */
    protected $productId;

    public function __construct(
        ?StringFilter $productId = null
    ) {
        $this->productId = $productId;
        parent::__construct();
    }

    /**
     * @return array{
     *                oxobjectid: null|StringFilter
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxobjectid' => $this->productId,
        ];
    }
}
