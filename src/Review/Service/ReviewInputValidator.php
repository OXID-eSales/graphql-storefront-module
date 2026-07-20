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

class ReviewInputValidator implements ReviewInputValidatorInterface
{
    public function validateRating(?int $rating): void
    {
        if (null !== $rating && ($rating < 1 || $rating > 5)) {
            throw RatingOutOfBounds::byWrongValue($rating);
        }
    }

    public function validateReviewInput(ReviewInputInterface $input): void
    {
        if (null === $input->getRating() && empty($input->getText())) {
            throw ReviewInputInvalid::byWrongValue();
        }
    }
}
