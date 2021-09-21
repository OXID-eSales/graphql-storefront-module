<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\DataType;

use OxidEsales\Eshop\Application\Model\UserBasket as BasketModel;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class PublicBasket extends AbstractBasket implements DataType
{
    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return BasketModel::class;
    }
}
