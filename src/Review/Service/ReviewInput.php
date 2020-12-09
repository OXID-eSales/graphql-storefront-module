<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Service;

use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Review\DataType\Review;
use OxidEsales\GraphQL\Storefront\Review\Exception\RatingOutOfBounds;
use OxidEsales\GraphQL\Storefront\Review\Exception\ReviewInputInvalid;
use OxidEsales\GraphQL\Storefront\Review\Infrastructure\ReviewFactory;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class ReviewInput
{
    /** @var Authentication */
    private $authentication;

    /** @var Repository */
    private $repository;

    /** @var ReviewFactory */
    private $reviewFactory;

    public function __construct(
        Authentication $authentication,
        Repository $repository,
        ReviewFactory $reviewFactory
    ) {
        $this->authentication = $authentication;
        $this->repository     = $repository;
        $this->reviewFactory  = $reviewFactory;
    }

    /**
     * @Factory
     */
    public function fromUserInput(string $productId, ?string $text, ?int $rating): Review
    {
        $this->assertProductIdValue($productId);
        $this->assertRatingValue($rating);

        if (null === $rating && empty($text)) {
            throw ReviewInputInvalid::byWrongValue();
        }

        return $this->reviewFactory->createProductReview(
            $this->authentication->getUserId(),
            $productId,
            (string) $text,
            (string) $rating
        );
    }

    /**
     * @throws RatingOutOfBounds
     *
     * @return true
     */
    private function assertRatingValue(?int $rating): bool
    {
        if (null !== $rating && ($rating < 1 || $rating > 5)) {
            throw RatingOutOfBounds::byWrongValue($rating);
        }

        return true;
    }

    /**
     * @throws ProductNotFound
     *
     * @return true
     */
    private function assertProductIdValue(string $productId): bool
    {
        try {
            $this->repository->getById($productId, Product::class);
        } catch (NotFound $e) {
            throw ProductNotFound::byId($productId);
        }

        return true;
    }
}
