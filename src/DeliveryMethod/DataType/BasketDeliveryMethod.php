<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\DeliveryMethod\DataType;

use OxidEsales\Eshop\Application\Model\Basket as EshopBasketModel;
use OxidEsales\Eshop\Application\Model\DeliverySet as EshopDeliverySetModel;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class BasketDeliveryMethod extends DeliveryMethod
{
    /** @var EshopBasketModel */
    private $basketModel;

    public function __construct(
        EshopDeliverySetModel $deliverySetModel,
        EshopBasketModel $basketModel,
        array $paymentTypes = []
    ) {
        $this->basketModel = $basketModel;

        parent::__construct($deliverySetModel, $paymentTypes);
    }

    public function getBasketModel(): EshopBasketModel
    {
        return $this->basketModel;
    }
}
