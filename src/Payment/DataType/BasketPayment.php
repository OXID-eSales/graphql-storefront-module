<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Payment\DataType;

use OxidEsales\Eshop\Application\Model\Basket as EshopBasketModel;
use OxidEsales\Eshop\Application\Model\Payment as EshopPaymentModel;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class BasketPayment extends Payment
{
    /** @var EshopBasketModel */
    private $basketModel;

    public function __construct(
        EshopPaymentModel $payment,
        EshopBasketModel $basketModel
    ) {
        $this->basketModel = $basketModel;

        parent::__construct($payment);
    }

    public function getBasketModel(): EshopBasketModel
    {
        return $this->basketModel;
    }
}
