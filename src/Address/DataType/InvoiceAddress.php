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
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class InvoiceAddress implements ShopModelAwareInterface
{
    /** @var EshopUserModel */
    private $customer;

    public function __construct(EshopUserModel $customer)
    {
        $this->customer = $customer;
    }

    public function getEshopModel(): EshopUserModel
    {
        return $this->customer;
    }

    /**
     * @Field()
     */
    public function salutation(): string
    {
        return (string) $this->customer->getRawFieldData('oxsal');
    }

    /**
     * @Field()
     */
    public function firstName(): string
    {
        return (string) $this->customer->getRawFieldData('oxfname');
    }

    /**
     * @Field()
     */
    public function lastName(): string
    {
        return (string) $this->customer->getRawFieldData('oxlname');
    }

    /**
     * @Field()
     */
    public function company(): string
    {
        return (string) $this->customer->getRawFieldData('oxcompany');
    }

    /**
     * @Field()
     */
    public function additionalInfo(): string
    {
        return (string) $this->customer->getRawFieldData('oxaddinfo');
    }

    /**
     * @Field()
     */
    public function street(): string
    {
        return (string) $this->customer->getRawFieldData('oxstreet');
    }

    /**
     * @Field()
     */
    public function streetNumber(): string
    {
        return (string) $this->customer->getRawFieldData('oxstreetnr');
    }

    /**
     * @Field()
     */
    public function zipCode(): string
    {
        return (string) $this->customer->getRawFieldData('oxzip');
    }

    /**
     * @Field()
     */
    public function city(): string
    {
        return (string) $this->customer->getRawFieldData('oxcity');
    }

    /**
     * @Field()
     */
    public function vatID(): string
    {
        return (string) $this->customer->getRawFieldData('oxustid');
    }

    /**
     * @Field()
     */
    public function phone(): string
    {
        return (string) $this->customer->getRawFieldData('oxprivphone');
    }

    /**
     * @Field()
     */
    public function mobile(): string
    {
        return (string) $this->customer->getRawFieldData('oxmobfone');
    }

    /**
     * @Field()
     */
    public function fax(): string
    {
        return (string) $this->customer->getRawFieldData('oxfax');
    }

    /**
     * @Field()
     */
    public function created(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->customer->getRawFieldData('oxcreate')
        );
    }

    /**
     * @Field()
     */
    public function updated(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->customer->getRawFieldData('oxtimestamp')
        );
    }

    public function countryId(): ID
    {
        return new ID(
            $this->customer->getRawFieldData('oxcountryid')
        );
    }

    public function stateId(): ID
    {
        return new ID(
            $this->customer->getRawFieldData('oxstateid')
        );
    }

    public static function getModelClass(): string
    {
        return EshopUserModel::class;
    }
}
