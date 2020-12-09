<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\Order as EshopOrderModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Order implements DataType
{
    /** @var EshopOrderModel */
    private $order;

    public function __construct(EshopOrderModel $order)
    {
        $this->order = $order;
    }

    public function getEshopModel(): EshopOrderModel
    {
        return $this->order;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->order->getId());
    }

    /**
     * @Field()
     */
    public function getOrderNumber(): int
    {
        return (int) ($this->order->getFieldData('oxordernr'));
    }

    /**
     * @Field()
     */
    public function getInvoiceNumber(): int
    {
        return (int) ($this->order->getInvoiceNum());
    }

    /**
     * @Field()
     */
    public function getPaid(): ?DateTimeInterface
    {
        $paid = (string) $this->order->getFieldData('oxpaid');

        return DateTimeImmutableFactory::fromString($paid);
    }

    /**
     * @Field()
     */
    public function getRemark(): string
    {
        return (string) ($this->order->getFieldData('oxremark'));
    }

    /**
     * @Field()
     */
    public function getCancelled(): bool
    {
        return (bool) ($this->order->getFieldData('oxstorno'));
    }

    /**
     * @Field()
     */
    public function getInvoiced(): ?DateTimeInterface
    {
        $invoiceDate = (string) $this->order->getFieldData('oxbilldate');

        return DateTimeImmutableFactory::fromString($invoiceDate);
    }

    /**
     * @Field()
     */
    public function getOrdered(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->order->getFieldData('oxorderdate')
        );
    }

    /**
     * @Field()
     */
    public function getUpdated(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->order->getFieldData('oxtimestamp')
        );
    }

    public static function getModelClass(): string
    {
        return \OxidEsales\Eshop\Application\Model\Order::class;
    }
}
