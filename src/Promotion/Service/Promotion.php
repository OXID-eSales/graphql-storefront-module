<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Promotion\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Promotion\DataType\Promotion as PromotionDataType;
use OxidEsales\GraphQL\Storefront\Promotion\Exception\PromotionNotFound;
use OxidEsales\GraphQL\Storefront\Promotion\Infrastructure\Promotion as PromotionInfrastructure;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use TheCodingMachine\GraphQLite\Types\ID;

final class Promotion
{
    /** @var Repository */
    private $repository;

    /** @var Authorization */
    private $authorizationService;

    /** @var PromotionInfrastructure */
    private $promotionInfrastructure;

    public function __construct(
        Repository $repository,
        Authorization $authorizationService,
        PromotionInfrastructure $promotionInfrastructure
    ) {
        $this->repository              = $repository;
        $this->authorizationService    = $authorizationService;
        $this->promotionInfrastructure = $promotionInfrastructure;
    }

    /**
     * @throws PromotionNotFound
     * @throws InvalidLogin
     */
    public function promotion(ID $id): PromotionDataType
    {
        try {
            /** @var PromotionDataType $promotion */
            $promotion = $this->repository->getById(
                (string) $id,
                PromotionDataType::class
            );
        } catch (NotFound $e) {
            throw PromotionNotFound::byId((string) $id);
        }

        if ($promotion->isActive()) {
            return $promotion;
        }

        if (!$this->authorizationService->isAllowed('VIEW_INACTIVE_PROMOTION')) {
            throw new InvalidLogin('Unauthorized');
        }

        return $promotion;
    }

    /**
     * @return PromotionDataType[]
     */
    public function promotions(): array
    {
        return $this->promotionInfrastructure->promotions();
    }
}
