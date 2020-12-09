<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Review\Infrastructure;

use OxidEsales\GraphQL\Catalogue\Review\DataType\Review as ReviewDataType;
use OxidEsales\GraphQL\Catalogue\Shared\DataType\Language;

final class Review
{
    public function getLanguage(ReviewDataType $review): Language
    {
        $languageId = $review->getEshopModel()->getFieldData('oxlang');

        return new Language((int) $languageId);
    }
}
