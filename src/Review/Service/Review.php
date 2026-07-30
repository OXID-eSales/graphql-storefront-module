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
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Review\DataType\Review as ReviewDataType;
use OxidEsales\GraphQL\Storefront\Review\DataType\ReviewFilterList;
use OxidEsales\GraphQL\Storefront\Review\Exception\ReviewAlreadyExists;
use OxidEsales\GraphQL\Storefront\Review\Exception\ReviewNotFound;
use OxidEsales\GraphQL\Storefront\Review\Infrastructure\Repository as ReviewRepository;
use OxidEsales\GraphQL\Storefront\Review\Infrastructure\ReviewFactoryInterface;
use OxidEsales\GraphQL\Storefront\Review\Input\ReviewInputInterface;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface;
use OxidEsales\GraphQL\Base\Service\Authorization;
use TheCodingMachine\GraphQLite\Types\ID;

final class Review
{
    /** @var RepositoryInterface */
    private $repository;

    /** @var ReviewRepository */
    private $reviewRepository;

    /** @var Authentication */
    private $authenticationService;

    /** @var ActivityService */
    private $reviewActivityService;

    /** @var Authorization */
    private $authorizationService;

    /** @var Legacy */
    private $legacyService;

    /** @var ReviewInputValidatorInterface */
    private $reviewInputValidator;

    /** @var ReviewFactoryInterface */
    private $reviewFactory;

    public function __construct(
        RepositoryInterface $repository,
        ReviewRepository $reviewRepository,
        Authentication $authenticationService,
        Authorization $authorizationService,
        ActivityService $reviewActivityService,
        Legacy $legacyService,
        ReviewInputValidatorInterface $reviewInputValidator,
        ReviewFactoryInterface $reviewFactory
    ) {
        $this->repository = $repository;
        $this->reviewRepository = $reviewRepository;
        $this->authenticationService = $authenticationService;
        $this->authorizationService = $authorizationService;
        $this->reviewActivityService = $reviewActivityService;
        $this->legacyService = $legacyService;
        $this->reviewInputValidator = $reviewInputValidator;
        $this->reviewFactory = $reviewFactory;
    }

    /**
     * @throws ProductNotFound
     * @throws ReviewAlreadyExists
     */
    public function set(ReviewInputInterface $input): ReviewDataType
    {
        $this->reviewInputValidator->validateRating($input->getRating());
        $this->reviewInputValidator->validateReviewInput($input);

        try {
            $this->repository->getById($input->getProductId(), Product::class);
        } catch (NotFound $e) {
            throw new ProductNotFound($input->getProductId());
        }

        $review = $this->reviewFactory->createProductReview(
            (string)$this->authenticationService->getUser()->id(),
            $input->getProductId(),
            (string)$input->getText(),
            (string)$input->getRating()
        );

        return $this->save($review);
    }

    /**
     * @throws ReviewNotFound
     * @throws InvalidLogin
     */
    public function review(ID $id): ReviewDataType
    {
        try {
            /** @var ReviewDataType $review */
            $review = $this->repository->getById((string)$id, ReviewDataType::class);
        } catch (NotFound $e) {
            throw new ReviewNotFound((string)$id);
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
     * @return true
     * @throws ReviewNotFound
     *
     * @throws InvalidLogin
     */
    public function delete(ID $id): bool
    {
        if (!((bool)$this->legacyService->getConfigParam('blAllowUsersToManageTheirReviews'))) {
            throw new InvalidLogin('Unauthorized - users are not allowed to manage their reviews');
        }
        $review = $this->review($id);

        //user can delete only its own review, admin can delete any review
        if (
            !$this->authorizationService->isAllowed('DELETE_REVIEW')
            && (string)$this->authenticationService->getUser()->id() !== $review->getReviewerId()
        ) {
            throw new InvalidLogin('Unauthorized');
        }

        return $this->reviewRepository->delete(
            $review
        );
    }

    private function save(ReviewDataType $review): ReviewDataType
    {
        if ($this->reviewRepository->doesReviewExist((string)$this->authenticationService->getUser()->id(), $review)) {
            throw ReviewAlreadyExists::byObjectId($review->getObjectId());
        }

        if ($review->getRating()) {
            $this->reviewRepository->saveRating($review);
        }

        $this->repository->saveModel(
            $review->getEshopModel()
        );

        return $this->repository->getById(
            $review->getEshopModel()->getId(),
            ReviewDataType::class
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
