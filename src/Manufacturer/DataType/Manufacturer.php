<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Manufacturer\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\Manufacturer as ManufacturerModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Manufacturer implements DataType
{
    /** @var ManufacturerModel */
    private $manufacturer;

    public function __construct(
        ManufacturerModel $manufacturer
    ) {
        $this->manufacturer = $manufacturer;
    }

    public function getEshopModel(): ManufacturerModel
    {
        return $this->manufacturer;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->manufacturer->getId());
    }

    /**
     * @Field()
     */
    public function isActive(): bool
    {
        return (bool) $this->manufacturer->getRawFieldData('oxactive');
    }

    /**
     * @Field()
     */
    public function getIcon(): ?string
    {
        return $this->manufacturer->getIconUrl();
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return $this->manufacturer->getTitle();
    }

    /**
     * @Field()
     */
    public function getShortdesc(): string
    {
        return $this->manufacturer->getShortDescription();
    }

    /**
     * @Field()
     */
    public function getTimestamp(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString((string) $this->manufacturer->getRawFieldData('oxtimestamp'));
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return ManufacturerModel::class;
    }
}
