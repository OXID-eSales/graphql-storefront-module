<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\Infrastructure;

use OxidEsales\Eshop\Application\Model\DeliverySet as EshopDeliveryProviderModel;
use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryProvider;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderDelivery as OrderDeliveryDataType;

final class OrderDelivery
{
    public function getDeliveryProvider(OrderDeliveryDataType $orderDelivery): DeliveryProvider
    {
        /** @var EshopDeliveryProviderModel $deliverySet */
        $deliverySet = $orderDelivery->getEshopModel()->getDelSet();

        return new DeliveryProvider($deliverySet);
    }
}
