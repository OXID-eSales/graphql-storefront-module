<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Service;

use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Storefront\Review\DataType\Review;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=Review::class)
 */
final class ActivityService
{
    /** @var Legacy */
    private $legacyService;

    public function __construct(Legacy $legacyService)
    {
        $this->legacyService = $legacyService;
    }

    /**
     * @Field()
     */
    public function isActive(Review $review): bool
    {
        $reviewModel = $review->getEshopModel();
        $moderationIsActive = (bool)$this->legacyService->getConfigParam('blGBModerate');
        $reviewActiveFieldValue = (bool)$reviewModel->getRawFieldData('oxactive');

        return $reviewActiveFieldValue || !$moderationIsActive;
    }
}
