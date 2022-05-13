<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use OxidEsales\Eshop\Application\Model\Order as EshopOrderModel;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class OrderProductBruttoSum implements ShopModelAwareInterface
{
    /** @var EshopOrderModel */
    private $order;

    public function __construct(
        EshopOrderModel $order
    ) {
        $this->order = $order;
    }

    public function getEshopModel(): EshopOrderModel
    {
        return $this->order;
    }

    /**
     * @Field()
     */
    public function getSum(): float
    {
        return (float)($this->order->getRawFieldData('oxtotalbrutsum'));
    }

    public static function getModelClass(): string
    {
        return EshopOrderModel::class;
    }
}
