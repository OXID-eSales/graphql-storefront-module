<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Infrastructure;

use OxidEsales\GraphQL\Storefront\Review\DataType\Review as ReviewDataType;

interface ReviewFactoryInterface
{
    public function createProductReview(
        string $userId,
        string $productId,
        string $text,
        string $rating
    ): ReviewDataType;
}
