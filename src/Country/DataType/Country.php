<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Country\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\Country as EshopCountryModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Country implements ShopModelAwareInterface
{
    /** @var EshopCountryModel */
    private $country;

    public function __construct(EshopCountryModel $country)
    {
        $this->country = $country;
    }

    public function getEshopModel(): EshopCountryModel
    {
        return $this->country;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->country->getId());
    }

    /**
     * @Field()
     */
    public function getPosition(): int
    {
        return (int) $this->country->getFieldData('oxorder');
    }

    /**
     * @Field()
     */
    public function isActive(): bool
    {
        return (bool) $this->country->getFieldData('oxactive');
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return (string) $this->country->getFieldData('oxtitle');
    }

    /**
     * @Field()
     */
    public function getIsoAlpha2(): string
    {
        return (string) $this->country->getFieldData('oxisoalpha2');
    }

    /**
     * @Field()
     */
    public function getIsoAlpha3(): string
    {
        return (string) $this->country->getFieldData('oxisoalpha3');
    }

    /**
     * @Field()
     */
    public function getIsoNumeric(): string
    {
        return (string) $this->country->getFieldData('oxunnum3');
    }

    /**
     * @Field()
     */
    public function getShortDescription(): string
    {
        return $this->country->getFieldData('oxshortdesc');
    }

    /**
     * @Field()
     */
    public function getDescription(): string
    {
        return $this->country->getFieldData('oxlongdesc');
    }

    /**
     * @Field()
     */
    public function getCreationDate(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString((string) $this->country->getFieldData('oxtimestamp'));
    }

    public static function getModelClass(): string
    {
        return EshopCountryModel::class;
    }
}
