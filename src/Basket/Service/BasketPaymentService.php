<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Service;

use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Payment as PaymentInfrastructure;
use OxidEsales\GraphQL\Storefront\Payment\DataType\BasketPayment;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Price;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=BasketPayment::class)
 */
final class BasketPaymentService
{
    /** @var PaymentInfrastructure */
    private $paymentInfrastructure;

    public function __construct(
        PaymentInfrastructure $paymentInfrastructure
    ) {
        $this->paymentInfrastructure = $paymentInfrastructure;
    }

    /**
     * @Field()
     */
    public function cost(BasketPayment $basketPayment): Price
    {
        return $this->paymentInfrastructure->getPaymentCost(
            $basketPayment,
            $basketPayment->getBasketModel()
        );
    }
}
