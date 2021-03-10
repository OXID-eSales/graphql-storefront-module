<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Voucher\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\Voucher as EshopVoucherModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Voucher implements DataType
{
    /** @var EshopVoucherModel */
    private $voucherModel;

    public function __construct(EshopVoucherModel $voucherModel)
    {
        $this->voucherModel = $voucherModel;
    }

    public function getEshopModel(): EshopVoucherModel
    {
        return $this->voucherModel;
    }

    /**
     * @Field
     */
    public function id(): ID
    {
        return new ID($this->getEshopModel()->getId());
    }

    /**
     * @Field
     */
    public function voucher(): string
    {
        return $this->getEshopModel()->getFieldData('OXVOUCHERNR');
    }

    /**
     * @Field
     */
    public function number(): string
    {
        return (string) $this->getEshopModel()->getFieldData('OXVOUCHERNR');
    }

    /**
     * @Field
     */
    public function reserved(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromTimeStamp(
            (int) $this->getEshopModel()->getFieldData('OXRESERVED')
        );
    }

    /**
     * @Field
     */
    public function discount(): ?float
    {
        return $this->getEshopModel()->getFieldData('OXORDERID') ? (float) $this->getEshopModel()->getFieldData('OXDISCOUNT') : null;
    }

    /**
     * @Field()
     */
    public function redeemedAt(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->getEshopModel()->getFieldData('OXDATEUSED')
        );
    }

    public function seriesId(): ID
    {
        return new ID($this->getEshopModel()->getFieldData('OXVOUCHERSERIEID'));
    }

    public static function getModelClass(): string
    {
        return EshopVoucherModel::class;
    }
}
