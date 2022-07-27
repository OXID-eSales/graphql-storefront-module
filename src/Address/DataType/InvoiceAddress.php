<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class InvoiceAddress extends AbstractAddress
{
    protected const PHONE_FIELD_NAME = 'privfon';
    private EshopUserModel $customer;

    public function __construct(EshopUserModel $customer)
    {
        $this->customer = $customer;
        parent::__construct('ox');
    }

    public function getEshopModel(): EshopUserModel
    {
        return $this->customer;
    }

    /**
     * @Field()
     */
    public function vatID(): string
    {
        return $this->getFieldValue('ustid');
    }

    /**
     * @Field()
     */
    public function mobile(): string
    {
        return $this->getFieldValue('mobfon');
    }

    /**
     * @Field()
     */
    public function created(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            $this->getFieldValue('create')
        );
    }

    /**
     * @Field()
     */
    public function updated(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            $this->getFieldValue('timestamp')
        );
    }

    public static function getModelClass(): string
    {
        return EshopUserModel::class;
    }
}
