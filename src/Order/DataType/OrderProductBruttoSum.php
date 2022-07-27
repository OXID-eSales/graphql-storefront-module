<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class OrderProductBruttoSum extends AbstractOrderDataType
{
    /**
     * @Field()
     */
    public function getSum(): float
    {
        return (float)($this->order->getRawFieldData('oxtotalbrutsum'));
    }
}
