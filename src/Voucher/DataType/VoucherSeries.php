<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Voucher\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\VoucherSerie as EshopVoucherModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class VoucherSeries implements ShopModelAwareInterface
{
    /** @var EshopVoucherModel */
    private $voucherSeriesModel;

    public function __construct(
        EshopVoucherModel $voucherSeriesModel
    ) {
        $this->voucherSeriesModel = $voucherSeriesModel;
    }

    public function getEshopModel(): EshopVoucherModel
    {
        return $this->voucherSeriesModel;
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
    public function title(): string
    {
        return (string) $this->getEshopModel()->getRawFieldData('OXSERIENR');
    }

    /**
     * @Field
     */
    public function description(): string
    {
        return (string) $this->getEshopModel()->getRawFieldData('OXSERIEDESCRIPTION');
    }

    /**
     * @Field
     */
    public function validFrom(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->getEshopModel()->getRawFieldData('OXBEGINDATE')
        );
    }

    /**
     * @Field
     */
    public function validTo(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->getEshopModel()->getRawFieldData('OXENDDATE')
        );
    }

    /**
     * @Field
     */
    public function discount(): float
    {
        return (float) $this->getEshopModel()->getRawFieldData('OXDISCOUNT');
    }

    /**
     * @Field
     */
    public function discountType(): string
    {
        return (string) $this->getEshopModel()->getRawFieldData('OXDISCOUNTTYPE');
    }

    public static function getModelClass(): string
    {
        return EshopVoucherModel::class;
    }
}
