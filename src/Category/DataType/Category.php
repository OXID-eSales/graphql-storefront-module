<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Category\DataType;

use DateTimeInterface;
use Exception;
use OxidEsales\Eshop\Application\Model\Category as CategoryModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Catalogue\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Category implements DataType
{
    /** @var CategoryModel */
    private $category;

    public function __construct(CategoryModel $category)
    {
        $this->category = $category;
    }

    public function getEshopModel(): CategoryModel
    {
        return $this->category;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID(
            $this->category->getId()
        );
    }

    public function getParentId(): ID
    {
        return new ID(
            (string) $this->category->getFieldData('oxparentid')
        );
    }

    public function getRootId(): ID
    {
        return new ID(
            (string) $this->category->getFieldData('oxrootid')
        );
    }

    /**
     * Defines the order in which categories are displayed:
     * The category with the lowest number is displayed at the top,
     * and the category with the highest number at the bottom
     *
     * @Field()
     */
    public function getPosition(): int
    {
        return (int) $this->category->getFieldData('oxsort');
    }

    /**
     * @Field()
     */
    public function isActive(?DateTimeInterface $now = null): bool
    {
        $active = (bool) $this->category->getFieldData('oxactive');

        if ($active) {
            return true;
        }

        $from = DateTimeImmutableFactory::fromString(
            (string) $this->category->getFieldData('oxactivefrom')
        );
        $to = DateTimeImmutableFactory::fromString(
            (string) $this->category->getFieldData('oxactiveto')
        );
        $now = $now ?? DateTimeImmutableFactory::fromString('now');

        if ($from <= $now && $to >= $now) {
            return true;
        }

        return false;
    }

    /**
     * Hidden categories are not visible in lists and menu,
     * but can be accessed by direct link
     *
     * @Field()
     */
    public function isHidden(): bool
    {
        return !$this->category->getIsVisible();
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return $this->category->getTitle();
    }

    /**
     * @Field()
     */
    public function getShortDescription(): string
    {
        return $this->category->getShortDescription();
    }

    /**
     * @Field()
     */
    public function getLongDescription(): string
    {
        return $this->category->getLongDesc();
    }

    /**
     * @Field()
     */
    public function getThumbnail(): ?string
    {
        return $this->category->getThumbUrl();
    }

    /**
     * If the external link is specified it will be opened instead of category content
     *
     * @Field()
     */
    public function getExternalLink(): string
    {
        return (string) $this->category->getFieldData('oxextlink');
    }

    /**
     * @Field()
     */
    public function getTemplate(): string
    {
        return (string) $this->category->getFieldData('oxtemplate');
    }

    /**
     * If specified, all products, with price higher than specified,
     * will be shown in this category
     *
     * @Field()
     */
    public function getPriceFrom(): float
    {
        return (float) $this->category->getFieldData('oxpricefrom');
    }

    /**
     * If specified, all products, with price lower than specified,
     * will be shown in this category
     *
     * @Field()
     */
    public function getPriceTo(): float
    {
        return (float) $this->category->getFieldData('oxpriceto');
    }

    /**
     * @Field()
     */
    public function getIcon(): ?string
    {
        return $this->category->getIconUrl();
    }

    /**
     * @Field()
     */
    public function getPromotionIcon(): ?string
    {
        return $this->category->getPromotionIconUrl();
    }

    /**
     * @Field()
     */
    public function getVat(): ?float
    {
        $vat = $this->category->getFieldData('oxvat');

        return null === $vat ? $vat : (float) $vat;
    }

    /**
     * Skip all negative discounts for products in this category
     * (Discounts, Vouchers, Delivery ...)
     *
     * @Field()
     */
    public function skipDiscount(): bool
    {
        return (bool) $this->category->getFieldData('oxskipdiscounts');
    }

    /**
     * @Field()
     */
    public function showSuffix(): bool
    {
        return (bool) $this->category->getFieldData('oxshowsuffix');
    }

    /**
     * @Field()
     *
     * @throws Exception
     */
    public function getTimestamp(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            $this->category->getFieldData('oxtimestamp')
        );
    }

    public function getDefSort(): ?string
    {
        return $this->category->getFieldData('oxdefsort');
    }

    public function getDefSortMode(): int
    {
        return (int) $this->category->getFieldData('oxdefsortmode');
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return CategoryModel::class;
    }
}
