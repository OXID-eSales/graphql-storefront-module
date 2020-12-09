<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Promotion\Controller;

use OxidEsales\GraphQL\Catalogue\Promotion\DataType\Promotion as PromotionDataType;
use OxidEsales\GraphQL\Catalogue\Promotion\Service\Promotion as PromotionService;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class Promotion
{
    /** @var PromotionService */
    private $promotionService;

    public function __construct(
        PromotionService $promotionService
    ) {
        $this->promotionService = $promotionService;
    }

    /**
     * @Query()
     */
    public function promotion(string $id): PromotionDataType
    {
        return $this->promotionService->promotion($id);
    }

    /**
     * @Query()
     *
     * @return PromotionDataType[]
     */
    public function promotions(): array
    {
        return $this->promotionService->promotions();
    }
}
