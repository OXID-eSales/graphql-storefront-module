<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Review\DataType\Review as ReviewDataType;
use OxidEsales\GraphQL\Storefront\Review\DataType\ReviewFilterList;
use OxidEsales\GraphQL\Storefront\Review\Exception\ReviewAlreadyExists;
use OxidEsales\GraphQL\Storefront\Review\Exception\ReviewNotFound;
use OxidEsales\GraphQL\Storefront\Review\Infrastructure\Repository as ReviewRepository;
use OxidEsales\GraphQL\Storefront\Review\Service\Review as ReviewService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

final class Review
{
    /** @var Repository */
    private $repository;

    /** @var ReviewRepository */
    private $reviewRepository;

    /** @var ReviewService */
    private $reviewService;

    /** @var Authentication */
    private $authenticationService;

    /** @var ActivityService */
    private $reviewActivityService;

    /** @var Authorization */
    private $authorizationService;

    /** @var Legacy */
    private $legacyService;

    public function __construct(
        Repository $repository,
        ReviewRepository $reviewRepository,
        ReviewService $reviewService,
        Authentication $authenticationService,
        Authorization $authorizationService,
        ActivityService $reviewActivityService,
        Legacy $legacyService
    ) {
        $this->repository            = $repository;
        $this->reviewRepository      = $reviewRepository;
        $this->reviewService         = $reviewService;
        $this->authenticationService = $authenticationService;
        $this->authorizationService  = $authorizationService;
        $this->reviewActivityService = $reviewActivityService;
        $this->legacyService         = $legacyService;
    }

    /**
     * @throws ReviewNotFound
     * @throws InvalidLogin
     */
    public function review(string $id): ReviewDataType
    {
        try {
            /** @var ReviewDataType $review */
            $review = $this->repository->getById($id, ReviewDataType::class);
        } catch (NotFound $e) {
            throw ReviewNotFound::byId($id);
        }

        if ($this->reviewActivityService->isActive($review)) {
            return $review;
        }

        if (!$this->authorizationService->isAllowed('VIEW_INACTIVE_REVIEW')) {
            throw new InvalidLogin('Unauthorized');
        }

        return $review;
    }

    /**
     * @throws InvalidLogin
     * @throws ReviewNotFound
     *
     * @return true
     */
    public function delete(string $id): bool
    {
        if (!((bool) $this->legacyService->getConfigParam('blAllowUsersToManageTheirReviews'))) {
            throw new InvalidLogin('Unauthorized - users are not allowed to manage their reviews');
        }
        $review = $this->reviewService->review($id);

        //user can delete only its own review, admin can delete any review
        if (
            !$this->authorizationService->isAllowed('DELETE_REVIEW')
            && $this->authenticationService->getUserId() !== $review->getReviewerId()
        ) {
            throw new InvalidLogin('Unauthorized');
        }

        return $this->reviewRepository->delete(
            $review
        );
    }

    /**
     * @return true
     */
    public function save(ReviewDataType $review): bool
    {
        if ($this->reviewRepository->doesReviewExist($this->authenticationService->getUserId(), $review)) {
            throw ReviewAlreadyExists::byObjectId($review->getObjectId());
        }

        if ($review->getRating()) {
            $this->reviewRepository->saveRating($review);
        }

        return $this->repository->saveModel(
            $review->getEshopModel()
        );
    }

    /**
     * @return ReviewDataType[]
     */
    public function reviews(ReviewFilterList $filter): array
    {
        // `oxactive` field is not used, therefore with no active filter
        return $this->repository->getByFilter(
            $filter->withActiveFilter(null),
            ReviewDataType::class
        );
    }
}
