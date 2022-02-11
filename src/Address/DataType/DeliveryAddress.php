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
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class DeliveryAddress implements AddressInterface, ShopModelAwareInterface
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
            $this->address->getRawFieldData('oxcountryid')
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
        return (string) $this->address->getRawFieldData('oxsal');
    }

    /**
     * @Field()
     */
    public function firstName(): string
    {
        return (string) $this->address->getRawFieldData('oxfname');
    }

    /**
     * @Field()
     */
    public function lastName(): string
    {
        return (string) $this->address->getRawFieldData('oxlname');
    }

    /**
     * @Field()
     */
    public function company(): string
    {
        return (string) $this->address->getRawFieldData('oxcompany');
    }

    /**
     * @Field()
     */
    public function additionalInfo(): string
    {
        return (string) $this->address->getRawFieldData('oxaddinfo');
    }

    /**
     * @Field()
     */
    public function street(): string
    {
        return (string) $this->address->getRawFieldData('oxstreet');
    }

    /**
     * @Field()
     */
    public function streetNumber(): string
    {
        return (string) $this->address->getRawFieldData('oxstreetnr');
    }

    /**
     * @Field()
     */
    public function zipCode(): string
    {
        return (string) $this->address->getRawFieldData('oxzip');
    }

    /**
     * @Field()
     */
    public function city(): string
    {
        return (string) $this->address->getRawFieldData('oxcity');
    }

    /**
     * @Field()
     */
    public function phone(): string
    {
        return (string) $this->address->getRawFieldData('oxfon');
    }

    /**
     * @Field()
     */
    public function fax(): string
    {
        return (string) $this->address->getRawFieldData('oxfax');
    }

    /**
     * @Field()
     */
    public function updated(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->address->getRawFieldData('oxtimestamp')
        );
    }

    public function userId(): ID
    {
        return new ID(
            (string) $this->address->getRawFieldData('oxuserid')
        );
    }

    public function stateId(): ID
    {
        return new ID(
            $this->address->getRawFieldData('oxstateid')
        );
    }

    public static function getModelClass(): string
    {
        return EshopAddressModel::class;
    }
}
