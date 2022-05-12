<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Infrastructure;

use OxidEsales\Eshop\Application\Model\Review as EshopReviewModel;
use OxidEsales\GraphQL\Storefront\Review\DataType\Review as ReviewDataType;

final class ReviewFactory
{
    public function createProductReview(
        string $userId,
        string $productId,
        string $text,
        string $rating
    ): ReviewDataType {
        /** @var EshopReviewModel */
        $model = oxNew(EshopReviewModel::class);
        $model->assign([
            'OXTYPE' => 'oxarticle',
            'OXOBJECTID' => $productId,
            'OXRATING' => $rating,
            'OXUSERID' => $userId,
            'OXTEXT' => $text,
        ]);

        return new ReviewDataType($model);
    }
}
