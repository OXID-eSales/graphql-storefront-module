<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\Service;

use OxidEsales\GraphQL\Base\DataType\Filter\StringFilter;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductRating;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

/**
 * @deprecated not used for storefront, we keep the code and later move it to admin
 * module
 */
final class ProductRatingRelationService
{
    /** @var Repository */
    private $repository;

    public function __construct(
        Repository $repository
    ) {
        $this->repository = $repository;
    }

    public function getRatings(ProductRating $rating): array
    {
        return [];
    }
}
