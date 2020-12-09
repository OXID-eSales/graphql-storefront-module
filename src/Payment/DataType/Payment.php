<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Payment\DataType;

use DateTimeImmutable;
use OxidEsales\Eshop\Application\Model\Payment as EshopPaymentModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type
 * @extendable-dataType
 */
class Payment implements DataType
{
    /** @var EshopPaymentModel */
    private $payment;

    public function __construct(EshopPaymentModel $payment)
    {
        $this->payment = $payment;
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
    public function isActive(): bool
    {
        return (bool) $this->payment->getFieldData('oxactive');
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return (string) $this->payment->getFieldData('oxdesc');
    }

    /**
     * @Field()
     */
    public function getDescription(): string
    {
        return (string) $this->payment->getFieldData('oxlongdesc');
    }

    /**
     * @Field()
     */
    public function getUpdated(): ?DateTimeImmutable
    {
        return DateTimeImmutableFactory::fromString($this->payment->getFieldData('oxtimestamp'));
    }

    public function getEshopModel(): EshopPaymentModel
    {
        return $this->payment;
    }

    public static function getModelClass(): string
    {
        return EshopPaymentModel::class;
    }
}
