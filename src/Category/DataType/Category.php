<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Category\DataType;

use DateTimeInterface;
use Exception;
use OxidEsales\Eshop\Application\Model\Category as CategoryModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Category implements ShopModelAwareInterface
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
            (string) $this->category->getRawFieldData('oxparentid')
        );
    }

    public function getRootId(): ID
    {
        return new ID(
            (string) $this->category->getRawFieldData('oxrootid')
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
        return (int) $this->category->getRawFieldData('oxsort');
    }

    /**
     * @Field()
     */
    public function isActive(?DateTimeInterface $now = null): bool
    {
        $active = (bool) $this->category->getRawFieldData('oxactive');

        if ($active) {
            return true;
        }

        $from = DateTimeImmutableFactory::fromString(
            (string) $this->category->getRawFieldData('oxactivefrom')
        );
        $to = DateTimeImmutableFactory::fromString(
            (string) $this->category->getRawFieldData('oxactiveto')
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
        return (string) $this->category->getRawFieldData('oxextlink');
    }

    /**
     * @Field()
     */
    public function getTemplate(): string
    {
        return (string) $this->category->getRawFieldData('oxtemplate');
    }

    /**
     * If specified, all products, with price higher than specified,
     * will be shown in this category
     *
     * @Field()
     */
    public function getPriceFrom(): float
    {
        return (float) $this->category->getRawFieldData('oxpricefrom');
    }

    /**
     * If specified, all products, with price lower than specified,
     * will be shown in this category
     *
     * @Field()
     */
    public function getPriceTo(): float
    {
        return (float) $this->category->getRawFieldData('oxpriceto');
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
        $vat = $this->category->getRawFieldData('oxvat');

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
        return (bool) $this->category->getRawFieldData('oxskipdiscounts');
    }

    /**
     * @Field()
     */
    public function showSuffix(): bool
    {
        return (bool) $this->category->getRawFieldData('oxshowsuffix');
    }

    /**
     * @Field()
     *
     * @throws Exception
     */
    public function getTimestamp(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            $this->category->getRawFieldData('oxtimestamp')
        );
    }

    public function getDefSort(): ?string
    {
        return $this->category->getRawFieldData('oxdefsort');
    }

    public function getDefSortMode(): int
    {
        return (int) $this->category->getRawFieldData('oxdefsortmode');
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return CategoryModel::class;
    }
}
