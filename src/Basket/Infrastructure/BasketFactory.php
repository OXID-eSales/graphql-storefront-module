<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Infrastructure;

use OxidEsales\Eshop\Application\Model\UserBasket as BasketModel;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;

final class BasketFactory
{
    public function createBasket(string $userId, string $title, bool $public): BasketDataType
    {
        /** @var BasketModel */
        $model = oxNew(BasketModel::class);
        $model->assign([
            'OXUSERID' => $userId,
            'OXTITLE'  => $title,
            'OXPUBLIC' => $public,
        ]);

        return new BasketDataType($model);
    }
}
