<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Service;

use OxidEsales\GraphQL\Storefront\Review\Exception\RatingOutOfBounds;
use OxidEsales\GraphQL\Storefront\Review\Exception\ReviewInputInvalid;
use OxidEsales\GraphQL\Storefront\Review\Input\ReviewInputInterface;

interface ReviewInputValidatorInterface
{
    /**
     * @throws RatingOutOfBounds
     */
    public function validateRating(?int $rating): void;

    /**
     * @throws ReviewInputInvalid
     */
    public function validateReviewInput(ReviewInputInterface $input): void;
}
