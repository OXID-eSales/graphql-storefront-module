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
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class DeliveryAddress implements DataType
{
    /** @var EshopAddressModel */
    private $address;

    public function __construct(EshopAddressModel $address)
    {
        $this->address = $address;
    }

    public function getEshopModel(): EshopAddressModel
    {
        return $this->address;
    }

    public function countryId(): ID
    {
        return new ID(
            $this->address->getFieldData('oxcountryid')
        );
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
    public function salutation(): string
    {
        return (string) $this->address->getFieldData('oxsal');
    }

    /**
     * @Field()
     */
    public function firstName(): string
    {
        return (string) $this->address->getFieldData('oxfname');
    }

    /**
     * @Field()
     */
    public function lastName(): string
    {
        return (string) $this->address->getFieldData('oxlname');
    }

    /**
     * @Field()
     */
    public function company(): string
    {
        return (string) $this->address->getFieldData('oxcompany');
    }

    /**
     * @Field()
     */
    public function additionalInfo(): string
    {
        return (string) $this->address->getFieldData('oxaddinfo');
    }

    /**
     * @Field()
     */
    public function street(): string
    {
        return (string) $this->address->getFieldData('oxstreet');
    }

    /**
     * @Field()
     */
    public function streetNumber(): string
    {
        return (string) $this->address->getFieldData('oxstreetnr');
    }

    /**
     * @Field()
     */
    public function zipCode(): string
    {
        return (string) $this->address->getFieldData('oxzip');
    }

    /**
     * @Field()
     */
    public function city(): string
    {
        return (string) $this->address->getFieldData('oxcity');
    }

    /**
     * @Field()
     */
    public function phone(): string
    {
        return (string) $this->address->getFieldData('oxfon');
    }

    /**
     * @Field()
     */
    public function fax(): string
    {
        return (string) $this->address->getFieldData('oxfax');
    }

    /**
     * @Field()
     */
    public function updated(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->address->getFieldData('oxtimestamp')
        );
    }

    public function userId(): ID
    {
        return new ID(
            (string) $this->address->getFieldData('oxuserid')
        );
    }

    public function stateId(): ID
    {
        return new ID(
            $this->address->getFieldData('oxstateid')
        );
    }

    public static function getModelClass(): string
    {
        return EshopAddressModel::class;
    }
}
