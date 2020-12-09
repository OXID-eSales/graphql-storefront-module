<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\Service;

use OxidEsales\GraphQL\Storefront\Order\DataType\OrderPayment;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderPaymentValue;
use OxidEsales\GraphQL\Storefront\Order\Infrastructure\OrderPayment as OrderPaymentInfrastructure;
use OxidEsales\GraphQL\Storefront\Payment\DataType\Payment;
use OxidEsales\GraphQL\Storefront\Payment\Exception\PaymentNotFound;
use OxidEsales\GraphQL\Storefront\Payment\Service\Payment as PaymentService;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=OrderPayment::class)
 */
final class OrderPaymentRelations
{
    /** @var PaymentService */
    private $paymentService;

    /** @var OrderPaymentInfrastructure */
    private $orderPaymentInfrastructure;

    public function __construct(
        PaymentService $paymentService,
        OrderPaymentInfrastructure $orderPaymentInfrastructure
    ) {
        $this->paymentService             = $paymentService;
        $this->orderPaymentInfrastructure = $orderPaymentInfrastructure;
    }

    /**
     * @Field()
     */
    public function getPayment(OrderPayment $orderPayment): ?Payment
    {
        try {
            return $this->paymentService->payment(
                $orderPayment->getPaymentId()
            );
        } catch (PaymentNotFound $e) {
            return null;
        }
    }

    /**
     * @Field()
     *
     * @return OrderPaymentValue[]
     */
    public function getValues(OrderPayment $orderPayment): array
    {
        return $this->orderPaymentInfrastructure->getPaymentValues($orderPayment);
    }
}
