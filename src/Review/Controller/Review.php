<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Controller;

use OxidEsales\GraphQL\Storefront\Review\DataType\Review as ReviewDataType;
use OxidEsales\GraphQL\Storefront\Review\Service\Review as ReviewService;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\HideIfUnauthorized;
use TheCodingMachine\GraphQLite\Types\ID;

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
    public function review(ID $reviewId): ReviewDataType
    {
        return $this->reviewService->review($reviewId);
    }

    /**
     * @Mutation()
     * @Logged()
     * @HideIfUnauthorized()
     */
    public function reviewSet(ReviewDataType $review): ReviewDataType
    {
        $this->reviewService->save($review);

        return $review;
    }

    /**
     * @Mutation()
     * @Logged()
     * @HideIfUnauthorized()
     */
    public function reviewDelete(ID $reviewId): bool
    {
        return $this->reviewService->delete($reviewId);
    }
}
