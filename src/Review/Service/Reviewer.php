<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Service;

use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Review\DataType\Reviewer as ReviewerDataType;
use OxidEsales\GraphQL\Storefront\Review\Exception\ReviewerNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface;

final class Reviewer
{
    /** @var RepositoryInterface */
    private $repository;

    public function __construct(
        RepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @throws ReviewerNotFound
     */
    public function reviewer(string $id): ReviewerDataType
    {
        try {
            /** @var ReviewerDataType $reviewer */
            $reviewer = $this->repository->getById($id, ReviewerDataType::class);
        } catch (NotFound $e) {
            throw new ReviewerNotFound($id);
        }

        return $reviewer;
    }
}
