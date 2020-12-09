<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Review\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Catalogue\Product\DataType\Product;
use OxidEsales\GraphQL\Catalogue\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Catalogue\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Catalogue\Review\DataType\Review;
use OxidEsales\GraphQL\Catalogue\Review\DataType\Reviewer;
use OxidEsales\GraphQL\Catalogue\Review\Infrastructure\Review as ReviewInfrastructure;
use OxidEsales\GraphQL\Catalogue\Review\Service\Reviewer as ReviewerService;
use OxidEsales\GraphQL\Catalogue\Shared\DataType\Language;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=Review::class)
 */
final class RelationService
{
    /** @var ProductService */
    private $productService;

    /** @var ReviewerService */
    private $reviewerService;

    /** @var ReviewInfrastructure */
    private $reviewInfrastructure;

    public function __construct(
        ProductService $productService,
        ReviewerService $reviewerService,
        ReviewInfrastructure $reviewInfrastructure
    ) {
        $this->productService          = $productService;
        $this->reviewerService         = $reviewerService;
        $this->reviewInfrastructure    = $reviewInfrastructure;
    }

    /**
     * @Field()
     */
    public function getReviewer(Review $review): ?Reviewer
    {
        $reviewerId = (string) $review->getReviewerId();

        return $this->reviewerService->reviewer($reviewerId);
    }

    /**
     * @Field()
     */
    public function getProduct(Review $review): ?Product
    {
        if (!$review->isArticleType()) {
            return null;
        }

        try {
            /** @var Product */
            return $this->productService->product($review->getObjectId());
        } catch (ProductNotFound | InvalidLogin $e) {
            return null;
        }
    }

    /**
     * @Field()
     */
    public function getLanguage(Review $review): Language
    {
        return $this->reviewInfrastructure->getLanguage($review);
    }
}
