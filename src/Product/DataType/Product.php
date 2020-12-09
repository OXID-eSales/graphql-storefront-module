<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Product\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\Article as EshopProductModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Catalogue\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

use function array_filter;
use function explode;

/**
 * @Type()
 */
final class Product implements DataType
{
    /** @var EshopProductModel */
    private $product;

    public function __construct(
        EshopProductModel $product
    ) {
        $this->product = $product;
    }

    public function getEshopModel(): EshopProductModel
    {
        return $this->product;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->product->getId());
    }

    /**
     * @Field()
     */
    public function isActive(): bool
    {
        return $this->product->isVisible();
    }

    /**
     * @Field()
     */
    public function getSku(): ?string
    {
        return (string) $this->product->getFieldData('oxartnum');
    }

    /**
     * @Field()
     */
    public function getEan(): string
    {
        return (string) $this->product->getFieldData('oxean');
    }

    /**
     * @Field()
     */
    public function getManufacturerEan(): string
    {
        return (string) $this->product->getFieldData('oxdistean');
    }

    /**
     * @Field()
     */
    public function getMpn(): string
    {
        return (string) $this->product->getFieldData('oxmpn');
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return (string) $this->product->getFieldData('oxtitle');
    }

    /**
     * @Field()
     */
    public function getShortDescription(): string
    {
        return (string) $this->product->getFieldData('oxshortdesc');
    }

    /**
     * @Field()
     */
    public function getLongDescription(): string
    {
        return (string) $this->product->getLongDesc();
    }

    /**
     * @Field()
     */
    public function getVat(): float
    {
        return (float) $this->product->getArticleVat();
    }

    /**
     * @Field()
     */
    public function getInsert(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->product->getFieldData('oxinsert')
        );
    }

    /**
     * @Field()
     */
    public function isFreeShipping(): bool
    {
        return (bool) $this->product->getFieldData('oxfreeshipping');
    }

    /**
     * @Field()
     */
    public function getTimestamp(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->product->getFieldData('oxtimestamp')
        );
    }

    /**
     * @Field()
     *
     * @return string[]
     */
    public function getVariantLabels(): array
    {
        return array_filter(
            explode(
                ' | ',
                (string) $this->product->getFieldData('oxvarname')
            )
        );
    }

    /**
     * @Field()
     *
     * @return string[]
     */
    public function getVariantValues(): array
    {
        return array_filter(
            explode(
                ' | ',
                (string) $this->product->getFieldData('oxvarselect')
            )
        );
    }

    /**
     * @Field()
     */
    public function wishedPriceEnabled(): bool
    {
        return !(bool) $this->product->getFieldData('oxblfixedprice');
    }

    /**
     * @Field()
     */
    public function getVarMinPrice(): float
    {
        return (float) $this->product->getFieldData('oxvarminprice');
    }

    public function getBundleId(): string
    {
        return (string) $this->product->getFieldData('oxbundleid');
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return EshopProductModel::class;
    }
}
