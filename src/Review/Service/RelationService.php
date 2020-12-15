<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Storefront\Review\DataType\Review;
use OxidEsales\GraphQL\Storefront\Review\DataType\Reviewer;
use OxidEsales\GraphQL\Storefront\Review\Infrastructure\Review as ReviewInfrastructure;
use OxidEsales\GraphQL\Storefront\Review\Service\Reviewer as ReviewerService;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Language;
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
