<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use DateTimeImmutable;
use OxidEsales\Eshop\Application\Model\UserPayment as EshopUserPaymentModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class OrderPayment implements DataType
{
    /** @var EshopUserPaymentModel */
    private $payment;

    public function __construct(EshopUserPaymentModel $payment)
    {
        $this->payment = $payment;
    }

    public function getEshopModel(): EshopUserPaymentModel
    {
        return $this->payment;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->payment->getId());
    }

    /**
     * @Field()
     */
    public function getUpdated(): ?DateTimeImmutable
    {
        $timestamp = $this->payment->getFieldData('oxtimestamp');

        return $timestamp ? DateTimeImmutableFactory::fromString($timestamp) : null;
    }

    public function getPaymentId(): string
    {
        return $this->payment->getFieldData('oxpaymentsid');
    }

    public static function getModelClass(): string
    {
        return EshopUserPaymentModel::class;
    }
}
