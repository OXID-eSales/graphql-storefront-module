<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\Address as EshopAddressModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class DeliveryAddress extends AbstractAddress
{
    private EshopAddressModel $address;

    public function __construct(EshopAddressModel $address)
    {
        $this->address = $address;
        parent::__construct('ox');
    }

    public function getEshopModel(): EshopAddressModel
    {
        return $this->address;
    }

    /**
     * @Field()
     */
    public function id(): ID
    {
        return new ID(
            $this->address->getId()
        );
    }

    /**
     * @Field()
     */
    public function updated(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString($this->getFieldValue('timestamp'));
    }

    public function userId(): ID
    {
        return new ID($this->getFieldValue('userid'));
    }

    public static function getModelClass(): string
    {
        return EshopAddressModel::class;
    }
}
