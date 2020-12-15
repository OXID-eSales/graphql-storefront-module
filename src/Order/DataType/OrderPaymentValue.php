<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use OxidEsales\Eshop\Application\Model\UserPayment as EshopUserPaymentModel;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use stdClass;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class OrderPaymentValue implements DataType
{
    /** @var stdClass */
    private $paymentValue;

    public function __construct(stdClass $paymentValue)
    {
        $this->paymentValue = $paymentValue;
    }

    /**
     * @Field()
     */
    public function getKey(): string
    {
        return (string) $this->paymentValue->name;
    }

    /**
     * @Field()
     */
    public function getValue(): string
    {
        return (string) $this->paymentValue->value;
    }

    public static function getModelClass(): string
    {
        return EshopUserPaymentModel::class;
    }
}
