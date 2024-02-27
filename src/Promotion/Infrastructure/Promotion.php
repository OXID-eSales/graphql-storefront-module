<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Promotion\Infrastructure;

use OxidEsales\Eshop\Application\Model\ActionList;
use OxidEsales\GraphQL\Storefront\Promotion\DataType\Promotion as PromotionDataType;

final class Promotion
{
    /**
     * @return PromotionDataType[]
     */
    public function promotions(): array
    {
        /** @var ActionList $actionList */
        $actionList = oxNew(ActionList::class);
        $actionList->loadCurrent();

        $result = [];

        $promotions = $actionList->getArray();
        if ($promotions) {
            foreach ($promotions as $promotion) {
                $result[] = new PromotionDataType($promotion);
            }
        }

        return $result;
    }
}
