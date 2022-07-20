<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use OxidEsales\Eshop\Application\Model\Order as EshopOrderModel;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use OxidEsales\GraphQL\Storefront\Address\DataType\AbstractAddress;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class OrderDeliveryAddress extends AbstractAddress implements ShopModelAwareInterface
{
    private EshopOrderModel $order;

    public function __construct(EshopOrderModel $order)
    {
        $this->order = $order;
        parent::__construct('oxdel');
    }

    public function getEshopModel(): EshopOrderModel
    {
        return $this->order;
    }

    public static function getModelClass(): string
    {
        return EshopOrderModel::class;
    }
}
