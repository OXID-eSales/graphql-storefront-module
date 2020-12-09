<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Review\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Catalogue\Review\DataType\Review as ReviewDataType;
use OxidEsales\GraphQL\Catalogue\Review\Exception\ReviewNotFound;
use OxidEsales\GraphQL\Catalogue\Shared\Infrastructure\Repository;

final class Review
{
    /** @var Repository */
    private $repository;

    /** @var Authorization */
    private $authorizationService;

    /** @var ActivityService */
    private $reviewActivityService;

    public function __construct(
        Repository $repository,
        Authorization $authorizationService,
        ActivityService $reviewActivityService
    ) {
        $this->repository            = $repository;
        $this->authorizationService  = $authorizationService;
        $this->reviewActivityService = $reviewActivityService;
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
}
