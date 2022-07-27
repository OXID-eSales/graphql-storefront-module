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
final class OrderCost extends AbstractOrderDataType
{
    /**
     * @Field()
     */
    public function getTotal(): float
    {
        return (float)$this->order->getRawFieldData('oxtotalordersum');
    }

    /**
     * @Field()
     */
    public function getVoucher(): float
    {
        return (float)$this->order->getRawFieldData('oxvoucherdiscount');
    }

    /**
     * @Field()
     */
    public function getDiscount(): float
    {
        return (float)$this->order->getRawFieldData('oxdiscount');
    }
}
