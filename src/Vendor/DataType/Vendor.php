<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Vendor\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\Vendor as VendorModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Vendor implements ShopModelAwareInterface
{
    /** @var VendorModel */
    private $vendor;

    public function __construct(
        VendorModel $vendor
    ) {
        $this->vendor = $vendor;
    }

    public function getEshopModel(): VendorModel
    {
        return $this->vendor;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->vendor->getId());
    }

    /**
     * @Field()
     */
    public function isActive(): bool
    {
        return (bool)$this->vendor->getRawFieldData('oxactive');
    }

    /**
     * @Field()
     */
    public function getIcon(): ?string
    {
        return $this->vendor->getIconUrl();
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return $this->vendor->getTitle();
    }

    /**
     * @Field()
     */
    public function getShortdesc(): string
    {
        return $this->vendor->getShortDescription();
    }

    /**
     * @Field()
     */
    public function getTimestamp(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString((string)$this->vendor->getRawFieldData('oxtimestamp'));
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return VendorModel::class;
    }
}
