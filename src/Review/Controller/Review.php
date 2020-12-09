<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Review\Controller;

use OxidEsales\GraphQL\Catalogue\Review\DataType\Review as ReviewDataType;
use OxidEsales\GraphQL\Catalogue\Review\Service\Review as ReviewService;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class Review
{
    /** @var ReviewService */
    private $reviewService;

    public function __construct(
        ReviewService $reviewService
    ) {
        $this->reviewService = $reviewService;
    }

    /**
     * @Query()
     */
    public function review(string $id): ReviewDataType
    {
        return $this->reviewService->review($id);
    }
}
