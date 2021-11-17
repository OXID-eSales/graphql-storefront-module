<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Infrastructure;

use OxidEsales\GraphQL\Storefront\Review\DataType\Review as ReviewDataType;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Language;

final class Review
{
    public function getLanguage(ReviewDataType $review): Language
    {
        $languageId = $review->getEshopModel()->getRawFieldData('oxlang');

        return new Language((int) $languageId);
    }
}
