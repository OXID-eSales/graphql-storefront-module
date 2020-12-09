<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Product\Service;

use OxidEsales\GraphQL\Base\DataType\StringFilter;
use OxidEsales\GraphQL\Catalogue\Product\DataType\ProductRating;
use OxidEsales\GraphQL\Catalogue\Shared\Infrastructure\Repository;

/**
 * @deprecated not used for catalogue, we keep the code and later move it to admin
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
        /*
        return $this->repository->getByFilter(
            new ProductRatingFilterList(
                new StringFilter((string)$rating->getEshopModel()->getId())
            ),
            Rating::class
        ); */
        return [];
    }
}
